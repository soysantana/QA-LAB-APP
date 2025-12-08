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

/* ============================================================
   CONFIGURACIÓN DEL ALGORITMO AVANZADO DE PRIORIDADES
   ============================================================ */

/* SLA por tipo de ensayo (en días) */
$SLA = [
  'SP'  => 3,
  'SND' => 7,
  'GS'  => 2,
  'GS' => 2,
  'HY' => 4,
  'MC'  => 1,
  'AL'  => 2,
  'SG'  => 2,
];

/* Peso por tipo de ensayo */
$PESO_TIPO = [
  'SP'  => 3,
  'SND' => 3,
  'GS'  => 2,
  'GS' => 2,
  'HY' => 2,
  'MC'  => 1,
  'AL'  => 1,
];

/* Clientes VIP o críticos */
$CLIENTES_VIP = ['TSF-Llagal','PV-Project','Capital-Project','TSF-Naranjo','MRM'];

/* Peso por estado */
$PESO_ESTADO = [
  'Preparación' => 1,
  'Realización' => 2,
  'Entrega'     => 1,
];

/* Penalizaciones por complejidad */
$PESO_MANIPULACION = [
  'muchos_tecnicos'   => 1,   // >3 técnicos
  'muchos_movimientos'=> 2,   // >5 movimientos
  'saltos_estado'     => 1,   // >3 estados en 24h
];

/* ============================================================
   PRIORIDAD AVANZADA DEL DÍA — ALGORITMO INTELIGENTE
   ============================================================ */

$priorityRows = find_by_sql("
  SELECT
    w.Sample_ID,
    w.Sample_Number,
    w.Test_Type AS Test_Type,
    w.Status,
    w.Process_Started,
    TIMESTAMPDIFF(HOUR, w.Process_Started, NOW())/24.0 AS dias,
    COALESCE(r.Client, '') AS Client,
    (
        TIMESTAMPDIFF(HOUR, w.Process_Started, NOW())/24.0
        + CASE 
            WHEN w.Status='Realización' THEN 2
            WHEN w.Status='Preparación' THEN 1
            ELSE 0
          END
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
  WHERE w.Status IN ('Preparación','Realización')
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
<main id="main" class="main dashboard">

  <!-- HERO HEADER -->
  <header class="hero">
    <div class="hero-left">
      <h1>Dashboard General</h1>
      <p class="hero-subtitle">Monitoreo en tiempo real del laboratorio</p>
    </div>

    <div class="hero-date">
      <?= date("l, d F Y") ?>
    </div>
  </header>

  <!-- KPI CARDS -->
  <section class="kpi-section">
    <?php foreach ($kpiStates as $st): ?>
      <div class="kpi-card">
        <div class="kpi-label"><?= h($st) ?></div>
        <div class="kpi-value" data-value="<?= (int)$kpis[$st] ?>">
          <?= (int)$kpis[$st] ?>
        </div>
        <div class="kpi-footer">
          <?php if ($st=='Registrado'): ?>Últimos 3 meses<?php endif; ?>
          <?php if ($st=='Revisado'): ?>Semana actual<?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <!-- MAIN GRID -->
  <section class="grid-two">

    <!-- PROCESS STREAM -->
    <div class="panel glass">
      <div class="panel-header">
        <h2>Proceso de Muestreo</h2>
      </div>

      <div class="stream">
        <?php if (empty($WF)): ?>
          <div class="stream-empty">
            <i class="bi bi-check-circle"></i>
            No hay ensayos en el flujo actualmente.
          </div>
        <?php else: foreach ($WF as $r): ?>
          <div class="stream-item hover-lift">
            <div class="stream-main">
              <div class="stream-id">
                <?= h($r['Sample_ID']) ?> <span>#<?= h($r['Sample_Number']) ?></span>
              </div>

              <div class="stream-tags">
                <span class="tag test"><?= h($r['Test_Type']) ?></span>
                <span class="tag status status-<?= strtolower($r['Status']) ?>">
                  <?= h($r['Status']) ?>
                </span>
              </div>
            </div>

            <div class="stream-meta">
              <div>
                <span class="meta-title">Desde</span>
                <span><?= h(date("Y-m-d", strtotime($r['Process_Started']))) ?></span>
              </div>
              <div>
                <span class="meta-title">Técnico(s)</span>
                <span><?= h($r['Techs'] ?: $r['Updated_By'] ?: '—') ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

<!-- PRIORIDAD DEL DÍA (Diseño Moderno Premium) -->
<div class="panel priority-panel glass">

  <div class="panel-header">
    <h2> Prioridad del Día — Top 15</h2>
    <small class="sub">
      Ordenado automáticamente por urgencia, antigüedad y criticidad del ensayo
    </small>
  </div>

  <?php if (empty($priorityRows)): ?>

    <div class="empty-state-modern">
      <div class="icon">✔️</div>
      <h3>Laboratorio al día</h3>
      <p>No hay ensayos con prioridad especial.</p>
    </div>

  <?php else: ?>

    <div class="priority-container">
      <?php $rank = 1; foreach ($priorityRows as $row): ?>

        <?php
          // Determinación de clase de urgencia
          $dias = (float)$row['dias'];
          $urg = ($dias >= 5) ? "urgent"
               : ($dias >= 2 ? "medium" : "low");
        ?>

        <div class="priority-card <?= $urg ?> hover-lift">

          <!-- Ranking destacado -->
          <div class="priority-rank">
            <span>#<?= $rank++ ?></span>
          </div>

          <!-- Información principal -->
          <div class="priority-main">
            <div class="title">
              <?= h($row['Sample_ID'].'-'.$row['Sample_Number']) ?>
            </div>

            <div class="labels">
              <span class="tag test"><?= h($row['Test_Type']) ?></span>

              <span class="tag status-<?= strtolower($row['Status']) ?>">
                <?= h($row['Status']) ?>
              </span>
            </div>
          </div>

          <!-- Metadata -->
          <div class="priority-meta">
            <div class="days">
              <strong><?= number_format($dias,1,'.','') ?></strong>
              <small>días</small>
            </div>

            <div class="client">
              <small><?= h($row['Client'] ?: "Sin cliente") ?></small>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>


  <!-- SECOND GRID -->
  <section class="grid-two">

    <!-- REPETICIÓN -->
    <div class="panel glass">
      <h2>Muestras en Repetición · Últimos 7 días</h2>

      <?php if (empty($repeatRows)): ?>
        <div class="repeat-ok">
          <i class="bi bi-check-circle-fill"></i>
          <p>No hay ensayos en repetición.</p>
        </div>

      <?php else: ?>
        <div class="repeat-alert pulse">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span><?= count($repeatRows) ?> requieren atención</span>
        </div>

        <div class="repeat-list">
          <?php foreach ($repeatRows as $r): ?>
            <div class="repeat-item hover-lift">
              <strong><?= h($r['Sample_ID'].'-'.$r['Sample_Number']) ?></strong>
              <span class="tag test"><?= h($r['Test_Type']) ?></span>
              <small><?= date("Y-m-d", strtotime($r['SD'])) ?> — <?= h($r['Send_By']) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PROCTOR -->
    <div class="panel glass">
      <h2>Método para Compactación · 31 días</h2>

      <?php if (empty($spRows)): ?>
        <div class="empty-state">
          Sin muestras SP pendientes.
        </div>
      <?php else: ?>
        <div class="sp-list">
          <?php foreach ($spRows as $r): ?>
            <div class="sp-item hover-lift">
              <strong><?= h($r['sid'].'-'.$r['num']) ?></strong>
              <span class="tag method"><?= h($r['met']) ?></span>
              <small><?= h($r['note']) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </section>

</main>

<style>
/* HERO */
.hero {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 0 10px;
}

.hero h1 {
  font-size:2rem;
  font-weight:700;
}

.hero-subtitle {
  color:#6b7280;
  margin-top:4px;
}

.hero-date {
  font-size:14px;
  color:#6b7280;
  font-weight:500;
}

/* KPI SECTION */
.kpi-section {
  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:12px;
  margin-bottom:24px;
}

.kpi-card {
  background:rgba(255,255,255,0.55);
  backdrop-filter:blur(12px);
  border-radius:16px;
  padding:16px;
  border:1px solid #e5e7eb;
  box-shadow:0 4px 12px rgba(0,0,0,0.04);
  transition:transform .2s;
}

.kpi-card:hover {
  transform:translateY(-4px);
}

.kpi-label { font-size:13px; color:#6b7280; }
.kpi-value { font-size:32px; font-weight:700; margin-top:4px; }
.kpi-footer { font-size:11px; color:#9ca3af; margin-top:6px; }

/* PANELS */
.panel {
  background:rgba(255,255,255,0.6);
  padding:20px;
  border-radius:16px;
  border:1px solid #e5e7eb;
  box-shadow:0 4px 16px rgba(0,0,0,0.05);
}

.glass {
  backdrop-filter:blur(12px);
}

/* GRID */
.grid-two {
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
  margin-bottom:20px;
}

/* PROCESS STREAM */
.stream {
  display:flex;
  flex-direction:column;
  gap:10px;
}

.stream-item {
  background:#fff;
  border-radius:12px;
  padding:15px;
  border:1px solid #e5e7eb;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.hover-lift {
  transition:transform .15s, box-shadow .15s;
}

.hover-lift:hover {
  transform:translateY(-4px);
  box-shadow:0 6px 18px rgba(0,0,0,0.1);
}

.stream-id {
  font-size:15px;
  font-weight:600;
}

.tag {
  padding:4px 10px;
  border-radius:8px;
  font-size:11px;
  font-weight:600;
}

.test { background:#eef2ff; color:#4f46e5; }
.method { background:#e0f2fe; color:#0284c7; }

/* STATUS TAG COLORS */
.status-registrado { background:#e0f2fe; color:#0369a1; }
.status-preparación { background:#f3e8ff; color:#9333ea; }
.status-realización { background:#fff7ed; color:#c2410c; }
.status-entrega { background:#ecfdf5; color:#059669; }
.status-revisado { background:#f1f5f9; color:#374151; }

/* PRIORIDAD LIST */
.priority-list {
  display:flex;
  flex-direction:column;
  gap:10px;
}

.priority-item {
  background:#fff;
  padding:12px 14px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.priority-rank {
  font-size:20px;
  font-weight:700;
  color:#6b7280;
}

/* REPETICIÓN */
.repeat-alert {
  background:#fee2e2;
  padding:12px;
  border-radius:10px;
  color:#b91c1c;
  font-size:14px;
  font-weight:600;
  display:flex;
  align-items:center;
  gap:10px;
}

.pulse { animation:pulseAnim 1.5s infinite; }

@keyframes pulseAnim {
  0% { transform:scale(1); opacity:.8; }
  50% { transform:scale(1.05); opacity:1; }
  100% { transform:scale(1); opacity:.8; }
}

/* SP LIST */
.sp-list, .repeat-list {
  display:flex;
  flex-direction:column;
  gap:10px;
}

.sp-item, .repeat-item {
  background:#fff;
  padding:12px;
  border-radius:12px;
  border:1px solid #e5e7eb;
}

/* EMPTY STATE */
.empty-state {
  text-align:center;
  color:#9ca3af;
  padding:20px 0;
}

/* PANEL */
.priority-panel {
  padding: 18px;
  background: rgba(255,255,255,0.75);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  border: 1px solid rgba(200,200,200,0.4);
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

/* HEADER */
.priority-panel .panel-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
}
.priority-panel .panel-header .sub {
  color: #64748b;
}

/* VACÍO */
.empty-state-modern {
  text-align: center;
  padding: 40px;
}
.empty-state-modern .icon {
  font-size: 2.5rem;
  margin-bottom: 10px;
}
.empty-state-modern h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #1e293b;
}
.empty-state-modern p {
  color: #64748b;
  font-size: 0.9rem;
}

/* LISTA */
.priority-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* CARD */
.priority-card {
  display: grid;
  grid-template-columns: 60px 1fr 90px;
  align-items: center;
  padding: 12px 16px;
  border-radius: 14px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  transition: transform .2s, box-shadow .2s;
}

/* Hover */
.hover-lift:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.07);
}

/* URGENCIA */
.priority-card.low {
  border-left: 5px solid #16a34a;
}
.priority-card.medium {
  border-left: 5px solid #f59e0b;
}
.priority-card.urgent {
  border-left: 5px solid #dc2626;
}

/* RANK */
.priority-rank span {
  font-size: 1.3rem;
  font-weight: 600;
  color: #475569;
}

/* MAIN INFO */
.priority-main .title {
  font-size: 1rem;
  font-weight: 600;
  color: #0f172a;
}
.labels {
  margin-top: 4px;
  display: flex;
  gap: 6px;
}

/* TAGS */
.tag {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 0.75rem;
  background: #f1f5f9;
  color: #0f172a;
  border: 1px solid #e2e8f0;
}

.status-registrado { background: #e2e8f0; }
.status-preparación { background: #dbeafe; color:#1d4ed8; }
.status-realización { background: #fef9c3; color:#ca8a04; }
.status-entrega { background:#dcfce7; color:#15803d; }

/* META */
.priority-meta {
  text-align: right;
}
.priority-meta .days strong {
  font-size: 1.2rem;
  color: #0f172a;
}

/* RESPONSIVE */
@media (max-width: 768px){
  .priority-card {
    grid-template-columns: 40px 1fr;
    grid-template-rows: auto auto;
    gap: 6px;
  }
  .priority-meta {
    grid-column: span 2;
    text-align: left;
  }
}


</style>

<?php include_once('../components/footer.php'); ?>
