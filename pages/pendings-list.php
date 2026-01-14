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

function normNum($v){
  $s = strtoupper(trim((string)$v));
  if ($s === '') return '';
  if (preg_match('/^\d+$/', $s)) return (string)intval($s);              // 0078 -> 78
  if (preg_match('/^G0*\d+$/', $s)) return 'G'.(string)intval(substr($s,1)); // G001 -> G1
  return $s;
}
function makeKey($sid, $sno, $tt){
  return N($sid) . "|" . normNum($sno) . "|" . N($tt);
}

function daysSince($date){
  if(!$date) return 0;
  $t = strtotime($date);
  if(!$t) return 0;
  return (time() - $t) / 86400;
}

/* =============================
   FILTRO FECHA (solo requisiciones)
============================= */
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-90 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from);
$toEsc   = $db->escape($to);

/* =============================
   1) REQUISICIONES (rango de fechas)
============================= */
$req = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
");
if (!is_array($req)) $req = [];

/* =============================
   2) WORKFLOW (FUENTE DE VERDAD)
   - NO filtramos por fecha aquí, porque el workflow puede avanzar fuera del rango
   - index por Sample_ID + Sample_Number + Test_Type
============================= */
$wf = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Status, Updated_At, Process_Started
  FROM test_workflow
");
if (!is_array($wf)) $wf = [];

$wfIndex = [];
foreach ($wf as $w) {
  $key = makeKey($w['Sample_ID'] ?? '', $w['Sample_Number'] ?? '', $w['Test_Type'] ?? '');
  if ($key === "||") continue;

  // Updated_At si existe; si no, Process_Started
  $sd = $w['Updated_At'] ?? $w['Process_Started'] ?? null;

  $wfIndex[$key] = [
    'status' => (string)($w['Status'] ?? 'Registrado'),
    'sd'     => $sd
  ];
}

/* =============================
   3) RESUMEN + PENDIENTES
   - Si NO existe en test_workflow => lo tratamos como Registrado/SIN
   - Si existe, usamos su Status
============================= */
$summary = [];
$pending = [];

foreach ($req as $row){

  if (empty($row['Test_Type'])) continue;

  $tests = array_map('trim', explode(',', (string)$row['Test_Type']));

  foreach ($tests as $t) {

    $T = N($t);
    if ($T === '') continue;

    if (!isset($summary[$T])) {
      $summary[$T] = [
        'sin'=>0,'prep'=>0,'prep_est'=>0,
        'real'=>0,'real_est'=>0,'ent'=>0,
        'rev'=>0,'rep'=>0,'total'=>0
      ];
    }

    $key = makeKey($row['Sample_ID'] ?? '', $row['Sample_Number'] ?? '', $T);

    $wfRow = $wfIndex[$key] ?? null;

    // Base date para estancados
    $SD = $wfRow['sd'] ?? ($row['Registed_Date'] ?? null);

    // Status del workflow o Registrado si no existe
    $status = $wfRow['status'] ?? 'Registrado';

    // Mapeo a tus códigos internos
    $stage = 'SIN';
    if ($status === 'Registrado')   $stage = 'SIN';
    if ($status === 'Preparación')  $stage = 'PREP';
    if ($status === 'Realización')  $stage = 'REAL';
    if ($status === 'Repetición')   $stage = 'REP';
    if ($status === 'Entrega')      $stage = 'ENT';
    if ($status === 'Revisado')     $stage = 'REV';

    // Estancados (por Updated_At del workflow)
    if ($stage === 'PREP' && daysSince($SD) >= 3) $stage = 'PREP_EST';
    if ($stage === 'REAL' && daysSince($SD) >= 4) $stage = 'REAL_EST';

    // Conteo
    if ($stage === 'SIN')      $summary[$T]['sin']++;
    if ($stage === 'PREP')     $summary[$T]['prep']++;
    if ($stage === 'PREP_EST') $summary[$T]['prep_est']++;
    if ($stage === 'REAL')     $summary[$T]['real']++;
    if ($stage === 'REAL_EST') $summary[$T]['real_est']++;
    if ($stage === 'ENT')      $summary[$T]['ent']++;
    if ($stage === 'REV')      $summary[$T]['rev']++;
    if ($stage === 'REP')      $summary[$T]['rep']++;

    $summary[$T]['total']++;

    // Lista de pendientes SIN iniciar = Registrado (o no existe en workflow)
    if ($stage === 'SIN') {
      $pending[] = [
        'sid'  => $row['Sample_ID'] ?? '',
        'num'  => $row['Sample_Number'] ?? '',
        'type' => $T,
        'date' => $row['Registed_Date'] ?? ''
      ];
    }
  }
}

ksort($summary);
?>

<main id="main" class="main">
  <div class="pagetitle d-flex align-items-center justify-content-between">
    <h1>Lista de Pendientes</h1>

    <form class="d-flex gap-2" method="GET" style="max-width:520px;">
      <input type="date" name="from" class="form-control form-control-sm" value="<?=h($from)?>">
      <input type="date" name="to" class="form-control form-control-sm" value="<?=h($to)?>">
      <button class="btn btn-sm btn-dark">Filtrar</button>
    </form>
  </div>

  <section class="section">

    <div class="card mb-4">
      <div class="card-body">
        <h4 class="card-title">Estado por ensayo (según test_workflow)</h4>

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
              <th>Revisado</th>
              <th>Repetición</th>
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
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=SIN&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-dark btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['prep']?>
    <?php if($s['prep']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=PREP&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-primary btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td class="text-danger fw-bold">
    <?=$s['prep_est']?>
    <?php if($s['prep_est']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=PREP_EST&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['real']?>
    <?php if($s['real']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=REAL&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-info btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td class="text-danger fw-bold">
    <?=$s['real_est']?>
    <?php if($s['real_est']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=REAL_EST&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['ent']?>
    <?php if($s['ent']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=ENT&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-secondary btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['rev']?>
    <?php if($s['rev']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=REV&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-warning btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td>
    <?=$s['rep']?>
    <?php if($s['rep']>0): ?>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&stage=REP&from=<?=h($from)?>&to=<?=h($to)?>"
       class="btn btn-danger btn-sm ms-1">
       <i class="bi bi-printer"></i>
    </a>
    <?php endif; ?>
  </td>

  <td><b><?=$s['total']?></b></td>

  <td>
    <a target="_blank"
       href="../pdf/pendings-process.php?type=<?=urlencode($t)?>&from=<?=h($from)?>&to=<?=h($to)?>"
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
        <h4 class="card-title">Pendientes sin iniciar (Registrado en workflow)</h4>

        <table class="table datatable">
          <thead>
            <tr>
              <th>#</th>
              <th>Muestra</th>
              <th>Número</th>
              <th>Tipo</th>
              <th>Fecha Registro</th>
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
