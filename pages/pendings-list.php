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
   CARGA BASE
============================= */
$req = find_all("lab_test_requisition_form");

$prep = find_all("test_preparation");
$real = find_all("test_realization");
$ent  = find_all("test_delivery");
$rev  = find_all("test_review");
$rep  = find_all("test_repeat");
$rev2 = find_all("test_reviewed");

/* =============================
   INDEX GENERAL DE ESTADO
============================= */
$index = [];

foreach ($prep as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'PREP','SD'=>$r['Start_Date']];

foreach ($real as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'REAL','SD'=>$r['Start_Date']];

foreach ($ent as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'ENT','SD'=>$r['Start_Date']];

foreach ($rev as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'REV','SD'=>$r['Start_Date']];

foreach ($rep as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'REP','SD'=>$r['Start_Date']];

foreach ($rev2 as $r)
  $index[N($r['Sample_ID'])."|".N($r['Sample_Number'])."|".N($r['Test_Type'])]
    = ['stage'=>'REV','SD'=>$r['Start_Date']];

/* =============================
   CONSTRUCCIÓN DE RESUMEN
============================= */

$summary = [];   // tabla principal
$pending = [];   // lista sin iniciar (solo vista inferior)

foreach ($req as $row){

    if(empty($row["Test_Type"])) continue;

    $tests = array_map('trim', explode(",", $row["Test_Type"]));

    foreach ($tests as $t){

        $T = N($t);

        // Crear si no existe
        if(!isset($summary[$T])){
            $summary[$T] = [
                'sin'=>0,'prep'=>0,'prep_est'=>0,
                'real'=>0,'real_est'=>0,'ent'=>0,
                'rev'=>0,'rep'=>0,'total'=>0
            ];
        }

        $key = N($row['Sample_ID'])."|".N($row['Sample_Number'])."|".$T;

        $stData = $index[$key] ?? null;

        $stage = $stData['stage'] ?? 'SIN';
        $SD    = $stData['SD'] ?? $row["Registed_Date"];

        /* === ESTANCADOS === */
        if($stage === 'PREP' && daysSince($SD) >= 3)
            $stage = 'PREP_EST';

        if($stage === 'REAL' && daysSince($SD) >= 4)
            $stage = 'REAL_EST';

        /* === Contar === */
        if($stage === 'SIN')          $summary[$T]['sin']++;
        if($stage === 'PREP')         $summary[$T]['prep']++;
        if($stage === 'PREP_EST')     $summary[$T]['prep_est']++;
        if($stage === 'REAL')         $summary[$T]['real']++;
        if($stage === 'REAL_EST')     $summary[$T]['real_est']++;
        if($stage === 'ENT')          $summary[$T]['ent']++;
        if($stage === 'REV')          $summary[$T]['rev']++;
        if($stage === 'REP')          $summary[$T]['rep']++;

        $summary[$T]['total']++;

        /* === Lista inferior (solo SIN) === */
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

ksort($summary); // ordenar por tipo ensayo
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Lista de Pendientes</h1>
  </div>

  <section class="section">

    <!-- ==============================
         TABLA RESUMEN
    =============================== -->
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

  <!-- SIN -->
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

  <!-- PREP -->
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

  <!-- PREP_EST -->
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

  <!-- REAL -->
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

  <!-- REAL_EST -->
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

  <!-- ENTREGA -->
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

  <!-- REV -->
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

  <!-- REP -->
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

  <!-- TOTAL -->
  <td><b><?=$s['total']?></b></td>

  <!-- PDF GENERAL -->
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

    <!-- ==============================
         LISTA DE SIN INICIAR
    =============================== -->
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
