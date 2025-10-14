<?php
// home_fast.php
$page_title = "Home";
$class_home = " ";
require_once "../config/load.php";
if (!$session->isUserLoggedIn(true)) { redirect("/index.php", false); }

// ==============================
// Helpers básicos
// ==============================
function N($v){ return strtoupper(trim((string)$v)); }
function K($sid,$num,$tt){ return N($sid).'|'.N($num).'|'.N($tt); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Detecta una columna de fecha válida en una tabla
function pick_date_col(string $table, array $candidates = [
  'Start_Date','Delivery_Date','Register_Date','Registed_Date',
  'Created_At','CreatedAt','Date','Updated_At'
]){
  global $db;
  foreach ($candidates as $col) {
    $res = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$db->escape($col)}'");
    if ($res && $res->num_rows>0) return $col;
  }
  return null;
}

// ¿Existe tabla/col?
function table_exists(string $table): bool {
  global $db;
  $res = $db->query("SHOW TABLES LIKE '{$db->escape($table)}'");
  return $res && $res->num_rows > 0;
}
function col_exists(string $table, string $col): bool {
  global $db;
  $res = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$db->escape($col)}'");
  return $res && $res->num_rows > 0;
}

// Trae un trío (t34,t38,tNo4) de una tabla si existen columnas
function try_fetch_triplet(string $table, array $map, string $sid, string $num): ?array {
  global $db;
  if (!table_exists($table)) return null;
  foreach ($map as $alias=>$col) {
    if (!col_exists($table, $col)) return null;
  }
  $sidEsc = $db->escape($sid);
  $numEsc = $db->escape($num);
  $sql = sprintf(
    "SELECT `%s` AS t34, `%s` AS t38, `%s` AS tNo4 FROM `%s` WHERE Sample_ID='%s' AND Sample_Number='%s' LIMIT 1",
    $map['t34'], $map['t38'], $map['tNo4'], $table, $sidEsc, $numEsc
  );
  $rows = find_by_sql($sql);
  if (!$rows || empty($rows)) return null;
  $r = $rows[0];
  return [
    'src'  => $table,
    't34'  => (float)($r['t34']  ?? 0),
    't38'  => (float)($r['t38']  ?? 0),
    'tNo4' => (float)($r['tNo4'] ?? 0),
  ];
}

// Badges/labels
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

// ==============================
// Parámetros de UI (GET)
// ==============================
$range = isset($_GET['r']) ? (int)$_GET['r'] : 14;      // días para “Proceso”
$limit = isset($_GET['l']) ? (int)$_GET['l'] : 200;     // máximo filas
$range = ($range>=1 && $range<=60)? $range : 14;
$limit = ($limit>=50 && $limit<=1000)? $limit : 200;

$fromDate = date('Y-m-d', strtotime("-{$range} days"));
$from7    = date('Y-m-d', strtotime('-7 days'));
$from31   = date('Y-m-d', strtotime('-31 days'));

// ==============================
// 1) Requisiciones recientes (limitadas)
// ==============================
$Requisitions = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date >= '{$fromDate}'
  ORDER BY Registed_Date DESC
  LIMIT {$limit}
");

// ==============================
// 2) Sets de ESTADO (por tabla, con filtro por fecha detectado)
// ==============================
$sets = ['Preparation'=>[], 'Realization'=>[], 'Delivery'=>[], 'Review'=>[], 'Repeat'=>[]];

// Preparation
if ($col = pick_date_col('test_preparation')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation WHERE `{$col}` >= '{$fromDate}'");
} else { $rows = []; }
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

// Review excluyendo reviewed
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

// Repeat (para mapa usamos ventana general)
if ($col = pick_date_col('test_repeat')) {
  $rows = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_repeat WHERE `{$col}` >= '{$fromDate}'");
} else { $rows = []; }
foreach ($rows as $r){ $sets['Repeat'][ K($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true; }

// ==============================
// 3) Proceso de muestreo + contadores
// ==============================
$statusCounts = ['Preparation'=>0,'Realization'=>0,'Delivery'=>0,'Review'=>0,'Repeat'=>0];
$procesoRows  = [];

foreach ($Requisitions as $req) {
  if (empty($req['Test_Type'])) continue;
  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', $req['Test_Type'])));
  foreach ($types as $ttRaw) {
    $tt  = N($ttRaw);
    $key = K($sid,$num,$tt);

    $st = null;
    if     (isset($sets['Repeat'][$key]))      $st='Repeat';
    elseif (isset($sets['Review'][$key]))      $st='Review';
    elseif (isset($sets['Delivery'][$key]))    $st='Delivery';
    elseif (isset($sets['Realization'][$key])) $st='Realization';
    elseif (isset($sets['Preparation'][$key])) $st='Preparation';

    if ($st) {
      $statusCounts[$st]++;
      $procesoRows[] = ['sid'=>$sid,'num'=>$num,'tt'=>$tt,'st'=>$st];
    }
  }
}

// ==============================
// 4) Ensayos en Repetición (últimos 7 días)
// ==============================
$repeatDateCol = pick_date_col('test_repeat') ?: 'Start_Date';
$repeatRows = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, `{$repeatDateCol}` AS SD, Send_By
  FROM test_repeat
  WHERE `{$repeatDateCol}` >= '{$from7}'
  ORDER BY `{$repeatDateCol}` DESC
");

// ==============================
// 5) Método Proctor (SP) – robusto sin UNION
// ==============================
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

  // ¿Incluye SP?
  $hasSP = false;
  foreach (array_filter(array_map('trim', explode(',', $req['Test_Type']))) as $tt) {
    if (N($tt)==='SP'){ $hasSP=true; break; }
  }
  if (!$hasSP) continue;

  // Solo pendientes (no en Preparation ni Review)
  $key = K($sid,$num,'SP');
  if (isset($sets['Preparation'][$key]) || isset($sets['Review'][$key])) continue;

  // Probar tablas en orden: general, coarse, fine, lpf, upstream
  $triplet = null;

  $triplet = $triplet ?: try_fetch_triplet('grain_size_general',
              ['t34'=>'CumRet11','t38'=>'CumRet13','tNo4'=>'CumRet14'],$sid,$num);

  $triplet = $triplet ?: try_fetch_triplet('grain_size_coarse',
              ['t34'=>'CumRet9','t38'=>'CumRet10','tNo4'=>'CumRet11'],$sid,$num);

  $triplet = $triplet ?: try_fetch_triplet('grain_size_fine',
              ['t34'=>'CumRet9','t38'=>'CumRet11','tNo4'=>'CumRet12'],$sid,$num);

  $triplet = $triplet ?: try_fetch_triplet('grain_size_lpf',
              ['t34'=>'CumRet5','t38'=>'CumRet6','tNo4'=>'CumRet7'],$sid,$num);

  $triplet = $triplet ?: try_fetch_triplet('grain_size_upstream_transition_fill',
              ['t34'=>'CumRet8','t38'=>'CumRet10','tNo4'=>'CumRet11'],$sid,$num);

  if ($triplet) {
    $T3p4 = $triplet['t34'];
    $T3p8 = $triplet['t38'];
    $TNo4 = $triplet['tNo4'];

    if     ($T3p4 > 0)                             $metodo = 'C';
    elseif ($T3p8 > 0 && $T3p4 == 0)               $metodo = 'B';
    elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0) $metodo = 'A';
    else                                           $metodo = 'No se puede determinar el método';

    $corr = ($T3p4 > 5) ? 'Corrección por Sobre Tamaño (hacer SG finas y gruesas)' : '';

    $spRows[] = ['sid'=>$sid,'num'=>$num,'met'=>$metodo,'note'=>$corr];
  } else {
    $spRows[] = ['sid'=>$sid,'num'=>$num,'met'=>'No data','note'=>'Sin granulometría válida o columnas/tabla no existen'];
  }
}

// ==============================
// 6) Pendientes por tipo (no aparecen en ningún estado)
// ==============================
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
