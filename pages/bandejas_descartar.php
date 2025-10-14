<?php
// /pages/bandejas_descartar.php
$page_title = 'Bandejas a Botar';
require_once('../config/load.php');
page_require_level(2); // seguridad (ajusta el nivel si hace falta)

/* ─────────────────────────────────────────────────────────────
   Helpers
────────────────────────────────────────────────────────────── */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/**
 * OJO: $db es tu wrapper MySqli_DB (no mysqli nativo), así que
 * no tipeamos como mysqli aquí. Solo exigimos que tenga ->query().
 */
function colExiste($db, string $tabla, string $col): bool {
  $q = $db->query("SHOW COLUMNS FROM `{$tabla}` LIKE '{$col}'");
  return $q && $q->num_rows > 0;
}

/* Columnas candidatas a "bandeja" */
$POSIBLES_BANDEJAS = [
  'Tare_Name',
  'Container','Container1','Container2','Container3','Container4','Container5','Container6',
  'LL_Container_1','LL_Container_2','LL_Container_3',
  'PL_Container_1','PL_Container_2','PL_Container_3',
  'TareMc','Tare_Name_MC_Before','Tare_Name_MC_After'
];

/* Whitelist de tablas con muestras físicas (EDITA ESTA LISTA A TU ESQUEMA) */
$TABLAS_WHITELIST = [
  'atterberg_limit',
  'hydrometer',
  'moisture_oven',
   'moisture_microwave',
    'moisture_scale',
     'moisture_constant_mass',
  'grain_size_general',
  'grain_size_coarse',
  'grain_size_fine',
  'grain_size_lpf',
  'grain_size_upstream_transition_fill',
  'standard_proctor',
  'specific_gravity_coarse',
  'specific_gravity_fine',
];

/* ─────────────────────────────────────────────────────────────
   POST: Marcar seleccionadas como descartadas
────────────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descartar']) && !empty($_POST['items'])) {
  $ok = 0; $fail = 0;

  foreach ($_POST['items'] as $packed) {
    $parts = explode('|', $packed, 2);
    if (count($parts) !== 2) { $fail++; continue; }
    [$tabla, $id] = $parts;

    if (!in_array($tabla, $TABLAS_WHITELIST, true)) { $fail++; continue; }
    $idEsc = (int)$id;
    if ($idEsc <= 0) { $fail++; continue; }

    if (colExiste($db, $tabla, 'discarded')) {
      $q = "UPDATE `{$tabla}` SET `discarded`=1 WHERE `id`={$idEsc} LIMIT 1";
    } elseif (colExiste($db, $tabla, 'Discarded_At')) {
      $q = "UPDATE `{$tabla}` SET `Discarded_At`=NOW() WHERE `id`={$idEsc} LIMIT 1";
    } else {
      $q = null; // no hay campo de descarte; omite
    }

    if ($q) {
      $res = $db->query($q);
      $res ? $ok++ : $fail++;
    }
  }

  if ($ok > 0)  $session->msg('s', "Se marcaron {$ok} registro(s) como descartados.");
  if ($fail> 0) $session->msg('d', "No se pudo marcar {$fail} registro(s).");
  redirect('bandejas_descartar.php'); // evita re-envío
}

/* ─────────────────────────────────────────────────────────────
   GET: Filtros (rango de fechas + búsqueda)
────────────────────────────────────────────────────────────── */
$hoy       = date('Y-m-d');
$desde_def = date('Y-m-d', strtotime('-7 days'));

$desde = isset($_GET['desde']) && $_GET['desde'] !== '' ? $_GET['desde'] : $desde_def;
$hasta = isset($_GET['hasta']) && $_GET['hasta'] !== '' ? $_GET['hasta'] : $hoy;
$busca = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) $desde = $desde_def;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) $hasta = $hoy;

$desdeDT = $db->escape($desde.' 00:00:00');
$hastaDT = $db->escape($hasta.' 23:59:59');

/* ─────────────────────────────────────────────────────────────
   Recolección de muestras con bandeja
────────────────────────────────────────────────────────────── */
$muestras = [];

foreach ($TABLAS_WHITELIST as $tabla) {
  if (!colExiste($db, $tabla, 'Test_Start_Date')) continue;

  $cols = ["id","Sample_ID","Sample_Number","test_type"];
  if (colExiste($db, $tabla, 'Material_Type')) $cols[] = "Material_Type";

  $colBandeja = null;
  foreach ($POSIBLES_BANDEJAS as $c) {
    if (colExiste($db, $tabla, $c)) { $colBandeja = $c; break; }
  }
  if ($colBandeja) $cols[] = "`{$colBandeja}` AS Bandeja";

  $colsSQL = implode(', ', array_map(function($c){
    return (str_contains($c, '`') || str_contains($c, ' AS ')) ? $c : "`{$c}`";
  }, $cols));

  $q = "SELECT {$colsSQL} FROM `{$tabla}` WHERE `Test_Start_Date` BETWEEN '{$desdeDT}' AND '{$hastaDT}'";

  if (colExiste($db, $tabla, 'discarded')) {
    $q .= " AND (`discarded` IS NULL OR `discarded`=0)";
  }

  if ($busca !== '') {
    $b = $db->escape('%'.$busca.'%');
    $q .= " AND (Sample_ID LIKE '{$b}' OR Sample_Number LIKE '{$b}' OR test_type LIKE '{$b}')";
  }

  $res = $db->query($q);
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      if ($colBandeja && empty($row['Bandeja'])) continue;
      $row['tabla'] = $tabla;
      $muestras[] = $row;
    }
    if (method_exists($res, 'free')) { $res->free(); }
  } else {
    error_log("Query bandejas fallo en {$tabla}: ".$db->error);
  }
}

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
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item active">Bandejas a Botar</li>
      </ol>
    </nav>
  </div>

  <div class="container-fluid">
    <?php echo display_msg($msg); ?>

    <div class="card shadow-sm">
      <div class="card-body p-4">

        <!-- Filtros -->
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

        <!-- Acciones -->
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
            <a href="../pdf/exportar_muestra_botar.php?desde=<?= h($desde) ?>&hasta=<?= h($hasta) ?>&q=<?= urlencode($busca) ?>"
               target="_blank" class="btn btn-outline-danger btn-sm">
              <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
            </a>
          </div>
        </div>

        <!-- Tabla -->
        <form method="post" id="formDescartar" onsubmit="return confirm('¿Marcar las bandejas seleccionadas como descartadas?');">
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaBandejas">
              <thead class="table-light">
                <tr>
                  <th style="width:40px;">
                    <input type="checkbox" id="chkAll">
                  </th>
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
                        <input class="form-check-input row-check" type="checkbox" name="items[]" value="<?= h($m['tabla'].'|'.$m['id']) ?>">
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

<!-- UI: seleccionar/limpiar + master checkbox -->
<script>
(function(){
  const chkAll = document.getElementById('chkAll');
  const rowChecks = () => document.querySelectorAll('.row-check');

  if (chkAll) {
    chkAll.addEventListener('change', function() {
      rowChecks().forEach(ch => ch.checked = chkAll.checked);
    });
  }

  const btnSelectAll = document.getElementById('selectAll');
  const btnClearAll  = document.getElementById('clearAll');

  if (btnSelectAll) btnSelectAll.addEventListener('click', () => {
    rowChecks().forEach(ch => ch.checked = true);
    if (chkAll) chkAll.checked = true;
  });

  if (btnClearAll) btnClearAll.addEventListener('click', () => {
    rowChecks().forEach(ch => ch.checked = false);
    if (chkAll) chkAll.checked = false;
  });
})();
</script>

<?php include_once('../components/footer.php'); ?>
