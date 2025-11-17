<?php
$page_title = 'Message & Notify';
require_once('../config/load.php');
page_require_level(2); // ✔ Seguridad: antes de manejar POST

/* ===========================
   Utilitarios
   =========================== */
function norm($v)
{
  return strtoupper(trim((string)$v));
}

/**
 * Normaliza y resuelve la URL del review según el tipo.
 * - Convierte a MAYÚSCULAS
 * - Reemplaza '-' por '_' para consistencia
 * - Primero busca coincidencia exacta en el mapa
 * - Luego aplica reglas por prefijo (familias completas)
 */
function route_for_test_type(string $rawType): ?string
{
  $t = norm($rawType);
  $t_norm = str_replace('-', '_', $t);

  // Mapa exacto
  $exact = [
    'AL' => '../reviews/atterberg-limit.php',
    'BTS' => '../reviews/brazilian.php',
    'HY' => '../reviews/hydrometer.php',
    'DHY' => '../reviews/double-hydrometer.php',
    'PLT' => '../reviews/point-Load.php',
    'SND' => '../reviews/soundness.php',
    'SP'  => '../reviews/standard-proctor.php',
    'UCS' => '../reviews/unixial-compressive.php',

    'MC_OVEN' => '../reviews/moisture-oven.php',
    'MC_MICROWAVE' => '../reviews/moisture-microwave.php',
    'MC_CONSTANT_MASS' => '../reviews/moisture-constant-mass.php',
    'MC_SCALE' => '../reviews/moisture-scale.php',

    'LAA_LARGE' => '../reviews/LAA-Large.php',
    'LAA_SMALL' => '../reviews/LAA-Small.php',

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
  if (isset($exact[$t_norm])) return $exact[$t_norm];

  if (strpos($t_norm, 'GS_') === 0 || $t_norm === 'GS') {
    return '../reviews/grain-size-full.php';
  }

  return null;
}

/* ===========================
   POST: marcar ensayos firmados
   =========================== */
if (isset($_POST['update-signed'])) {
  $sample_id     = $db->escape($_POST['Sample_ID'] ?? '');
  $sample_number = $db->escape($_POST['Sample_Number'] ?? '');

  $testTypeMappings = [
    'AL'  => ['AL'],
    'BTS' => ['BTS'],
    'GS'  => ['GS_FF', 'GS_CF', 'GS_LPF', 'GS_UTF'],
    'LAA' => ['LAA_Large', 'LAA_Small'],
    'MC'  => ['MC_Oven', 'MC_Microwave', 'MC_Constant_Mass', 'MC_Scale'],
    'PLT' => ['PLT'],
    'SG'  => ['SG_Coarse', 'SG_Fine'],
    'SP'  => ['SP'],
    'UCS' => ['UCS'],
  ];

  $normalizedMappings = [];
  foreach ($testTypeMappings as $k => $arr) {
    $k2 = str_replace('-', '_', norm($k));
    $normalizedMappings[$k2] = array_map(function ($v) {
      return str_replace('-', '_', norm($v));
    }, $arr);
  }

  $update_count = 0;

  for ($i = 1; $i <= 20; $i++) {
    $testTypeKey      = 'Test_Type' . $i;
    $testTypeValueKey = 'Test_Type' . $i . '_value';

    $testTypeValue = $_POST[$testTypeValueKey] ?? '';
    $testTypeValue = str_replace('-', '_', norm($testTypeValue));

    $signed = isset($_POST[$testTypeKey]) ? 1 : 0;

    $mappedValues = $normalizedMappings[$testTypeValue] ?? [];

    foreach ($mappedValues as $mappedValue) {
      $mappedEsc = $db->escape($mappedValue);

      $qUpdate = "
        UPDATE test_reviewed
           SET Signed = '{$signed}'
         WHERE Sample_ID = '{$sample_id}'
           AND Sample_Number = '{$sample_number}'
           AND Test_Type = '{$mappedEsc}'
      ";
      $ok = $db->query($qUpdate);
      if (!$ok) {
        error_log('test_reviewed UPDATE error: ' . $db->error);
        continue;
      }

      $aff = (int)$db->affected_rows;
      if ($aff > 0) {
        $update_count += $aff;
        continue;
      }

      $qExist = "
        SELECT 1
          FROM test_reviewed
         WHERE Sample_ID = '{$sample_id}'
           AND Sample_Number = '{$sample_number}'
           AND Test_Type = '{$mappedEsc}'
         LIMIT 1
      ";
      $exists = find_by_sql($qExist);

      if (empty($exists)) {
        $qInsert = "
          INSERT INTO test_reviewed
            (Sample_ID, Sample_Number, Test_Type, Signed, Start_Date)
          VALUES
            ('{$sample_id}', '{$sample_number}', '{$mappedEsc}', '{$signed}', NOW())
        ";
        $ok2 = $db->query($qInsert);
        if (!$ok2) {
          error_log('test_reviewed INSERT error: ' . $db->error);
        } else {
          $update_count += 1;
        }
      }
    }
  }

  if ($update_count > 0) {
    $session->msg('s', 'Sample has been updated');
  } else {
    $session->msg('w', 'No changes were made');
  }

  header('Location: ../pages/message.php');
  exit();
}

// ===========================
// GET: Notificaciones
// ===========================

include_once('../components/header.php');

// Rango de días
$days = isset($_GET['days']) ? max(1, (int)$_GET['days']) : 6;

// Reviewed
$RevNotify = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date
      FROM test_reviewed
     WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
     ORDER BY Start_Date DESC
     LIMIT 200
  ");

// Repeat
$RepNotify = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date
      FROM test_repeat
     WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
     ORDER BY Start_Date DESC
     LIMIT 200
  ");
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Notification Center</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item">Panel</li>
          <li class="breadcrumb-item active">Notification</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <div class="col-12">
        <?php echo display_msg($msg); ?>
      </div>

      <!-- Filtros -->
      <div class="col-12">
        <div class="card card-filter-modern">
          <div class="card-body py-3">
            <form class="row g-3 align-items-center" method="get">
              <div class="col-auto">
                <span class="filter-label text-muted">Mostrar notificaciones de los últimos</span>
              </div>
              <div class="col-auto">
                <select name="days" class="form-select form-select-sm filter-select">
                  <?php
                  $options = [1, 3, 6, 7, 10, 14, 30];
                  if (!in_array($days, $options)) $options[] = $days;
                  sort($options);
                  foreach ($options as $d): ?>
                    <option value="<?= (int)$d; ?>" <?= $d == $days ? 'selected' : ''; ?>>
                      <?= (int)$d; ?> día<?= $d > 1 ? 's' : ''; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="bi bi-funnel"></i> Aplicar
                </button>
              </div>
              <div class="col-auto ms-auto">
                <span class="badge bg-light text-dark border small">
                  Total Reviewed: <?= count($RevNotify); ?> · Repeat: <?= count($RepNotify); ?>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Columna Reviewed -->
      <div class="col-lg-6">
        <div class="card card-modern">
          <div class="card-header-modern d-flex justify-content-between align-items-center">
            <div>
              <div class="card-eyebrow">Quality Review</div>
              <h5 class="card-title mb-0">Reviewed</h5>
              <small class="text-muted">Últimos <?= (int)$days ?> día<?= $days > 1 ? 's' : ''; ?></small>
            </div>
            <div class="icon-circle bg-success-subtle text-success">
              <i class="bi bi-check2-square"></i>
            </div>
          </div>
          <div class="card-body pt-2">
            <?php if (empty($RevNotify)): ?>
              <div class="empty-state-modern text-center py-4">
                <div class="empty-icon">✅</div>
                <div class="empty-title">Sin ensayos revisados</div>
                <div class="empty-subtitle">No hay registros en este periodo.</div>
              </div>
            <?php else: ?>
              <div class="list-modern">
                <?php foreach ($RevNotify as $revNotify):
                  $id       = htmlspecialchars((string)$revNotify['Sample_ID']);
                  $number   = htmlspecialchars((string)$revNotify['Sample_Number']);
                  $testType = (string)$revNotify['Test_Type'];
                  $testTypeDisplay = htmlspecialchars($testType);
                  $tracking = urlencode((string)$revNotify['Tracking']);
                  $url      = route_for_test_type($testType);
                  $dateStr  = $revNotify['Start_Date'] ?? null;
                  $dateFmt  = $dateStr ? date('Y-m-d H:i', strtotime($dateStr)) : '—';
                ?>
                  <?php if ($url): ?>
                    <a href="<?= $url ?>?id=<?= $tracking ?>" class="list-modern-item">
                      <div class="list-modern-main">
                        <div class="list-modern-title">
                          <?= $id . ' - ' . $number; ?>
                        </div>
                        <div class="list-modern-meta">
                          <span class="badge badge-test-type"><?= $testTypeDisplay; ?></span>
                          <span class="list-modern-date">
                            <i class="bi bi-clock"></i> <?= $dateFmt; ?>
                          </span>
                        </div>
                      </div>
                      <div class="list-modern-chevron">
                        <i class="bi bi-arrow-right-short"></i>
                      </div>
                    </a>
                  <?php else: ?>
                    <div class="list-modern-item list-modern-item-disabled">
                      <div class="list-modern-main">
                        <div class="list-modern-title">
                          <?= $id . ' - ' . $number; ?>
                        </div>
                        <div class="list-modern-meta">
                          <span class="badge badge-test-type"><?= $testTypeDisplay; ?></span>
                          <span class="list-modern-date">
                            <i class="bi bi-clock"></i> <?= $dateFmt; ?>
                          </span>
                        </div>
                      </div>
                      <div class="list-modern-chevron text-muted small">
                        sin vista
                      </div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Columna Repeat -->
      <div class="col-lg-6">
        <div class="card card-modern">
          <div class="card-header-modern d-flex justify-content-between align-items-center">
            <div>
              <div class="card-eyebrow">Re-testing</div>
              <h5 class="card-title mb-0">Repeat</h5>
              <small class="text-muted">Últimos <?= (int)$days ?> día<?= $days > 1 ? 's' : ''; ?></small>
            </div>
            <div class="icon-circle bg-warning-subtle text-warning">
              <i class="bi bi-arrow-repeat"></i>
            </div>
          </div>
          <div class="card-body pt-2">
            <?php if (empty($RepNotify)): ?>
              <div class="empty-state-modern text-center py-4">
                <div class="empty-icon">🔁</div>
                <div class="empty-title">Sin ensayos en repetición</div>
                <div class="empty-subtitle">No hay registros en este periodo.</div>
              </div>
            <?php else: ?>
              <div class="list-modern">
                <?php foreach ($RepNotify as $repNotify):
                  $id       = htmlspecialchars((string)$repNotify['Sample_ID']);
                  $number   = htmlspecialchars((string)$repNotify['Sample_Number']);
                  $testType = (string)$repNotify['Test_Type'];
                  $testTypeDisplay = htmlspecialchars($testType);
                  $tracking = urlencode((string)$repNotify['Tracking']);
                  $url      = route_for_test_type($testType);
                  $dateStr  = $repNotify['Start_Date'] ?? null;
                  $dateFmt  = $dateStr ? date('Y-m-d H:i', strtotime($dateStr)) : '—';
                ?>
                  <?php if ($url): ?>
                    <a href="<?= $url ?>?id=<?= $tracking ?>" class="list-modern-item">
                      <div class="list-modern-main">
                        <div class="list-modern-title">
                          <?= $id . ' - ' . $number; ?>
                        </div>
                        <div class="list-modern-meta">
                          <span class="badge badge-test-type badge-repeat"><?= $testTypeDisplay; ?></span>
                          <span class="list-modern-date">
                            <i class="bi bi-clock"></i> <?= $dateFmt; ?>
                          </span>
                        </div>
                      </div>
                      <div class="list-modern-chevron">
                        <i class="bi bi-arrow-right-short"></i>
                      </div>
                    </a>
                  <?php else: ?>
                    <div class="list-modern-item list-modern-item-disabled">
                      <div class="list-modern-main">
                        <div class="list-modern-title">
                          <?= $id . ' - ' . $number; ?>
                        </div>
                        <div class="list-modern-meta">
                          <span class="badge badge-test-type badge-repeat"><?= $testTypeDisplay; ?></span>
                          <span class="list-modern-date">
                            <i class="bi bi-clock"></i> <?= $dateFmt; ?>
                          </span>
                        </div>
                      </div>
                      <div class="list-modern-chevron text-muted small">
                        sin vista
                      </div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </section>

</main><!-- End #main -->

<style>
  .card-filter-modern {
    border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, 0.5);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
  }
  .filter-label {
    font-size: 0.85rem;
  }
  .filter-select {
    min-width: 130px;
  }

  .card-modern {
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.3);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.09);
    overflow: hidden;
  }

  .card-header-modern {
    padding: 0.85rem 1.2rem 0.5rem;
    border-bottom: 1px solid rgba(226, 232, 240, 0.9);
    background: radial-gradient(circle at top left, #e0f2fe 0, #ffffff 55%);
  }

  .card-eyebrow {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    margin-bottom: 0.1rem;
  }

  .icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
  }

  .empty-state-modern .empty-icon {
    font-size: 1.6rem;
    margin-bottom: 0.25rem;
  }
  .empty-state-modern .empty-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: #0f172a;
  }
  .empty-state-modern .empty-subtitle {
    font-size: 0.8rem;
    color: #64748b;
  }

  .list-modern {
    max-height: 420px;
    overflow-y: auto;
    padding: 0.4rem 0.4rem 0.75rem;
  }

  .list-modern-item {
    display: flex;
    align-items: center;
    padding: 0.45rem 0.6rem;
    border-radius: 12px;
    border: 1px solid transparent;
    margin-bottom: 0.25rem;
    text-decoration: none;
    background: #f9fafb;
    transition: all 0.15s ease;
  }
  .list-modern-item:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    transform: translateY(-1px);
  }

  .list-modern-item-disabled {
    cursor: default;
    background: #f8fafc;
  }
  .list-modern-item-disabled:hover {
    background: #f8fafc;
    border-color: transparent;
    transform: none;
  }

  .list-modern-main {
    flex: 1;
    min-width: 0;
  }
  .list-modern-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }
  .list-modern-meta {
    margin-top: 0.05rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
  }
  .list-modern-date {
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
  }
  .list-modern-chevron {
    margin-left: 0.35rem;
    color: #94a3b8;
    font-size: 1.25rem;
  }

  .badge-test-type {
    font-size: 0.7rem;
    border-radius: 999px;
    padding: 0.15rem 0.45rem;
    background: #e5f3ff;
    color: #1d4ed8;
  }
  .badge-repeat {
    background: #fef3c7;
    color: #b45309;
  }

  @media (max-width: 991.98px) {
    .list-modern {
      max-height: 320px;
    }
  }
</style>

<?php include_once('../components/footer.php'); ?>
