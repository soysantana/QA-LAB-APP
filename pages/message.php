<?php
$page_title = 'Message & Notify';
require_once('../config/load.php');
page_require_level(2); // ✔ Seguridad: antes de manejar POST

/* ===========================
   Utilitarios
   =========================== */
function norm($v){ return strtoupper(trim((string)$v)); }

/**
 * Normaliza y resuelve la URL del review según el tipo.
 * - Convierte a MAYÚSCULAS
 * - Reemplaza '-' por '_' para consistencia
 * - Primero busca coincidencia exacta en el mapa
 * - Luego aplica reglas por prefijo (familias completas)
 */
function route_for_test_type(string $rawType): ?string {
  $t = norm($rawType);
  $t_norm = str_replace('-', '_', $t);

  // Mapa exacto
  $exact = [
    'AL' => '../reviews/atterberg-limit.php',
    'BTS' => '../reviews/brazilian.php',
    'HY' => '../reviews/hydrometer.php',
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

    'GS' => '../reviews/grain-size.php',
    'GS_FF' => '../reviews/grain-size-fine-filter.php',
    'GS_CF' => '../reviews/grain-size-coarse-filter.php',
    'GS_LPF' => '../reviews/grain-size-lpf.php',
    'GS_UTF' => '../reviews/grain-size-upstream-transition-fill.php',
  ];
  if (isset($exact[$t_norm])) return $exact[$t_norm];

  // Prefijos (familias que van al mismo review)
  if (strpos($t_norm, 'GS_') === 0 || $t_norm === 'GS') {
    // Ej.: GS_TRF, GS_UFF, GS_FRF, GS_IRF, GS_RF, GS_BF
    return '../reviews/grain-size-full.php';
  }

  return null; // sin ruta conocida
}

/* ===========================
   POST: update-signed
   =========================== */
if (isset($_POST['update-signed'])) {
  $sample_id     = $db->escape($_POST['Sample_ID'] ?? '');
  $sample_number = $db->escape($_POST['Sample_Number'] ?? '');

  // Mapeo principal -> subtipos (usar '_' consistentemente)
  $testTypeMappings = [
    'AL'  => ['AL'],
    'BTS' => ['BTS'],
    'GS'  => ['GS_FF', 'GS_CF', 'GS_LPF', 'GS_UTF'], // ajusta según tu nomenclatura real
    'LAA' => ['LAA_Large', 'LAA_Small'],
    'MC'  => ['MC_Oven', 'MC_Microwave', 'MC_Constant_Mass', 'MC_Scale'],
    'PLT' => ['PLT'],
    'SG'  => ['SG_Coarse', 'SG_Fine'],
    'SP'  => ['SP'],
    'UCS' => ['UCS'],
  ];

  // Normaliza claves del mapeo (guiones -> underscores y upper)
  $normalizedMappings = [];
  foreach ($testTypeMappings as $k => $arr) {
    $k2 = str_replace('-', '_', norm($k));
    $normalizedMappings[$k2] = array_map(function($v){
      return str_replace('-', '_', norm($v));
    }, $arr);
  }

  $update_count = 0;

  for ($i = 1; $i <= 20; $i++) {
    $testTypeKey      = 'Test_Type' . $i;
    $testTypeValueKey = 'Test_Type' . $i . '_value';

    // Valor oculto del tipo (el "macro")
    $testTypeValue = $_POST[$testTypeValueKey] ?? '';
    $testTypeValue = str_replace('-', '_', norm($testTypeValue)); // normaliza

    // Checkbox marcado => 1; si no viene, 0
    $signed = isset($_POST[$testTypeKey]) ? 1 : 0;

    // Resuelve subtipos mapeados
    $mappedValues = $normalizedMappings[$testTypeValue] ?? [];

    foreach ($mappedValues as $mappedValue) {
      // UPDATE primero
      $qUpdate = "
        UPDATE test_reviewed
           SET Signed = '{$signed}'
         WHERE Sample_ID = '{$sample_id}'
           AND Sample_Number = '{$sample_number}'
           AND Test_Type = '{$db->escape($mappedValue)}'
      ";
      $ok = $db->query($qUpdate);
      if (!$ok) {
        error_log('test_reviewed UPDATE error: '.$db->error);
        continue;
      }

      $aff = (int)$db->affected_rows();
      if ($aff > 0) {
        $update_count += $aff; // se cambió valor existente
        continue;
      }

      // Si no afectó filas, puede ser porque no existe el registro
      // Revisamos existencia:
      $qExist = "
        SELECT 1
          FROM test_reviewed
         WHERE Sample_ID = '{$sample_id}'
           AND Sample_Number = '{$sample_number}'
           AND Test_Type = '{$db->escape($mappedValue)}'
         LIMIT 1
      ";
      $exists = find_by_sql($qExist);

      if (empty($exists)) {
        // INSERT nuevo
        $qInsert = "
          INSERT INTO test_reviewed
            (Sample_ID, Sample_Number, Test_Type, Signed, Start_Date)
          VALUES
            ('{$sample_id}', '{$sample_number}', '{$db->escape($mappedValue)}', '{$signed}', NOW())
        ";
        $ok2 = $db->query($qInsert);
        if (!$ok2) {
          error_log('test_reviewed INSERT error: '.$db->error);
        } else {
          $update_count += 1;
        }
      } else {
        // Existía pero no cambió (mismo valor Signed)
        // No sumamos al contador
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

include_once('../components/header.php');
?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Notification</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Notification</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">

      <div class="col-lg-8">
        <?php echo display_msg($msg); ?>
      </div>

      <?php
        // Rango de días configurable (?days=14), por defecto 6
        $days = isset($_GET['days']) ? max(1, (int)$_GET['days']) : 6;

        // Reviewed (últimos N días, con LIMIT para evitar listados enormes)
        $RevNotify = find_by_sql("
          SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date
            FROM test_reviewed
           WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
           ORDER BY Start_Date DESC
           LIMIT 200
        ");

        // Repeat (últimos N días)
        $RepNotify = find_by_sql("
          SELECT Sample_ID, Sample_Number, Test_Type, Tracking, Start_Date
            FROM test_repeat
           WHERE Start_Date >= NOW() - INTERVAL {$days} DAY
           ORDER BY Start_Date DESC
           LIMIT 200
        ");
      ?>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Reviewed <small class="text-muted">(últimos <?= (int)$days ?> días)</small></h5>
            <div class="list-group">
              <?php if (empty($RevNotify)): ?>
                <span class="list-group-item text-muted">Sin registros en este periodo.</span>
              <?php else: ?>
                <?php foreach ($RevNotify as $revNotify): 
                  $id       = htmlspecialchars($revNotify['Sample_ID']);
                  $number   = htmlspecialchars($revNotify['Sample_Number']);
                  $testType = $revNotify['Test_Type']; // normalizo en route
                  $tracking = urlencode($revNotify['Tracking']);
                  $url      = route_for_test_type($testType);
                ?>
                  <?php if ($url): ?>
                    <a href="<?= $url ?>?id=<?= $tracking ?>" class="list-group-item list-group-item-action">
                      <?= $id . '-' . $number . '-' . htmlspecialchars($testType) ?>
                    </a>
                  <?php else: ?>
                    <span class="list-group-item list-group-item-action text-muted">
                      <?= $id . '-' . $number . '-' . htmlspecialchars($testType) ?> (sin vista)
                    </span>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Repeat <small class="text-muted">(últimos <?= (int)$days ?> días)</small></h5>
            <div class="list-group">
              <?php if (empty($RepNotify)): ?>
                <span class="list-group-item text-muted">Sin registros en este periodo.</span>
              <?php else: ?>
                <?php foreach ($RepNotify as $repNotify): 
                  $id       = htmlspecialchars($repNotify['Sample_ID']);
                  $number   = htmlspecialchars($repNotify['Sample_Number']);
                  $testType = $repNotify['Test_Type'];
                  $tracking = urlencode($repNotify['Tracking']);
                  $url      = route_for_test_type($testType);
                ?>
                  <?php if ($url): ?>
                    <a href="<?= $url ?>?id=<?= $tracking ?>" class="list-group-item list-group-item-action">
                      <?= $id . '-' . $number . '-' . htmlspecialchars($testType) ?>
                    </a>
                  <?php else: ?>
                    <span class="list-group-item list-group-item-action text-muted">
                      <?= $id . '-' . $number . '-' . htmlspecialchars($testType) ?> (sin vista)
                    </span>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Si quieres reactivar la sección de Signed con modal, te la adapto a este patrón DRY y segura -->

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php'); ?>
