<?php
// /pages/bandejas_descartar.php (versión robusta con fallback de columnas)
$page_title = 'Bandejas a Botar';
require_once('../config/load.php');
page_require_level(2);

// ─────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// columnas candidatas para “bandeja”
$POSIBLES_BANDEJAS = [
  'Tare_Name',
  'Container','Container1','Container2','Container3','Container4','Container5','Container6',
  'LL_Container_1','LL_Container_2','LL_Container_3',
  'PL_Container_1','PL_Container_2','PL_Container_3',
  'TareMc','Tare_Name_MC_Before','Tare_Name_MC_After'
];

/**
 * CONFIGURACIÓN POR TABLA (EDITA PARA TU SERVIDOR)
 * - name: nombre exacto de la tabla (ojo con mayúsculas en Linux)
 * - date_col: columna de fecha para el rango (obligatoria)
 * - discard_col: pon 'discarded' SOLO si esa tabla realmente tiene esa columna. Si no, deja null.
 * - discarded_at_col: alternativa si tu tabla guarda fecha de descarte (si no, null).
 * - bandeja_cols: deja la lista global o personalízala por tabla.
 */
$TABLES = [
  ['name'=>'atterberg_limit',                        'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'hydrometer',                             'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'moisture_content',                       'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'grain_size_general',                     'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'grain_size_coarse',                      'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'grain_size_fine',                        'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'grain_size_lpf',                         'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'grain_size_upstream_transition_fill',    'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'standard_proctor',                       'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'specific_gravity_fine_aggregate',        'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
  ['name'=>'specific_gravity_coarse_aggregates',     'date_col'=>'Test_Start_Date', 'discard_col'=>null,         'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],
];
// EJEMPLO si sabes que una tabla sí tiene discarded:
// ['name'=>'moisture_content', 'date_col'=>'Test_Start_Date', 'discard_col'=>'discarded', 'discarded_at_col'=>null, 'bandeja_cols'=>$POSIBLES_BANDEJAS],

// ─────────────────────────────────────────────────────────────
// POST: marcar seleccionadas como descartadas
// ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descartar']) && !empty($_POST['items'])) {
  $ok=0; $fail=0;
  $byName=[]; foreach($TABLES as $t){ $byName[$t['name']]=$t; }

  foreach($_POST['items'] as $packed){
    $parts = explode('|',$packed,2);
    if(count($parts)!==2){ $fail++; continue; }
    [$tabla,$id] = $parts;
    if(!isset($byName[$tabla])){ $fail++; continue; }
    $cfg = $byName[$tabla];
    $idEsc = (int)$id;
    if($idEsc<=0){ $fail++; continue; }

    $q=null;
    if(!empty($cfg['discard_col'])){
      $col=$cfg['discard_col'];
      $q="UPDATE `{$tabla}` SET `{$col}`=1 WHERE `id`={$idEsc} LIMIT 1";
    }elseif(!empty($cfg['discarded_at_col'])){
      $col=$cfg['discarded_at_col'];
      $q="UPDATE `{$tabla}` SET `{$col}`=NOW() WHERE `id`={$idEsc} LIMIT 1";
    }
    if($q){
      try{
        $res=$db->query($q);
        $res?$ok++:$fail++;
      }catch(Throwable $e){
        $fail++;
        error_log("DESCARTAR fallo en {$tabla}: ".$e->getMessage());
      }
    }
  }

  if($ok>0)  $session->msg('s',"Se marcaron {$ok} registro(s) como descartados.");
  if($fail>0) $session->msg('d',"No se pudo marcar {$fail} registro(s).");
  redirect('bandejas_descartar.php'); exit;
}

// ─────────────────────────────────────────────────────────────
// GET: filtros
// ─────────────────────────────────────────────────────────────
$hoy=date('Y-m-d'); $desde_def=date('Y-m-d',strtotime('-7 days'));
$desde = ($_GET['desde']??'') ?: $desde_def;
$hasta = ($_GET['hasta']??'') ?: $hoy;
$busca = trim($_GET['q']??'');

if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$desde)) $desde=$desde_def;
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$hasta)) $hasta=$hoy;

$desdeDT=$db->escape($desde.' 00:00:00');
$hastaDT=$db->escape($hasta.' 23:59:59');

// ─────────────────────────────────────────────────────────────
$muestras=[];

foreach($TABLES as $cfg){
  $tabla=$cfg['name']; $dateCol=$cfg['date_col'];
  if(empty($dateCol)) continue;

  // base WHERE (fecha)
  $where = " WHERE `{$dateCol}` BETWEEN '{$desdeDT}' AND '{$hastaDT}'";

  // búsqueda simple
  $andSearch='';
  if($busca!==''){
    $b=$db->escape('%'.$busca.'%');
    $andSearch = " AND (Sample_ID LIKE '{$b}' OR Sample_Number LIKE '{$b}' OR test_type LIKE '{$b}')";
  }

  // arma SQL con filtro de descartados si está configurado
  $sql = "SELECT * FROM `{$tabla}`{$where}{$andSearch}";
  if(!empty($cfg['discard_col'])){
    $col=$cfg['discard_col'];
    $sql .= " AND (`{$col}` IS NULL OR `{$col}`=0)";
  }

  // intenta ejecutar; si falla por columna desconocida, reintenta sin filtro de descartados
  try{
    $res = $db->query($sql);
  }catch(Throwable $e){
    if(stripos($e->getMessage(),"Unknown column")!==false){
      // reintento sin el filtro de descartados
      $sql = "SELECT * FROM `{$tabla}`{$where}{$andSearch}";
      $res = $db->query($sql);
    }else{
      error_log("SELECT fallo en {$tabla}: ".$e->getMessage());
      continue;
    }
  }

  while($row=$res->fetch_assoc()){
    // detectar bandeja según prioridad
    $bandeja=null;
    $candidatas = $cfg['bandeja_cols'] ?: $POSIBLES_BANDEJAS;
    foreach($candidatas as $c){
      if(array_key_exists($c,$row) && $row[$c]!==null && $row[$c]!==''){
        $bandeja=$row[$c]; break;
      }
    }
    // si quieres solo filas con bandeja real, descomenta:
    // if($bandeja===null || $bandeja==='') continue;

    $muestras[] = [
      'tabla'         => $tabla,
      'id'            => $row['id']            ?? null,
      'Sample_ID'     => $row['Sample_ID']     ?? null,
      'Sample_Number' => $row['Sample_Number'] ?? null,
      'Material_Type' => $row['Material_Type'] ?? null,
      'test_type'     => $row['test_type']     ?? null,
      'Bandeja'       => $bandeja,
    ];
  }
  if(method_exists($res,'free')) $res->free();
}

usort($muestras,function($a,$b){
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
                  <th style="width:40px;"><input type="checkbox" id="chkAll"></th>
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
                        <?php if (!empty($m['id'])): ?>
                          <input class="form-check-input row-check" type="checkbox" name="items[]" value="<?= h($m['tabla'].'|'.$m['id']) ?>">
                        <?php endif; ?>
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
