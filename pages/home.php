<?php
// home.php — Dashboard con paginación (sin filtros de rango/limite en UI)
$page_title = "Home";
$class_home = " ";
require_once "../config/load.php";
if (!$session->isUserLoggedIn(true)) { redirect("/index.php", false); }

// ==============================
// Helpers
// ==============================
function N($v){ return strtoupper(trim((string)$v)); }
function K($sid,$num,$tt){ return N($sid).'|'.N($num).'|'.N($tt); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

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

// Detección defensiva de columna de fecha (para secciones auxiliares)
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

// ¿Existe registro en tabla X?
function exists_in(string $table, string $sid, string $num, string $tt): bool {
  global $db;
  if (!$table) return false;
  $sidE = $db->escape($sid);
  $numE = $db->escape($num);
  $ttE  = $db->escape($tt);
  $sql  = "SELECT 1 FROM `{$table}` WHERE Sample_ID='{$sidE}' AND Sample_Number='{$numE}' AND Test_Type='{$ttE}' LIMIT 1";
  $res  = $db->query($sql);
  return $res && $res->num_rows>0;
}

// SP: tabla/col existen
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
// SP: intenta devolver tripleta t34,t38,tNo4 de una tabla concreta
function try_fetch_triplet(string $table, array $map, string $sid, string $num): ?array {
  global $db;
  if (!table_exists($table)) return null;
  foreach ($map as $alias=>$col) if (!col_exists($table,$col)) return null;

  $sidE=$db->escape($sid); $numE=$db->escape($num);
  $sql = sprintf(
    "SELECT `%s` AS t34, `%s` AS t38, `%s` AS tNo4
     FROM `%s`
     WHERE Sample_ID='%s' AND Sample_Number='%s' LIMIT 1",
     $map['t34'],$map['t38'],$map['tNo4'],$table,$sidE,$numE
  );
  $rows = find_by_sql($sql);
  if (!$rows) return null;
  $r = $rows[0];
  return [
    'src'=>$table,
    't34'=>(float)($r['t34']??0),
    't38'=>(float)($r['t38']??0),
    'tNo4'=>(float)($r['tNo4']??0),
  ];
}

// ==============================
// Paginación (clásica con OFFSET) optimizada
// ==============================
$PAGE_SIZE = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $PAGE_SIZE;

// Conteo total con caché simple para no recalcular siempre
function cache_get($k,$ttl=300){ $f=sys_get_temp_dir()."/$k.cache.php"; if(!is_file($f)||filemtime($f)+$ttl<time()) return null; return include $f; }
function cache_set($k,$v){ $f=sys_get_temp_dir()."/$k.cache.php"; @file_put_contents($f,"<?php\nreturn ".var_export($v,true).";"); }

if (($totalReq = cache_get('cnt_lab_test_req')) === null) {
  $row = find_by_sql("SELECT COUNT(*) AS c
                      FROM lab_test_requisition_form
                      WHERE Registed_Date IS NOT NULL
                        AND Test_Type IS NOT NULL AND Test_Type <> ''");
  $totalReq = (int)($row[0]['c'] ?? 0);
  cache_set('cnt_lab_test_req', $totalReq);
}

$totalPages = max(1, (int)ceil($totalReq / $PAGE_SIZE));

// Traer bloque
$Requisitions = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
  FROM lab_test_requisition_form
  WHERE Registed_Date IS NOT NULL
    AND Test_Type IS NOT NULL AND Test_Type <> ''
  ORDER BY Registed_Date DESC
  LIMIT {$PAGE_SIZE} OFFSET {$offset}
");


// ==============================
// Construcción de “Proceso de muestreo”
// ==============================
// NOTA: Para cada ensayo, determinamos estado por EXISTE en orden de prioridad
$statusCounts = ['Preparation'=>0,'Realization'=>0,'Delivery'=>0,'Review'=>0,'Repeat'=>0];
$procesoRows  = [];

foreach ($Requisitions as $req) {
  if (empty($req['Test_Type'])) continue;

  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', $req['Test_Type'])));

  foreach ($types as $ttRaw) {
    $tt = N($ttRaw);

    // Prioridad: Repeat > Review > Delivery > Realization > Preparation
    $st = null;
    if     (exists_in('test_repeat',      $sid,$num,$tt)) $st='Repeat';
    elseif (exists_in('test_review',      $sid,$num,$tt)) {
      // excluir si está en reviewed
      if (!exists_in('test_reviewed', $sid,$num,$tt)) $st='Review';
    }
    elseif (exists_in('test_delivery',    $sid,$num,$tt)) $st='Delivery';
    elseif (exists_in('test_realization', $sid,$num,$tt)) $st='Realization';
    elseif (exists_in('test_preparation', $sid,$num,$tt)) $st='Preparation';

    if ($st) {
      $statusCounts[$st]++;
      $procesoRows[] = [
        'sid'=>$sid,'num'=>$num,'tt'=>$tt,'st'=>$st
      ];
    }
  }
}

// ==============================
// Ensayos en Repetición (recientes, sin UI de filtros)
// ==============================
$from7 = date('Y-m-d', strtotime('-7 days'));
$repDateCol = pick_date_col('test_repeat') ?: 'Start_Date';
$repeatRows = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, `{$repDateCol}` AS SD, Send_By
  FROM test_repeat
  WHERE `{$repDateCol}` >= '{$from7}'
  ORDER BY `{$repDateCol}` DESC
  LIMIT 200
");

// ==============================
// Método Proctor (SP) — robusto, sin UNION
// ==============================
$from31 = date('Y-m-d', strtotime('-31 days'));
$ReqSP = find_by_sql("
  SELECT Sample_ID, Sample_Number, Sample_Date, Test_Type
  FROM lab_test_requisition_form
  WHERE Registed_Date >= '{$from31}'
  ORDER BY Registed_Date DESC
  LIMIT 400
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
  if (exists_in('test_preparation',$sid,$num,'SP') || exists_in('test_review',$sid,$num,'SP')) continue;

  // Probar fuentes en orden
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
// Pendientes por tipo (solo del bloque de página actual)
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
    $seen[$key] = true;

    if (
      !exists_in('test_preparation',$sid,$num,$tt) &&
      !exists_in('test_realization',$sid,$num,$tt) &&
      !exists_in('test_delivery',   $sid,$num,$tt) &&
      !(exists_in('test_review',$sid,$num,$tt) && !exists_in('test_reviewed',$sid,$num,$tt)) &&
      !exists_in('test_repeat',     $sid,$num,$tt)
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
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item active">Panel Control</li>
      </ol>
    </nav>
  </div>

  <?= display_msg($msg); ?>

  <section class="section dashboard">
    <div class="row">

      <!-- Izquierda -->
      <div class="col-lg-8">

        <!-- Proceso de muestreo -->
        <div class="card recent-sales overflow-auto mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Proceso de muestreo</h5>
              <!-- Paginación arriba -->
              <nav aria-label="Pag" class="d-none d-md-block">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item <?= $page<=1?'disabled':'' ?>">
                    <a class="page-link" href="?page=1">&laquo;</a>
                  </li>
                  <li class="page-item <?= $page<=1?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= max(1,$page-1) ?>">&lsaquo;</a>
                  </li>
                  <li class="page-item disabled"><span class="page-link"><?= $page ?> / <?= $totalPages ?></span></li>
                  <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= min($totalPages,$page+1) ?>">&rsaquo;</a>
                  </li>
                  <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= $totalPages ?>">&raquo;</a>
                  </li>
                </ul>
              </nav>
            </div>

            <div class="table-responsive mt-3">
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
                  <tr><td colspan="4" class="text-center text-muted">No hay datos en esta página.</td></tr>
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

            <!-- Paginación abajo -->
            <div class="d-flex justify-content-end">
              <nav aria-label="Pag">
                <ul class="pagination pagination-sm">
                  <li class="page-item <?= $page<=1?'disabled':'' ?>">
                    <a class="page-link" href="?page=1">&laquo;</a>
                  </li>
                  <li class="page-item <?= $page<=1?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= max(1,$page-1) ?>">&lsaquo;</a>
                  </li>
                  <li class="page-item disabled"><span class="page-link"><?= $page ?> / <?= $totalPages ?></span></li>
                  <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= min($totalPages,$page+1) ?>">&rsaquo;</a>
                  </li>
                  <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                    <a class="page-link" href="?page=<?= $totalPages ?>">&raquo;</a>
                  </li>
                </ul>
              </nav>
            </div>

          </div>
        </div>

        <!-- Ensayos en Repetición -->
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
     <?php
/* ===============================
   CONTEOS GLOBALES “ACTUALES” POR PROCESO
   - Exclusión por prioridad: Repeat > Review(sin firmar) > Delivery > Realization > Preparation
   - Independiente de la paginación
   =============================== */

/* Helpers */
if (!function_exists('normalize_key')) {
  function normalize_key($sid,$num,$tt){
    return strtoupper(trim((string)$sid)).'|'.strtoupper(trim((string)$num)).'|'.strtoupper(trim((string)$tt));
  }
}
if (!function_exists('set_minus')) {
  function set_minus(array $A, array $B): array {
    foreach ($B as $k=>$_) { if (isset($A[$k])) unset($A[$k]); }
    return $A;
  }
}

/* Si quisieras limitar por tiempo, descomenta y configura:
$FROM_SQL = " AND `Start_Date` >= DATE_SUB(NOW(), INTERVAL 90 DAY) ";
*/
// Por defecto, SIN filtro de fecha (todos los registros):
$FROM_SQL = "";

/* Obtiene un set (distinct triples) desde una tabla */
function table_set_distinct(string $table, string $dateFilter = ""): array {
  // Nota: si tu columna de fecha se llama distinto, cambia `Start_Date` en $dateFilter
  $rows = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type
    FROM `{$table}`
    WHERE 1=1 {$dateFilter}
    GROUP BY Sample_ID, Sample_Number, Test_Type
  ");
  $set = [];
  foreach ($rows as $r) {
    $k = normalize_key($r['Sample_ID'] ?? '', $r['Sample_Number'] ?? '', $r['Test_Type'] ?? '');
    if ($k !== '||') $set[$k] = true;
  }
  return $set;
}

/* Review sin firmar: test_review EXCLUYENDO los que aparecen en test_reviewed */
function review_unreviewed_set(string $dateFilter = ""): array {
  // $dateFilter debe referirse a la columna de fecha de test_review; ajusta si usas otra columna
  $rows = find_by_sql("
    SELECT p.Sample_ID, p.Sample_Number, p.Test_Type
    FROM test_review p
    LEFT JOIN test_reviewed r
      ON r.Sample_ID=p.Sample_ID AND r.Sample_Number=p.Sample_Number AND r.Test_Type=p.Test_Type
    WHERE r.Sample_ID IS NULL {$dateFilter}
    GROUP BY p.Sample_ID, p.Sample_Number, p.Test_Type
  ");
  $set = [];
  foreach ($rows as $r) {
    $k = normalize_key($r['Sample_ID'] ?? '', $r['Sample_Number'] ?? '', $r['Test_Type'] ?? '');
    if ($k !== '||') $set[$k] = true;
  }
  return $set;
}

/* Cargar sets base (puedes cambiar Start_Date por tu columna real si lo necesitas en $FROM_SQL) */
$Sprep = table_set_distinct('test_preparation', $FROM_SQL);            // Preparation
$Sreal = table_set_distinct('test_realization', $FROM_SQL);            // Realization
$Sdelv = table_set_distinct('test_delivery',    str_replace('Start_Date','Register_Date',$FROM_SQL)); // Delivery (usa Register_Date si aplica)
$Srept = table_set_distinct('test_repeat',      $FROM_SQL);            // Repeat
$Srev  = review_unreviewed_set($FROM_SQL);                             // Review sin firmar

/* Exclusión por prioridad para que cada muestra cuente en UN solo estado “actual” */
$CURrepeat = $Srept;

$CURreview = set_minus($Srev,  $CURrepeat);

$CURdelivery = set_minus($Sdelv, $CURreview + $CURrepeat);

$CURreal = set_minus($Sreal,   $CURdelivery + $CURreview + $CURrepeat);

$CURprep = set_minus($Sprep,   $CURreal + $CURdelivery + $CURreview + $CURrepeat);

/* Conteos finales */
$cntPreparation = count($CURprep);
$cntRealization = count($CURreal);
$cntDelivery    = count($CURdelivery);
$cntReview      = count($CURreview);
$cntRepeat      = count($CURrepeat);

/* (Opcional) Pendiente lógico global:
   Ensayos solicitados (de todas las requisiciones con Test_Type) que NO aparecen en ningún estado actual
*/
$reqAll = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type
  FROM lab_test_requisition_form
  WHERE Test_Type IS NOT NULL AND Test_Type <> ''
");
$requested = [];
foreach ($reqAll as $rq) {
  $sid = $rq['Sample_ID'] ?? ''; $num = $rq['Sample_Number'] ?? '';
  $types = array_filter(array_map('trim', explode(',', $rq['Test_Type'] ?? '')));
  foreach ($types as $t) {
    $requested[ normalize_key($sid,$num,$t) ] = true;
  }
}
$inAnyCurrent = $CURprep + $CURreal + $CURdelivery + $CURreview + $CURrepeat;
$pendingLogical = 0;
foreach ($requested as $k=>$_) { if (!isset($inAnyCurrent[$k])) $pendingLogical++; }

// Pendientes globales por tipo
$pendingByType = [];
foreach ($requested as $k => $_) {
  if (!isset($inAnyCurrent[$k])) {
    // $k = SID|NUM|TT
    $parts = explode('|', $k);
    $tt = $parts[2] ?? '';
    if ($tt !== '') $pendingByType[$tt] = ($pendingByType[$tt] ?? 0) + 1;
  }
}

?>

<!-- Derecha -->
<div class="col-lg-4">

  <!-- Conteos globales (estado actual) -->
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Cantidades en Procesos</h5>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Preparación
          <span class="badge bg-primary rounded-pill"><?= (int)$cntPreparation ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Realización
          <span class="badge bg-secondary rounded-pill"><?= (int)$cntRealization ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Entrega
          <span class="badge bg-success rounded-pill"><?= (int)$cntDelivery ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Revisión 
          <span class="badge bg-dark rounded-pill"><?= (int)$cntReview ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Repetición
          <span class="badge bg-warning rounded-pill"><?= (int)$cntRepeat ?></span>
        </li>
      
      </ul>
    </div>
  </div>

  <!-- Pendientes globales por tipo -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Ensayos Pendiente de Realizar</h5>
      <ul class="list-group">
        <?php if (empty($pendingByType)): ?>
          <li class="list-group-item">✅ No hay ensayos pendientes</li>
        <?php else: ?>
          <?php foreach ($pendingByType as $tt => $cnt): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <code><?= htmlspecialchars($tt) ?></code>
              <span class="badge bg-danger rounded-pill"><?= (int)$cnt ?></span>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>

</div>
<!-- /Derecha -->


      <!-- /Derecha -->

    </div>
  </section>
</main>
<?php include_once('../components/footer.php'); ?>
