<?php
// home_fast.php
$page_title = "Home";
$class_home = " ";
require_once "../config/load.php";
if (!$session->isUserLoggedIn(true)) { redirect("/index.php", false); }

// ------------------------------
// Helpers
// ------------------------------
function N($v){ return strtoupper(trim((string)$v)); }
function K($sid,$num,$tt){ return N($sid).'|'.N($num).'|'.N($tt); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Detecta una columna de fecha viable en una tabla
function pick_date_col(string $table, array $candidates = ['Start_Date','Delivery_Date','Register_Date','Registed_Date','Created_At','CreatedAt','Date','Updated_At']){
  global $db;
  foreach ($candidates as $col) {
    $res = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$col}'");
    if ($res && $res->num_rows>0) return $col;
  }
  return null;
}

// Badge/labels
function status_badge($s){
  switch ($s) {
    case 'Preparation': return 'primary';
    case 'Realization': return 'secondary';
    case 'Delivery':    return 'success';
    case 'Review':      return 'dark';
    case 'Repeat':      return 'warning';
    default:            return 'light';
  }
}
function status_label($s){
  switch ($s) {
    case 'Preparation': return 'Preparación';
    case 'Realization': return 'Realización';
    case 'Delivery':    return 'Entrega';
    case 'Review':      return 'Revisión';
    case 'Repeat':      return 'Repetición';
    default:            return $s;
  }
}

// ------------------------------
// Parámetros de UI (GET)
// ------------------------------
$range = isset($_GET['r']) ? (int)$_GET['r'] : 14;      // días para ventana “Proceso”
$limit = isset($_GET['l']) ? (int)$_GET['l'] : 200;     // máximo filas a mostrar
$range = ($range>=1 && $range<=60)? $range : 14;
$limit = ($limit>=50 && $limit<=1000)? $limit : 200;

$fromDate   = date('Y-m-d', strtotime("-{$range} days"));
$from7      = date('Y-m-d', strtotime('-7 days'));
$from31     = date('Y-m-d', strtotime('-31 days'));

// ------------------------------
// 1) Requisiciones recientes (limitadas)
// ------------------------------
$Requisitions = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date >= '{$fromDate}'
  ORDER BY Registed_Date DESC
  LIMIT {$limit}
");

// ------------------------------
// 2) Cargar ESTADOS una sola vez (con filtro por fecha auto-detectado)
//    Creamos un “set” (mapa) por estado: Preparation / Realization / Delivery / Review / Repeat
// ------------------------------
$sets = ['Preparation'=>[], 'Realization'=>[], 'Delivery'=>[], 'Review'=>[], 'Repeat'=>[]];

// Preparation
if ($col = pick_date_col('test_preparation')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation WHERE `{$col}` >= '{$fromDate}'");
} else {
  $rows = []; // si no hay fecha, evitamos cargar todo
}
foreach ($rows as $r){ $sets['Preparation'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// Realization
if ($col = pick_date_col('test_realization')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization WHERE `{$col}` >= '{$fromDate}'");
} else { $rows = []; }
foreach ($rows as $r){ $sets['Realization'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// Delivery
if ($col = pick_date_col('test_delivery')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery WHERE `{$col}` >= '{$fromDate}'");
} else { $rows = []; }
foreach ($rows as $r){ $sets['Delivery'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// Review (excluir los que ya están en reviewed)
if ($col = pick_date_col('test_review')) {
  $rows = find_by_sql("
    SELECT p.Sample_ID, p.Sample_Number, p.Test_Type
    FROM test_review p
    LEFT JOIN test_reviewed r
      ON r.Sample_ID=p.Sample_ID AND r.Sample_Number=p.Sample_Number AND r.Test_Type=p.Test_Type
    WHERE r.Sample_ID IS NULL AND p.`{$col}` >= '{$fromDate}'
  ");
} else {
  $rows = find_by_sql("
    SELECT p.Sample_ID, p.Sample_Number, p.Test_Type
    FROM test_review p
    LEFT JOIN test_reviewed r
      ON r.Sample_ID=p.Sample_ID AND r.Sample_Number=p.Sample_Number AND r.Test_Type=p.Test_Type
    WHERE r.Sample_ID IS NULL
  ");
}
foreach ($rows as $r){ $sets['Review'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// Repeat (para sección específica usamos 7 días, para el mapa usamos ventana general)
if ($col = pick_date_col('test_repeat')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_repeat WHERE `{$col}` >= '{$fromDate}'");
} else { $rows = []; }
foreach ($rows as $r){ $sets['Repeat'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// ------------------------------
// 3) Construir tabla “Proceso de muestreo” + contadores
//    Prioridad: Repeat > Review > Delivery > Realization > Preparation
// ------------------------------
$statusCounts = ['Preparation'=>0,'Realization'=>0,'Delivery'=>0,'Review'=>0,'Repeat'=>0];
$procesoRows  = [];

foreach ($Requisitions as $req) {
  if (empty($req['Test_Type'])) continue;
  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', $req['Test_Type'])));
  foreach ($types as $ttRaw) {
    $tt = N($ttRaw);
    $key = K($sid,$num,$tt);

    $st = null;
    if     (isset($sets['Repeat'][$key]))      $st='Repeat';
    elseif (isset($sets['Review'][$key]))      $st='Review';
    elseif (isset($sets['Delivery'][$key]))    $st='Delivery';
    elseif (isset($sets['Realization'][$key])) $st='Realization';
    elseif (isset($sets['Preparation'][$key])) $st='Preparation';

    if ($st) {
      $statusCounts[$st]++;
      $procesoRows[] = [
        'sid'=>$sid,'num'=>$num,'tt'=>$tt,'st'=>$st
      ];
    }
  }
}

// ------------------------------
// 4) Ensayos en Repetición (últimos 7 días)
// ------------------------------
$repeatDateCol = pick_date_col('test_repeat') ?: 'Start_Date';
$repeatRows = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, `{$repeatDateCol}` AS SD, Send_By
  FROM test_repeat
  WHERE `{$repeatDateCol}` >= '{$from7}'
  ORDER BY `{$repeatDateCol}` DESC
");

// ------------------------------
// 5) Método Proctor (SP) – últimos 31 días, solo pendientes (no en Preparation/Review)
//    Busca 1 registro de granulometría en las tablas conocidas (UNION ALL LIMIT 1)
// ------------------------------
$ReqSP = find_by_sql("
  SELECT Sample_ID, Sample_Number, Sample_Date, Test_Type
  FROM lab_test_requisition_form
  WHERE Registed_Date >= '{$from31}'
  ORDER BY Registed_Date DESC
");
$spRows = [];
foreach ($ReqSP as $req) {
  if (empty($req['Test_Type'])) continue;
  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', $req['Test_Type'])));
  foreach ($types as $tt) {
    if (N($tt) !== 'SP') continue;
    $key = K($sid,$num,'SP');
    if (isset($sets['Preparation'][$key]) || isset($sets['Review'][$key])) continue; // solo pendientes

    // Escapar para SQL
    $sidEsc  = $db->escape($sid);
    $numEsc  = $db->escape($num);

    $gs = find_by_sql("
      SELECT 'general'  AS src, CumRet11 AS t34, CumRet13 AS t38, CumRet14 AS tNo4
        FROM grain_size_general WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' LIMIT 1
      UNION ALL
      SELECT 'coarse',  CumRet9,  CumRet10, CumRet11
        FROM grain_size_coarse  WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' LIMIT 1
      UNION ALL
      SELECT 'fine',    CumRet9,  CumRet11, CumRet12
        FROM grain_size_fine    WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' LIMIT 1
      UNION ALL
      SELECT 'lpf',     CumRet5,  CumRet6,  CumRet7
        FROM grain_size_lpf     WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' LIMIT 1
      UNION ALL
      SELECT 'upstream',CumRet8,  CumRet10, CumRet11
        FROM grain_size_upstream_transition_fill
        WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$numEsc}' LIMIT 1
      LIMIT 1
    ");

    if (!empty($gs)) {
      $g    = $gs[0];
      $T3p4 = (float)($g['t34']  ?? 0);
      $T3p8 = (float)($g['t38']  ?? 0);
      $TNo4 = (float)($g['tNo4'] ?? 0);

      if     ($T3p4 > 0)                                 $metodo = 'C';
      elseif ($T3p8 > 0 && $T3p4 == 0)                   $metodo = 'B';
      elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0)     $metodo = 'A';
      else                                               $metodo = 'No se puede determinar el método';

      $corr = ($T3p4 > 5) ? 'Corrección por Sobre Tamaño (hacer SG finas y gruesas)' : '';

      $spRows[] = ['sid'=>$sid,'num'=>$num,'met'=>$metodo,'note'=>$corr];
    } else {
      $spRows[] = ['sid'=>$sid,'num'=>$num,'met'=>'No data','note'=>'Sin granulometría para inferir método'];
    }
  }
}

// ------------------------------
// 6) Pendientes por tipo (cuántos no aparecen en ningún estado)
// ------------------------------
$pendCounts = [];
$seen = [];
foreach ($Requisitions as $req) {
  if (empty($req['Test_Type'])) continue;
  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', $req['Test_Type'])));
  foreach ($types as $ttRaw) {
    $tt = N($ttRaw);
    $key = K($sid,$num,$tt);
    if (isset($seen[$key])) continue;
    $seen[$key]=true;

    if (
      !isset($sets['Preparation'][$key]) &&
      !isset($sets['Realization'][$key]) &&
      !isset($sets['Delivery'][$key]) &&
      !isset($sets['Review'][$key]) &&
      !isset($sets['Repeat'][$key])
    ) {
      $pendCounts[$tt] = ($pendCounts[$tt] ?? 0) + 1;
    }
  }
}
?>
<?php include_once('../components/header.php'); ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Panel Control</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home_fast.php">Home</a></li>
        <li class="breadcrumb-item active">Panel Control</li>
      </ol>
    </nav>
  </div>

  <?= display_msg($msg); ?>

  <!-- Filtros rápidos -->
  <form class="row g-2 align-items-end mb-3" method="get">
    <div class="col-auto">
      <label class="form-label">Rango (días)</label>
      <select name="r" class="form-select">
        <?php foreach([7,14,21,31,60] as $opt): ?>
          <option value="<?= $opt ?>" <?= $opt===$range?'selected':'' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label">Máx. filas</label>
      <select name="l" class="form-select">
        <?php foreach([50,100,200,400,600,1000] as $opt): ?>
          <option value="<?= $opt ?>" <?= $opt===$limit?'selected':'' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Aplicar</button>
    </div>
  </form>

  <section class="section dashboard">
    <div class="row">
      <!-- Izquierda -->
      <div class="col-lg-8">
        <!-- Proceso de muestreo -->
        <div class="card recent-sales overflow-auto mb-3">
          <div class="card-body">
            <h5 class="card-title">Proceso de muestreo
              <span class="text-muted">| Últimos <?= (int)$range ?> días – máx <?= (int)$limit ?></span>
            </h5>
            <div class="table-responsive">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>Muestra</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($procesoRows)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Sin datos en el rango seleccionado.</td></tr>
                  <?php else: ?>
                    <?php foreach ($procesoRows as $row): ?>
                      <tr>
                        <td><?= h($row['sid']) ?></td>
                        <td><?= h($row['num']) ?></td>
                        <td><?= h($row['tt']) ?></td>
                        <td><span class="badge bg-<?= status_badge($row['st']) ?>"><?= h(status_label($row['st'])) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Ensayos en Repetición (7 días) -->
        <div class="card recent-sales overflow-auto mb-3">
          <div class="card-body">
            <h5 class="card-title">Ensayos en Repetición <span class="text-muted">| Últimos 7 días</span></h5>
            <div class="table-responsive">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>Muestra</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Enviado por</th>
                  </tr>
                </thead>
                <tbody>
                <?php if (empty($repeatRows)): ?>
                  <tr><td colspan="5" class="text-center text-muted">No hay ensayos en repetición recientes.</td></tr>
                <?php else: ?>
                  <?php foreach ($repeatRows as $r): ?>
                    <tr>
                      <td><?= h($r['Sample_ID']) ?></td>
                      <td><?= h($r['Sample_Number']) ?></td>
                      <td><?= h($r['Test_Type']) ?></td>
                      <td><?= h(date('Y-m-d', strtotime($r['SD']))) ?></td>
                      <td><?= h($r['Send_By']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Método Proctor (SP) -->
        <div class="card recent-sales overflow-auto">
          <div class="card-body">
            <h5 class="card-title">Método para Proctor (SP) <span class="text-muted">| Últimos 31 días</span></h5>
            <div class="table-responsive">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>Muestra</th>
                    <th>Método</th>
                    <th>Comentario</th>
                  </tr>
                </thead>
                <tbody>
                <?php if (empty($spRows)): ?>
                  <tr><td colspan="3" class="text-center text-muted">Sin muestras SP pendientes o sin granulometría.</td></tr>
                <?php else: ?>
                  <?php foreach ($spRows as $r): ?>
                    <tr>
                      <td><?= h($r['sid'].'-'.$r['num'].'-SP') ?></td>
                      <td><?= h($r['met']) ?></td>
                      <td><?= h($r['note']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
      <!-- /Izquierda -->

      <!-- Derecha -->
      <div class="col-lg-4">
        <!-- Cantidades en Proceso -->
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Cantidades en Proceso</h5>
            <ul class="list-group">
              <?php foreach ($statusCounts as $st=>$cnt): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= h(status_label($st)) ?>
                  <span class="badge bg-primary rounded-pill"><?= (int)$cnt ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <!-- Pendientes por tipo -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Cantidad de Ensayos Pendientes</h5>
            <ul class="list-group">
              <?php if (empty($pendCounts)): ?>
                <li class="list-group-item">✅ No hay ensayos pendientes</li>
              <?php else: ?>
                <?php foreach ($pendCounts as $tt=>$cnt): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <code><?= h($tt) ?></code>
                    <span class="badge bg-primary rounded-pill"><?= (int)$cnt ?></span>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
      <!-- /Derecha -->
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
