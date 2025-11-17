<?php
// /pages/home.php — Panel General
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

/* ==============================
   Helpers
   ============================== */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function N($v){ return strtoupper(trim((string)$v)); }
function K($sid,$num,$tt){ return N($sid).'|'.N($num).'|'.N($tt); }

function status_badge($s){
  switch ($s) {
    case 'Registrado':  return 'secondary';
    case 'Preparación': return 'primary';
    case 'Realización': return 'info';
    case 'Entrega':     return 'success';
    case 'Revisado':    return 'dark';
    default:            return 'light';
  }
}

// ¿Existe registro en tabla X?
function exists_in(string $table, string $sid, string $num, string $tt): bool {
  global $db;
  $sidE = $db->escape($sid);
  $numE = $db->escape($num);
  $ttE  = $db->escape($tt);
  $sql  = "SELECT 1 FROM `{$table}` 
           WHERE Sample_ID='{$sidE}' 
             AND Sample_Number='{$numE}' 
             AND UPPER(TRIM(Test_Type))='{$ttE}' 
           LIMIT 1";
  $res  = $db->query($sql);
  return $res && $res->num_rows>0;
}

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
function pick_date_col(string $table, array $candidates = [
  'Start_Date','Delivery_Date','Register_Date','Registed_Date',
  'Created_At','CreatedAt','Date','Updated_At'
]){
  foreach ($candidates as $col) {
    if (col_exists($table, $col)) return $col;
  }
  return null;
}
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
    'src'  => $table,
    't34'  => (float)($r['t34']??0),
    't38'  => (float)($r['t38']??0),
    'tNo4' => (float)($r['tNo4']??0),
  ];
}

/* ==============================
   Ventanas de tiempo
   ============================== */
$FROM_90 = date('Y-m-d', strtotime('-90 days'));   // últimos 3 meses
$FROM_7  = date('Y-m-d', strtotime('-7 days'));    // repetición 7 días
$FROM_31 = date('Y-m-d', strtotime('-31 days'));   // Proctor 31 días

/* ==============================
   KPIs simples
   ============================== */
$kpiStates = ['Registrado','Preparación','Realización','Entrega','Revisado'];
$kpis = array_fill_keys($kpiStates, 0);

// Registrado (últimos 3 meses en test_workflow)
$rowR = find_by_sql("
  SELECT COUNT(*) c
  FROM test_workflow
  WHERE Status='Registrado'
    AND Process_Started >= '{$FROM_90}'
");
$kpis['Registrado'] = (int)($rowR[0]['c'] ?? 0);

// Preparación / Realización / Entrega: totales actuales
$grp = find_by_sql("
  SELECT Status, COUNT(*) c
  FROM test_workflow
  WHERE Status IN ('Preparación','Realización','Entrega')
  GROUP BY Status
");
foreach ($grp as $r) {
  $s = $r['Status'] ?? '';
  if (isset($kpis[$s])) $kpis[$s] = (int)$r['c'];
}

// Revisado: DISTINCT triples en test_reviewed (semana ISO actual)
$revDateCol = pick_date_col('test_reviewed', [
  'Reviewed_Date',
  'Review_Date',
  'Registed_Date',
  'Register_Date',
  'Start_Date',
  'Date',
  'Updated_At'
]);

if ($revDateCol) {
  $rowRev = find_by_sql("
    SELECT COUNT(DISTINCT CONCAT(
      COALESCE(Sample_ID,''),'|',
      COALESCE(Sample_Number,''),'|',
      UPPER(TRIM(COALESCE(Test_Type,'')))
    )) AS c
    FROM test_reviewed
    WHERE YEARWEEK(`{$revDateCol}`, 1) = YEARWEEK(CURDATE(), 1)
  ");
  $kpis['Revisado'] = (int)($rowRev[0]['c'] ?? 0);
} else {
  $kpis['Revisado'] = 0;
}

/* ==============================
   Paginación — Workflow (proceso de muestreo)
   ============================== */
$PAGE_SIZE = 12;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $PAGE_SIZE;

// Conteo total en workflow
$row = find_by_sql("SELECT COUNT(*) c FROM test_workflow");
$total = (int)($row[0]['c'] ?? 0);
$totalPages = max(1, (int)ceil($total / $PAGE_SIZE));

// Bloque para la tabla principal
$WF = find_by_sql("
  SELECT
    w.id,
    w.Sample_ID,
    w.Sample_Number,
    w.Test_Type,
    w.Status,
    w.Process_Started,
    w.Updated_By,
    TIMESTAMPDIFF(HOUR, w.Process_Started, NOW()) AS Dwell_Hours,
    COALESCE(agg.techs, '') AS Techs
  FROM test_workflow AS w
  LEFT JOIN (
    SELECT
      a.test_id,
      GROUP_CONCAT(
        DISTINCT TRIM(t.Technician)
        ORDER BY TRIM(t.Technician)
        SEPARATOR ', '
      ) AS techs
    FROM test_activity a
    LEFT JOIN test_activity_technician t
      ON t.activity_id = a.id
    GROUP BY a.test_id
  ) AS agg
    ON agg.test_id = w.id
  ORDER BY w.Updated_At DESC
  LIMIT {$PAGE_SIZE} OFFSET {$offset}
");

// SLA de referencia (ya no se muestra, solo si quisieras usarlo luego)
$SLA = ['Registrado'=>24,'Preparación'=>48,'Realización'=>72,'Entrega'=>24,'Revisado'=>24];
function sla_for_local($s){ global $SLA; return (int)($SLA[$s] ?? 48); }

/* ==============================
   PRIORIDAD DEL DÍA (Top 15)
   - SOLO Preparación y Realización
   ============================== */

$priorityRows = find_by_sql("
  SELECT
    w.Sample_ID,
    w.Sample_Number,
    w.Test_Type,
    w.Status,
    w.Process_Started,
    -- días en proceso (en decimales)
    TIMESTAMPDIFF(HOUR, w.Process_Started, NOW())/24.0 AS dias,
    COALESCE(r.Client, '') AS Client,
    (
      -- antigüedad
      TIMESTAMPDIFF(HOUR, w.Process_Started, NOW())/24.0
      -- peso por estado
      + CASE 
          WHEN w.Status='Realización' THEN 2
          WHEN w.Status='Preparación' THEN 1
          ELSE 0
        END
      -- peso por tipo de ensayo
      + CASE
          WHEN UPPER(TRIM(w.Test_Type)) IN ('SP','CBR') THEN 2
          WHEN UPPER(TRIM(w.Test_Type)) IN ('GS','GSF','GSC') THEN 1
          ELSE 0
        END
    ) AS score
  FROM test_workflow w
  LEFT JOIN lab_test_requisition_form r
    ON r.Sample_ID = w.Sample_ID
   AND r.Sample_Number = w.Sample_Number
  WHERE w.Status IN ('Preparación','Realización')   -- 👈 AQUÍ EL CAMBIO
    AND w.Process_Started >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
  ORDER BY score DESC
  LIMIT 15
");


/* ==============================
   Repetición (últimos 7 días)
   ============================== */
$repDateCol = pick_date_col('test_repeat') ?: 'Start_Date';
$repeatRows = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, `{$repDateCol}` AS SD, Send_By
  FROM test_repeat
  WHERE `{$repDateCol}` >= '{$FROM_7}'
  ORDER BY `{$repDateCol}` DESC
  LIMIT 200
");

/* ==============================
   Método Proctor (SP) — últimos 31 días (pendientes)
   ============================== */
$ReqSP = find_by_sql("
  SELECT Sample_ID, Sample_Number, Sample_Date, Test_Type
  FROM lab_test_requisition_form
  WHERE Registed_Date >= '{$FROM_31}'
    AND Test_Type IS NOT NULL AND Test_Type <> ''
  ORDER BY Registed_Date DESC
  LIMIT 400
");
$spRows = [];
foreach ($ReqSP as $req) {
  $sid = $req['Sample_ID']; 
  $num = $req['Sample_Number'];
  $types = array_filter(array_map('trim', explode(',', (string)$req['Test_Type'])));
  $hasSP = false;
  foreach ($types as $t) { if (N($t)==='SP'){ $hasSP=true; break; } }
  if (!$hasSP) continue;

  // SP pendiente: no debe estar en Preparación / Realización / Entrega
  $isInCore = (
    exists_in('test_preparation',$sid,$num,'SP') ||
    exists_in('test_realization',$sid,$num,'SP') ||
    exists_in('test_delivery',   $sid,$num,'SP')
  );
  if ($isInCore) continue;

  // Buscar tripletas
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
    $spRows[] = [
      'sid'=>$sid,
      'num'=>$num,
      'met'=>'No data',
      'note'=>'Sin granulometría válida o columnas/tabla no existen'
    ];
  }
}
?>
<main id="main" class="main" style="padding:18px;">
  <div class="pagetitle">
    <h1>Panel General</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item active">Panel</li>
      </ol>
    </nav>
  </div>

  <?= display_msg($msg); ?>

  <!-- KPIs -->
  <section class="kpi-grid">
    <?php foreach ($kpiStates as $st): ?>
      <div class="kpi">
        <div class="kpi-title"><?= h($st) ?></div>
        <div class="kpi-val"><?= (int)$kpis[$st] ?></div>
        <?php if ($st==='Registrado'): ?>
          <div class="kpi-sub">últ. 3 meses</div>
        <?php elseif ($st==='Revisado'): ?>
          <div class="kpi-sub">semana actual</div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </section>

  <!-- Grid principal: Proceso + Prioridad del día -->
  <div class="grid-2">
    <!-- Proceso de muestreo (paginado, de test_workflow) -->
    <section class="card">
      <div class="card-title d-flex justify-content-between align-items-center">
        <span>Proceso de muestreo</span>
        <nav aria-label="Pag" class="d-none d-md-block">
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?= $page<=1?'disabled':'' ?>">
              <a class="page-link" href="?page=1">&laquo;</a>
            </li>
            <li class="page-item <?= $page<=1?'disabled':'' ?>">
              <a class="page-link" href="?page=<?= max(1,$page-1) ?>">&lsaquo;</a>
            </li>
            <li class="page-item disabled">
              <span class="page-link"><?= $page ?> / <?= $totalPages ?></span>
            </li>
            <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
              <a class="page-link" href="?page=<?= min($totalPages,$page+1) ?>">&rsaquo;</a>
            </li>
            <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
              <a class="page-link" href="?page=<?= $totalPages ?>">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>Sample ID</th>
              <th>#</th>
              <th>Ensayo</th>
              <th>Estado</th>
              <th>Desde</th>
              <th>Por</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($WF)): ?>
              <tr><td colspan="6" class="text-center text-muted">No hay datos.</td></tr>
            <?php else: foreach ($WF as $r): ?>
              <tr>
                <td><?= h($r['Sample_ID']) ?></td>
                <td><?= h($r['Sample_Number'] ?? '') ?></td>
                <td><span class="pill"><?= h($r['Test_Type']) ?></span></td>
                <td>
                  <span class="badge bg-<?= status_badge($r['Status']) ?>">
                    <?= h($r['Status']) ?>
                  </span>
                </td>
                <td><?= h($r['Process_Started']) ?></td>
                <td>
                  <?php
                    $names = [];
                    if (!empty($r['Techs'])) {
                      $names[] = $r['Techs'];
                    }
                    if (!empty($r['Updated_By'])) {
                      if (empty($r['Techs']) || stripos($r['Techs'], $r['Updated_By']) === false) {
                        $names[] = $r['Updated_By'];
                      }
                    }
                    echo h($names ? implode(' | ', $names) : '—');
                  ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Prioridad del día (Top 15 ensayos) -->
    <section class="card card-metric">
      <div class="card-metric-header">
        <div>
          <div class="card-metric-eyebrow">Órden sugerido</div>
          <h2 class="card-metric-title">Prioridad del día · Top 15 ensayos</h2>
        </div>
        <div class="card-metric-chip">
          <?= count($priorityRows) ?> activo<?= count($priorityRows) === 1 ? '' : 's' ?>
        </div>
      </div>

      <div class="card-metric-body">
        <p class="text-muted small mb-3">
          Lista ordenada por antigüedad, estado del proceso y tipo de ensayo (SP/CBR/GS con mayor peso).
        </p>

        <div class="table-wrap-tight">
          <table class="tbl tbl-modern">
            <thead>
              <tr>
                <th>#</th>
                <th>Muestra</th>
                <th>Ensayo</th>
                <th>Estado</th>
                <th>Días en proceso</th>
                <th>Cliente</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($priorityRows)): ?>
                <tr>
                  <td colspan="6" class="text-center py-3">
                    <span class="empty-icon">✅</span>
                    <div class="empty-title">Sin ensayos con prioridad especial</div>
                    <div class="empty-subtitle">No hay ensayos activos en Registrado/Preparación/Realización en los últimos 90 días.</div>
                  </td>
                </tr>
              <?php else: ?>
                <?php $idx = 1; foreach ($priorityRows as $row): ?>
                  <tr>
                    <td><?= $idx++; ?></td>
                    <td><?= h($row['Sample_ID'].'-'.$row['Sample_Number']) ?></td>
                    <td><span class="pill"><code><?= h($row['Test_Type']) ?></code></span></td>
                    <td>
                      <span class="badge bg-<?= status_badge($row['Status']) ?>">
                        <?= h($row['Status']) ?>
                      </span>
                    </td>
                    <td>
                      <?php
                        $dias = (float)$row['dias'];
                        // Mostrar con 1 decimal
                        echo number_format($dias, 1, '.', '').' d';
                      ?>
                    </td>
                    <td><?= h($row['Client']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>

  <!-- Grid secundario: Repetición y Proctor -->
  <div class="grid-2">
    <!-- Repetición (últimos 7 días) -->
    <section class="card">
      <div class="card-title">
        Muestras en repetición <span class="text-muted">| Últimos 7 días</span>
      </div>
      <div class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>Sample ID</th>
              <th>#</th>
              <th>Test</th>
              <th>Fecha</th>
              <th>Enviado por</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($repeatRows)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">
                  No hay ensayos en repetición recientes.
                </td>
              </tr>
            <?php else: foreach ($repeatRows as $r): ?>
              <tr>
                <td><?= h($r['Sample_ID']) ?></td>
                <td><?= h($r['Sample_Number']) ?></td>
                <td><span class="pill"><?= h($r['Test_Type']) ?></span></td>
                <td><?= h(date('Y-m-d', strtotime((string)$r['SD']))) ?></td>
                <td><?= h($r['Send_By']) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Proctor (SP) -->
    <section class="card">
      <div class="card-title">
        Método para Compactación <span class="text-muted">| Últimos 31 días</span>
      </div>
      <div class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>Muestra</th>
              <th>Método</th>
              <th>Comentario</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($spRows)): ?>
              <tr>
                <td colspan="3" class="text-center text-muted">
                  Sin muestras SP pendientes o sin granulometría.
                </td>
              </tr>
            <?php else: foreach ($spRows as $r): ?>
              <tr>
                <td><?= h($r['sid'].'-'.$r['num'].'-SP') ?></td>
                <td><?= h($r['met']) ?></td>
                <td><?= h($r['note']) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>

<style>
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 12px;
  }
  .kpi {
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:12px;
    box-shadow:0 1px 3px rgba(0,0,0,.04);
  }
  .kpi .kpi-title{ font-size:12px; color:#666; }
  .kpi .kpi-val{ font-size:28px; font-weight:700; }
  .kpi .kpi-sub{ font-size:11px; color:#64748b; margin-top:4px; }

  .grid-2{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
    margin-bottom:12px;
  }

  .card{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:12px;
    box-shadow:0 1px 3px rgba(0,0,0,.04);
  }
  .card-title{
    font-weight:600;
    margin-bottom:8px;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .table-wrap{ overflow:auto; }
  .tbl{ width:100%; border-collapse:collapse; }
  .tbl th, .tbl td{
    border:1px solid #eee;
    padding:6px 8px;
    font-size:13px;
  }
  .tbl th{ background:#f8fafc; text-align:left; }

  .pill{
    display:inline-flex;
    align-items:center;
    padding:0.15rem 0.5rem;
    border-radius:999px;
    font-size:0.78rem;
    background:#f1f5f9;
    color:#0f172a;
    border:1px solid #e5e7eb;
  }

  @media (max-width: 1200px){
    .kpi-grid{ grid-template-columns: repeat(3, 1fr);}
    .grid-2{ grid-template-columns:1fr; }
  }

  /* Estilos de la card-metric y tabla moderna */
  .card-metric {
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #ffffff;
  }
  .card-metric-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
  }
  .card-metric-eyebrow {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    margin-bottom: 0.15rem;
  }
  .card-metric-title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }
  .card-metric-chip {
    font-size: 0.8rem;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid rgba(59, 130, 246, 0.35);
    white-space: nowrap;
  }
  .card-metric-body { margin-top: 0.25rem; }

  .table-wrap-tight {
    max-height: 260px;
    overflow-y: auto;
  }
  .tbl-modern {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }
  .tbl-modern thead th {
    font-weight: 600;
    font-size: 0.8rem;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    padding: 0.4rem 0.25rem;
  }
  .tbl-modern tbody tr:nth-child(even) {
    background-color: #f9fafb;
  }
  .tbl-modern tbody td {
    padding: 0.4rem 0.25rem;
    vertical-align: middle;
  }

  .text-right { text-align: right; }
  .text-center { text-align: center; }

  .badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.8rem;
    padding: 0.15rem 0.4rem;
    border-radius: 999px;
    background: #eefdf4;
    color: #15803d;
    font-weight: 600;
    font-size: 0.78rem;
  }
  .empty-icon {
    font-size: 1.3rem;
    display: block;
    margin-bottom: 0.25rem;
  }
  .empty-title {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.9rem;
  }
  .empty-subtitle {
    font-size: 0.78rem;
    color: #64748b;
  }
</style>

<?php include_once('../components/footer.php'); ?>
