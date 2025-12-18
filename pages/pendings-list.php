<?php
$page_title = 'Lista de Pendientes';
$Pending_List = 'show';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

if (!function_exists('h')) {
    function h($string){
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

function N($v){ return strtoupper(trim((string)$v)); }

function daysSince($date){
    if(!$date) return 0;
    $t = strtotime($date);
    if(!$t) return 0;
    return (time() - $t) / 86400;
}

/* =============================
   FILTRO FECHA (para rendimiento)
   - por defecto: últimos 90 días
============================= */
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-90 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from);
$toEsc   = $db->escape($to);

/* =============================
   CARGA BASE (OPTIMIZADA)
   - NO usamos find_all() para traer todo
   - Traemos solo campos necesarios
============================= */
$req = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
");

/* Si no hay resultados, evita warnings */
if (!is_array($req)) $req = [];

/* =============================
   INDEX GENERAL DE ESTADO
   - en vez de find_all(), solo columnas necesarias
============================= */
$index = [];

/* PREPARATION */
$prep = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_preparation
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($prep)) $prep = [];

foreach ($prep as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'PREP',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* REALIZATION */
$real = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_realization
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($real)) $real = [];

foreach ($real as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'REAL',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* DELIVERY */
$ent = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_delivery
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($ent)) $ent = [];

foreach ($ent as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'ENT',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* REVIEW */
$rev = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_review
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rev)) $rev = [];

foreach ($rev as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'REV',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* REPEAT */
$rep = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_repeat
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rep)) $rep = [];

foreach ($rep as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'REP',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* REVIEWED */
$rev2 = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date
    FROM test_reviewed
    WHERE Start_Date IS NOT NULL
      AND DATE(Start_Date) BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($rev2)) $rev2 = [];

foreach ($rev2 as $r) {
    $index[N($r['Sample_ID'] ?? '')."|".N($r['Sample_Number'] ?? '')."|".N($r['Test_Type'] ?? '')] = [
        'stage' => 'REV',
        'SD'    => $r['Start_Date'] ?? null
    ];
}

/* =============================
   CONSTRUCCIÓN DE RESUMEN (IGUAL)
============================= */
$summary = [];
$pending = [];

foreach ($req as $row){

    if(empty($row["Test_Type"])) continue;

    $tests = array_map('trim', explode(",", $row["Test_Type"]));

    foreach ($tests as $t){

        $T = N($t);
        if($T==='') continue;

        if(!isset($summary[$T])){
            $summary[$T] = [
                'sin'=>0,'prep'=>0,'prep_est'=>0,
                'real'=>0,'real_est'=>0,'ent'=>0,
                'rev'=>0,'rep'=>0,'total'=>0
            ];
        }

        $key = N($row['Sample_ID'] ?? '')."|".N($row['Sample_Number'] ?? '')."|".$T;
        $stData = $index[$key] ?? null;

        $stage = $stData['stage'] ?? 'SIN';
        $SD    = $stData['SD'] ?? $row["Registed_Date"];

        if($stage === 'PREP' && daysSince($SD) >= 3) $stage = 'PREP_EST';
        if($stage === 'REAL' && daysSince($SD) >= 4) $stage = 'REAL_EST';

        if($stage === 'SIN')      $summary[$T]['sin']++;
        if($stage === 'PREP')     $summary[$T]['prep']++;
        if($stage === 'PREP_EST') $summary[$T]['prep_est']++;
        if($stage === 'REAL')     $summary[$T]['real']++;
        if($stage === 'REAL_EST') $summary[$T]['real_est']++;
        if($stage === 'ENT')      $summary[$T]['ent']++;
        if($stage === 'REV')      $summary[$T]['rev']++;
        if($stage === 'REP')      $summary[$T]['rep']++;

        $summary[$T]['total']++;

        if($stage === 'SIN'){
            $pending[] = [
                'sid'=>$row['Sample_ID'],
                'num'=>$row['Sample_Number'],
                'type'=>$T,
                'date'=>$row['Registed_Date']
            ];
        }
    }
}



ksort($summary);
?>

<main id="main" class="main">
  <div class="pagetitle d-flex align-items-center justify-content-between">
    <h1>Lista de Pendientes</h1>

    <!-- filtro (opcional) -->
    <form class="d-flex gap-2" method="GET" style="max-width:520px;">
      <input type="date" name="from" class="form-control form-control-sm" value="<?=h($from)?>">
      <input type="date" name="to" class="form-control form-control-sm" value="<?=h($to)?>">
      <button class="btn btn-sm btn-dark">Filtrar</button>
    </form>
  </div>

  <section class="section">

    <div class="card mb-4">
      <div class="card-body">
        <h4 class="card-title">Estado por ensayo (incluye estancados)</h4>

        <table class="table table-bordered small">
          <thead class="table-light">
            <tr>
              <th>Ensayo</th>
              <th>Sin iniciar</th>
              <th>Prep</th>
              <th>Prep est.</th>
              <th>Real</th>
              <th>Real est.</th>
              <th>Entrega</th>
              <th>Revisión</th>
              <th>Repeat</th>
              <th>Total</th>
              <th>PDF</th>
            </tr>
          </thead>
          <tbody>

<?php foreach($summary as $t => $s): ?>
<tr>
  <td><b><?=h($t)?></b></td>

  <td>
    <?=$s['sin']?>
    <?php if($s['sin']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=SIN"
       class="btn btn-dark btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['prep']?>
    <?php if($s['prep']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=PREP"
       class="btn btn-primary btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td class="text-danger fw-bold">
    <?=$s['prep_est']?>
    <?php if($s['prep_est']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=PREP_EST"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['real']?>
    <?php if($s['real']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=REAL"
       class="btn btn-info btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td class="text-danger fw-bold">
    <?=$s['real_est']?>
    <?php if($s['real_est']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=REAL_EST"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['ent']?>
    <?php if($s['ent']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=ENT"
       class="btn btn-secondary btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['rev']?>
    <?php if($s['rev']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=REV"
       class="btn btn-warning btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['rep']?>
    <?php if($s['rep']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>&stage=REP"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td><b><?=$s['total']?></b></td>

  <td>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=$t?>"
       class="btn btn-dark btn-sm">
       <i class="bi bi-printer"></i>
    </a>
  </td>
</tr>
<?php endforeach; ?>

          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Pendientes sin iniciar</h4>

        <table class="table datatable">
          <thead>
            <tr>
              <th>#</th>
              <th>Muestra</th>
              <th>Número</th>
              <th>Tipo</th>
              <th>Fecha Muestra</th>
            </tr>
          </thead>
          <tbody>
<?php foreach($pending as $i => $p): ?>
<tr>
  <td><?=$i+1?></td>
  <td><?=h($p['sid'])?></td>
  <td><?=h($p['num'])?></td>
  <td><code><?=h($p['type'])?></code></td>
  <td><?=h($p['date'])?></td>
</tr>
<?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</main>

<?php include_once('../components/footer.php'); ?>
