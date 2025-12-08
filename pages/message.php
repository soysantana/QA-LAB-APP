<?php
$page_title = 'Message & Notify';
require_once('../config/load.php');
page_require_level(2);

// ========================================
// UTILIDADES
// ========================================
function norm($v) { return strtoupper(trim((string)$v)); }

/* Resolver ruta según Test Type */
function route_for_test_type(string $rawType): ?string {
  $t = norm($rawType);
  $t = str_replace('-', '_', $t);

  $map = [
    'AL' => '../reviews/atterberg-limit.php',
    'BTS' => '../reviews/brazilian.php',
    'HY' => '../reviews/hydrometer.php',
    'DHY' => '../reviews/double-hydrometer.php',
    'PLT' => '../reviews/point-load.php',
    'SND' => '../reviews/soundness.php',
    'SP'  => '../reviews/standard-proctor.php',
    'UCS' => '../reviews/unixial-compressive.php',

    'MC_OVEN' => '../reviews/moisture-oven.php',
    'MC_MICROWAVE' => '../reviews/moisture-microwave.php',
    'MC_CONSTANT_MASS' => '../reviews/moisture-constant-mass.php',
    'MC_SCALE' => '../reviews/moisture-scale.php',

    'LAA_LARGE' => '../reviews/laa-large.php',
    'LAA_SMALL' => '../reviews/laa-small.php',

    'SG' => '../reviews/specific-gravity.php',
    'SG_COARSE' => '../reviews/specific-gravity-coarse-aggregates.php',
    'SG_FINE' => '../reviews/specific-gravity-fine-aggregate.php',

    'SCT' => '../reviews/sand-castle-test.php',

    'GS' => '../reviews/grain-size.php',
    'GS_FF' => '../reviews/grain-size-fine-filter.php',
    'GS_CF' => '../reviews/grain-size-coarse-filter.php',
    'GS_LPF' => '../reviews/grain-size-lpf.php',
    'GS_UTF' => '../reviews/grain-size-upstream-transition-fill.php',
  ];

  if (isset($map[$t])) return $map[$t];
  if (strpos($t, 'GS_') === 0 || $t === 'GS') return '../reviews/grain-size-full.php';
  return null;
}

// ========================================
// FILTRO DE DÍAS
// ========================================
$days = isset($_GET['days']) ? max(1, (int)$_GET['days']) : 6;

// ========================================
// FETCH — REVIEWED
// ========================================
$RevNotify = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date
  FROM test_reviewed
  WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
  ORDER BY Start_Date DESC
  LIMIT 200
");

// ========================================
// FETCH — REPEAT (OPCIÓN B: solo lo que existe en tu tabla real)
// ========================================
$RepNotify = find_by_sql("
  SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date, Send_By
  FROM test_repeat
  WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
  ORDER BY Start_Date DESC
  LIMIT 200
");

include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="notif-header">
    <div>
      <h1 class="notif-title-main">Notification Center</h1>
      <span class="notif-sub">Revisiones · Ensayos en Repetición · Alertas</span>
    </div>

    <form method="get">
      <select name="days" class="notif-select" onchange="this.form.submit()">
        <?php foreach([1,3,6,7,10,14,30] as $d): ?>
        <option value="<?= $d ?>" <?= $d==$days?'selected':'' ?>>
          Últimos <?= $d ?> días
        </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <!-- REVIEWED -->
  <section class="notif-section">
    <h3 class="notif-section-title">Reviewed <span>(<?= count($RevNotify) ?>)</span></h3>

    <?php if(empty($RevNotify)): ?>
      <div class="notif-empty">No hay ensayos revisados en este periodo.</div>
    <?php else: ?>
      <?php foreach($RevNotify as $n): ?>
        <a class="notif-item" href="<?= route_for_test_type($n['Test_Type']) ?>?id=<?= urlencode($n['Tracking']) ?>">
          
          <div class="notif-icon reviewed">✔</div>

          <div class="notif-info">
            <div class="notif-row">
              <strong><?= $n['Test_Type'] ?></strong>
              <span class="notif-badge reviewed">REVIEWED</span>
            </div>

            <div class="notif-sample">
              <?= $n['Sample_ID'] ?> - <?= $n['Sample_Number'] ?>
            </div>

            <div class="notif-date">
              <i class="bi bi-clock"></i>
              <?= date('Y-m-d H:i', strtotime($n['Start_Date'])) ?>
            </div>
          </div>

          <div class="notif-arrow">→</div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <!-- REPEAT -->
  <section class="notif-section">

    <h3 class="notif-section-title">Repeat <span>(<?= count($RepNotify) ?>)</span></h3>

    <?php if(empty($RepNotify)): ?>
      <div class="notif-empty">No hay ensayos en repetición recientes.</div>
    <?php else: ?>
      <?php foreach($RepNotify as $n): ?>
        <div class="notif-item repeat">

          <div class="notif-icon repeat">⚠️</div>

          <div class="notif-info">
            <div class="notif-row">
              <strong><?= $n['Test_Type'] ?></strong>
              <span class="notif-badge repeat">REPEAT</span>
            </div>

            <div class="notif-sample">
              <?= $n['Sample_ID'] ?> - <?= $n['Sample_Number'] ?>
            </div>

            <div class="notif-date">
              <i class="bi bi-clock"></i>
              <?= date('Y-m-d H:i', strtotime($n['Start_Date'])) ?>
              <?php if ($n['Send_By']): ?>
                · enviado por <?= $n['Send_By'] ?>
              <?php endif; ?>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

</main>

<style>

body {
  background: #f3f4f6 !important;
}

/* TITULO SUPERIOR */
.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.notif-title-main {
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0;
}

.notif-sub {
  font-size: 0.9rem;
  color: #6b7280;
}

/* SELECT MODERNO */
.notif-select {
  background: #ffffff;
  border: 1px solid #d1d5db;
  padding: 6px 10px;
  border-radius: 10px;
  font-size: 0.9rem;
}

/* SECCIÓN */
.notif-section {
  margin-bottom: 35px;
}

.notif-section-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 12px;
  color: #1f2937;
}

/* VACÍO */
.notif-empty {
  background: white;
  padding: 22px;
  color: #6b7280;
  border-radius: 14px;
  text-align: center;
  border: 1px dashed #d1d5db;
  font-size: 0.95rem;
}

/* ITEM */
.notif-item {
  display: flex;
  gap: 14px;
  padding: 16px;
  background: white;
  border-radius: 14px;
  margin-bottom: 12px;
  border: 1px solid #e5e7eb;
  transition: all 0.15s ease;
  cursor: pointer;
  text-decoration: none;
  color: #111;
}

.notif-item:hover {
  transform: translateY(-2px);
  border-color: #60a5fa;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  background: #f9fafb;
}

/* ÍCONO IZQUIERDA */
.notif-icon {
  font-size: 1.4rem;
  width: 32px;
  text-align: center;
  padding-top: 3px;
}

.notif-icon.reviewed {
  color: #16a34a;
}

.notif-icon.repeat {
  color: #f59e0b;
}

/* INFORMACIÓN */
.notif-info {
  flex: 1;
}

.notif-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.notif-sample {
  font-size: 0.92rem;
  color: #374151;
  margin-top: 2px;
}

.notif-date {
  font-size: 0.8rem;
  color: #6b7280;
  margin-top: 4px;
}

/* BADGES */
.notif-badge {
  padding: 2px 8px;
  font-size: 0.65rem;
  border-radius: 8px;
  font-weight: 600;
  letter-spacing: 0.03em;
}

.notif-badge.reviewed {
  background: #dcfce7;
  color: #166534;
}

.notif-badge.repeat {
  background: #fef3c7;
  color: #92400e;
}

/* FLECHA */
.notif-arrow {
  font-size: 1.4rem;
  color: #9ca3af;
  margin-left: auto;
}

.notif-item:hover .notif-arrow {
  color: #2563eb;
}

</style>


<?php include_once('../components/footer.php'); ?>
