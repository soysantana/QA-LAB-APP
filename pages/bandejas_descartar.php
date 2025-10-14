<?php
// bandejas_descartar.php
$page_title = 'Bandejas a Botar';
require_once('../config/load.php');
page_require_level(2); // seguridad básica

// ─────────────────────────────────────────────────────────────────────────────
// Utilitarios
// ─────────────────────────────────────────────────────────────────────────────
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function colExiste($db, $tabla, $col) {
  $q = $db->query("SHOW COLUMNS FROM `{$tabla}` LIKE '{$col}'");
  return $q && $q->num_rows > 0;
}

// Columnas candidatas para “bandeja”
$POSIBLES_BANDEJAS = [
  'Tare_Name',
  'Container','Container1','Container2','Container3','Container4','Container5','Container6',
  'LL_Container_1','LL_Container_2','LL_Container_3',
  'PL_Container_1','PL_Container_2','PL_Container_3',
  'TareMc','Tare_Name_MC_Before','Tare_Name_MC_After'
];

// ─────────────────────────────────────────────────────────────────────────────
// Manejo POST: marcar como descartadas
// ─────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descartar']) && !empty($_POST['items'])) {
  $ok = 0; $fail = 0;
  foreach ($_POST['items'] as $packed) {
    // packed = table|id
    [$tabla, $id] = explode('|', $packed, 2);
    $tablaEsc = preg_replace('/[^a-zA-Z0-9_\-]/', '', $tabla);
    $idEsc = (int)$id;

    if (!$tablaEsc || !$idEsc) { $fail++; continue; }

    // si la tabla tiene 'discarded', marcamos 1; sinó, intentamos borrar la bandeja (opcional)
    if (colExiste($db, $tablaEsc, 'discarded')) {
      $q = "UPDATE `{$tablaEsc}` SET `discarded`=1 WHERE `id`={$idEsc} LIMIT 1";
    } else {
      // opción alternativa si no hay 'discarded': solo registrar fecha de descarte si existe
      if (colExiste($db, $tablaEsc, 'Discarded_At')) {
        $q = "UPDATE `{$tablaEsc}` SET `Discarded_At`=NOW() WHERE `id`={$idEsc} LIMIT 1";
      } else {
        // si no hay ninguna de las dos, igual no fallamos: simplemente no hacemos nada destructivo
        $q = null;
      }
    }

    if ($q) {
      $res = $db->query($q);
      $res ? $ok++ : $fail++;
    }
  }

  if ($ok > 0)  $session->msg('s', "Se marcaron {$ok} registro(s) como descartados.");
  if ($fail> 0) $session->msg('d', "No se pudo marcar {$fail} registro(s).");
  redirect('bandejas_descartar.php'); // evitar repost
}

// ─────────────────────────────────────────────────────────────────────────────
// Filtros GET: rango de fechas + búsqueda
// ─────────────────────────────────────────────────────────────────────────────
$hoy       = date('Y-m-d');
$desde_def = date('Y-m-d', strtotime('-7 days'));

$desde = isset($_GET['desde']) && $_GET['desde'] !== '' ? $_GET['desde'] : $desde_def;
$hasta = isset($_GET['hasta']) && $_GET['hasta'] !== '' ? $_GET['hasta'] : $hoy;
$busca = isset($_GET['q']) ? trim($_GET['q']) : '';

// Validar fechas simples
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) $desde = $desde_def;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) $hasta = $hoy;

// Rango datetime
$desdeDT = $db->escape($desde.' 00:00:00');
$hastaDT = $db->escape($hasta.' 23:59:59');

// ─────────────────────────────────────────────────────────────────────────────
// Recolección de muestras con bandeja
// ─────────────────────────────────────────────────────────────────────────────

// (Opción A) Descubrir todas las tablas del esquema
$tablas = [];
$tablas = [
  'atterberg_limit','hydrometer','moisture_content','grain_size_general',
  'grain_size_coarse','grain_size_fine','grain_size_lpf','grain_size_upstream_transition_fill',
  // añade las que uses
];

while ($r = $resTab->fetch_row()) { $tablas[] = $r[0]; }

// (Opción B) Mejor performance (whitelist). Descomenta y comenta la opción A si ya conoces tus tablas:
// $tablas = [
//   'atterberg_limit','hydrometer','moisture_content','grain_size_general',
//   'grain_size_coarse','grain_size_fine','grain_size_lpf','grain_size_upstream_transition_fill',
//   'standard_proctor','specific_gravity_fine_aggregate','specific_gravity_coarse_aggregates',
// ];

$muestras = [];
foreach ($tablas as $tabla) {
  // Debe tener fecha de inicio
  if (!colExiste($db, $tabla, 'Test_Start_Date')) continue;

  // Columnas mínimas a traer
  $cols = ["id","Sample_ID","Sample_Number","test_type"];
  if (colExiste($db, $tabla, 'Material_Type')) $cols[] = "Material_Type";

  // Detectar columna de bandeja
  $colBandeja = null;
  foreach ($POSIBLES_BANDEJAS as $c) {
    if (colExiste($db, $tabla, $c)) { $colBandeja = $c; break; }
  }
  if ($colBandeja) $cols[] = "`{$colBandeja}` AS Bandeja";

  // Construir SELECT
  $colsSQL = implode(', ', array_map(function($c){
    return (str_contains($c, '`') || str_contains($c, ' AS ')) ? $c : "`{$c}`";
  }, $cols));

  $q = "SELECT {$colsSQL} FROM `{$tabla}` WHERE `Test_Start_Date` BETWEEN '{$desdeDT}' AND '{$hastaDT}'";

  // Excluir descartados si existe la columna
  if (colExiste($db, $tabla, 'discarded')) {
    $q .= " AND (`discarded` IS NULL OR `discarded`=0)";
  }

  // Búsqueda básica si hay término
  if ($busca !== '') {
    $b = $db->escape('%'.$busca.'%');
    $q .= " AND (Sample_ID LIKE '{$b}' OR Sample_Number LIKE '{$b}' OR test_type LIKE '{$b}')";
  }

  $res = $db->query($q);
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      // Solo interesan filas donde haya info de bandeja (si se detectó)
      if ($colBandeja && empty($row['Bandeja'])) continue;
      $row['tabla'] = $tabla;
      $muestras[] = $row;
    }
  }
}

// Orden simple (por Sample_ID, Sample_Number)
usort($muestras, function($a,$b){
  return [$a['Sample_ID'],$a['Sample_Number']] <=> [$b['Sample_ID'],$b['Sample_Number']];
});

include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Bandejas a Botar</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../pages/home.php">Home</a></li>
        <li class="breadcrumb-item active">Bandejas a Botar</li>
      </ol>
    </nav>
  </div>

  <div class="container-fluid">
    <?php echo display_msg($msg); ?>

    <div class="card shadow-sm">
      <div class="card-body p-4">
        <form class="row g-3 align-items-end mb-3" method="get" action="">
          <div class="col-md-3">
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="<?= h($desde) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="<?= h($hasta) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Buscar</label>
            <input type="text" name="q" class="form-control" placeholder="Muestra, número o tipo…" value="<?= h($busca) ?>">
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
          </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex gap-2">
            <button type="button" id="selectAll" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-check2-square"></i> Seleccionar todo
            </button>
            <button type="button" id="clearAll" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-x-square"></i> Limpiar selección
            </button>
          </div>
          <div class="d-flex gap-2">
            <a href="../pdf/exportar_muestra_botar.php?desde=<?= h($desde) ?>&hasta=<?= h($hasta) ?>&q=<?= urlencode($busca) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
              <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
            </a>
          </div>
        </div>

        <form method="post" id="formDescartar" onsubmit="return confirm('¿Marcar las bandejas seleccionadas como descartadas?');">
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaBandejas">
              <thead class="table-light">
                <tr>
                  <th style="width:40px;"></th>
                  <th>Sample</th>
                  <th>Número</th>
                  <th>Material</th>
                  <th>Ensayo</th>
                  <th>Bandeja</th>
                  <th class="text-muted" style="width: 160px;">Origen</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($muestras)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-success py-4">✅ No hay bandejas a botar en el rango seleccionado.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($muestras as $m): ?>
                    <tr>
                      <td>
                        <input class="form-check-input" type="checkbox" name="items[]" value="<?= h($m['tabla'].'|'.$m['id']) ?>">
                      </td>
                      <td><span class="fw-semibold"><?= h($m['Sample_ID'] ?? '-') ?></span></td>
                      <td><?= h($m['Sample_Number'] ?? '-') ?></td>
                      <td><span class="badge bg-secondary"><?= h($m['Material_Type'] ?? 'N/D') ?></span></td>
                      <td><code><?= h($m['test_type'] ?? '-') ?></code></td>
                      <td><?= h($m['Bandeja'] ?? 'N/A') ?></td>
                      <td class="text-muted"><small><?= h($m['tabla']) ?></small></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
              <small>Total: <?= count($muestras) ?> registro(s)</small>
            </div>
            <button class="btn btn-danger" name="descartar" value="1">
              <i class="bi bi-trash"></i> Marcar como descartadas
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</main>

<!-- Interacciones UI -->
<script>
  // seleccionar/limpiar
  (function() {
    const selectAll = document.getElementById('selectAll');
    const clearAll  = document.getElementById('clearAll');
    const checkboxes = () => document.querySelectorAll('input[type="checkbox"][name="items[]"]');

    if (selectAll) selectAll.addEventListener('click', () => {
      checkboxes().forEach(ch => ch.checked = true);
    });

    if (clearAll) clearAll.addEventListener('click', () => {
      checkboxes().forEach(ch => ch.checked = false);
    });
  })();
</script>

<?php include_once('../components/footer.php'); ?>
