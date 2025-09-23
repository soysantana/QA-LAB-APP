<?php
$page_title = 'Soundness Test';
require_once('../config/load.php');
$Search = find_by_id('soundness', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-snd'])) {
    include('../database/soundness/update.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Soundness Test</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Soundness Test</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->
  <section class="section">
    <div class="row">
      <form class="row" action="soundness.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4"><?php echo display_msg($msg); ?></div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <!-- Laboratory Information -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'ASTM-C88') echo 'selected'; ?>>ASTM-C88</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>" />
                </div>
                <div class=" col-md-4">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried" <?php if ($Search['Preparation_Methods'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                    <option value="Air Dried" <?php if ($Search['Preparation_Methods'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                    <option value="Microwave Dried" <?php if ($Search['Preparation_Methods'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                    <option value="Wet" <?php if ($Search['Preparation_Methods'] == 'Wet') echo 'selected'; ?>>Wet</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual" <?php if ($Search['Split_Methods'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    <option value="Mechanical" <?php if ($Search['Split_Methods'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>" />
                </div>
                <div class=" col-md-4">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>" />
                </div>
                <div class=" col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Laboratory Information -->

        <!-- Form the Grain Size Distribution for Soundness -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grain Size Distribution for Soundness</h5>

              <!-- Wet Dry and Washed g -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>Wt Dry Soil, g</th>
                    <td><input type="text" style="border: none" class="form-control" name="WtDrySoil" id="WtDrySoil" value="<?php echo ($Search['WtDrySoil']); ?>" /></td>
                  </tr>
                  <tr>
                    <th>Wt Washed, g</th>
                    <td><input type="text" style="border: none" class="form-control" name="WtWashed" id="WtWashed" value="<?php echo ($Search['WtWashed']); ?>" /></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Wet Dry and Washed g -->

              <!-- Tamices para GS Coarse: Screen (pulgadas o No.) => abertura en mm -->
              <?php
              $sizes = [
                '4"' => 100,
                '3 1/2"' => 90,
                '3"' => 75,
                '2 1/2"' => 63,
                '2"' => 50,
                '1 1/2"' => 38,
                '1"' => 25,
                '3/4"' => 19,
                '1/2"' => 12.5,
                '3/8"' => 9.5,
                'No. 4' => 4.75
              ];
              ?>
              <!-- End Tamices para GS Coarse: Screen (pulgadas o No.) => abertura en mm -->

              <!-- Table for Coarse Aggregate -->
              <table class="table table-bordered" oninput="calculateGrainSizeCoarse()">
                <thead>
                  <tr>
                    <th scope="col">Screen</th>
                    <th scope="col">(mm)</th>
                    <th scope="col">Wt. Ret</th>
                    <th scope="col">% Ret</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="4">Coarse Aggregate</td>
                  </tr>
                  <?php
                  // Obtener valores para WtRetCoarse y PctRetCoarse desde la BD
                  $valuesString = $Search['WtRetCoarse'] ?? '';
                  $valuesArray = !empty($valuesString) ? explode(',', $valuesString) : [];

                  $pctString = $Search['PctRetCoarse'] ?? '';
                  $pctArray = !empty($pctString) ? explode(',', $pctString) : [];

                  $i = 1;
                  foreach ($sizes as $size => $mm):
                    // Valor WtRetCoarse
                    $value = $valuesArray[$i - 1] ?? '';
                    $value = trim($value);
                    if ($value === 'null' || $value === '') {
                      $value = '';
                    }

                    // Valor PctRetCoarse
                    $pctValue = $pctArray[$i - 1] ?? '';
                    $pctValue = trim($pctValue);
                    if ($pctValue === 'null' || $pctValue === '') {
                      $pctValue = '';
                    }
                  ?>
                    <tr>
                      <th scope="row"><?php echo htmlspecialchars($size); ?></th>
                      <th><?php echo htmlspecialchars($mm); ?></th>
                      <td>
                        <input type="text" style="border: none" class="form-control" name="WtRetCoarse<?php echo $i; ?>" id="WtRetCoarse<?php echo $i; ?>" value="<?php echo htmlspecialchars($value); ?>" />
                      </td>
                      <td>
                        <input type="text" style="border: none" class="form-control" name="PctRetCoarse<?php echo $i; ?>" id="PctRetCoarse<?php echo $i; ?>" value="<?php echo htmlspecialchars($pctValue); ?>" readonly tabindex="-1" />
                      </td>
                    </tr>
                  <?php
                    $i++;
                  endforeach;
                  ?>

                  <tr>
                    <th scope="row" colspan="2">Total</th>
                    <td>
                      <input type="text" style="border: none" class="form-control" name="WtRetCoarseTotal" id="WtRetCoarseTotal" readonly tabindex="-1" value="<?php echo ($Search['WtRetCoarseTotal']); ?>" />
                    </td>
                    <td>
                      <input type="text" style="border: none" class="form-control" name="PctRetCoarseTotal" id="PctRetCoarseTotal" readonly tabindex="-1" value="<?php echo ($Search['PctRetCoarseTotal']); ?>" />
                    </td>
                  </tr>
                </tbody>
              </table>
              <!-- End Table for Coarse Aggregate -->


              <!-- Tamices para GS Coarse: Screen (pulgadas o No.) => abertura en mm -->
              <?php
              $sizes = [
                'No. 4' => 4.75,
                'No. 8' => 2.36,
                'No. 16' => 1.18,
                'No. 30' => 0.6,
                'No. 50' => 0.3,
                'No. 100' => 0.15,
                'Pan' => '',
              ];
              ?>
              <!-- End Tamices para GS Coarse: Screen (pulgadas o No.) => abertura en mm -->

              <!-- Table for Fine Aggregate -->
              <table class="table table-bordered" oninput="calculateGrainSizeFine()">
                <tbody>
                  <tr>
                    <td colspan="4">Fine Aggregate</td>
                  </tr>
                  <?php
                  // Obtener valores para WtRetFine y PctRetFine desde la BD
                  $valuesString = $Search['WtRetFine'] ?? '';
                  $valuesArray = !empty($valuesString) ? explode(',', $valuesString) : [];

                  $pctString = $Search['PctRetFine'] ?? '';
                  $pctArray = !empty($pctString) ? explode(',', $pctString) : [];

                  $i = 1;
                  foreach ($sizes as $size => $mm):
                    // Valor WtRetCoarse
                    $value = $valuesArray[$i - 1] ?? '';
                    $value = trim($value);
                    if ($value === 'null' || $value === '') {
                      $value = '';
                    }

                    // Valor PctRetCoarse
                    $pctValue = $pctArray[$i - 1] ?? '';
                    $pctValue = trim($pctValue);
                    if ($pctValue === 'null' || $pctValue === '') {
                      $pctValue = '';
                    }
                  ?>
                    <tr>
                      <th scope="row"><?php echo htmlspecialchars($size); ?></th>
                      <th><?php echo htmlspecialchars($mm); ?></th>
                      <td>
                        <input type="text" style="border: none" class="form-control" name="WtRetFine<?php echo $i; ?>" id="WtRetFine<?php echo $i; ?>" value="<?php echo htmlspecialchars($value); ?>" />
                      </td>
                      <td>
                        <input type="text" style="border: none" class="form-control" name="PctRetFine<?php echo $i; ?>" id="PctRetFine<?php echo $i; ?>" readonly tabindex="-1" value="<?php echo htmlspecialchars($pctValue); ?>" />
                      </td>
                    </tr>
                  <?php $i++;
                  endforeach; ?>
                  <tr>
                    <th scope="row" colspan="2">Total</th>
                    <td>
                      <input type="text" style="border: none" class="form-control" name="WtRetFineTotal" id="WtRetFineTotal" readonly tabindex="-1" value="<?php echo htmlspecialchars($Search['WtRetFineTotal']); ?>" />
                    </td>
                    <td>
                      <input type="text" style="border: none" class="form-control" name="PctRetFineTotal" id="PctRetFineTotal" readonly tabindex="-1" value="<?php echo htmlspecialchars($Search['PctRetFineTotal']); ?>" />
                    </td>
                  </tr>
                </tbody>
              </table>
              <!-- End Table for Fine Aggregate -->


            </div>
          </div>
        </div>
        <!-- End Form the Grain Size Distribution for Soundness -->

        <!-- Soundness Test Cycles -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Information</h5>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Cycle</th>
                    <th>Start Date</th>
                    <th>Room Temperature (°C)</th>
                    <th>Solution Temperature (°C)</th>
                    <th>Specific Gravity of the Solution</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $startDates = !empty($Search['StartDate']) ? explode(',', $Search['StartDate']) : [];
                  $roomTemps = !empty($Search['RoomTemp']) ? explode(',', $Search['RoomTemp']) : [];
                  $solutionTemps = !empty($Search['SolutionTemp']) ? explode(',', $Search['SolutionTemp']) : [];
                  $specificGravities = !empty($Search['SpecificGravity']) ? explode(',', $Search['SpecificGravity']) : [];

                  $cycles = range(1, 5);
                  foreach ($cycles as $i => $cycle):
                  ?>
                    <tr>
                      <th><?php echo htmlspecialchars($cycle); ?></th>
                      <td>
                        <input type="date" style="border: none" class="form-control"
                          name="StartDate<?php echo $cycle; ?>"
                          id="StartDate<?php echo $cycle; ?>"
                          value="<?php echo isset($startDates[$i]) ? htmlspecialchars($startDates[$i]) : ''; ?>"
                          tabindex="-1" />
                      </td>
                      <td>
                        <input type="text" style="border: none" class="form-control"
                          name="RoomTemp<?php echo $cycle; ?>"
                          id="RoomTemp<?php echo $cycle; ?>"
                          value="<?php echo isset($roomTemps[$i]) ? htmlspecialchars($roomTemps[$i]) : ''; ?>" />
                      </td>
                      <td>
                        <input type="text" style="border: none" class="form-control"
                          name="SolutionTemp<?php echo $cycle; ?>"
                          id="SolutionTemp<?php echo $cycle; ?>"
                          value="<?php echo isset($solutionTemps[$i]) ? htmlspecialchars($solutionTemps[$i]) : ''; ?>" />
                      </td>
                      <td>
                        <input type="text" style="border: none" class="form-control"
                          name="SpecificGravity<?php echo $cycle; ?>"
                          id="SpecificGravity<?php echo $cycle; ?>"
                          value="<?php echo isset($specificGravities[$i]) ? htmlspecialchars($specificGravities[$i]) : ''; ?>" />
                      </td>
                    </tr>
                  <?php endforeach; ?>

                  <tr>
                    <th colspan="2">Solution Used</th>
                    <th colspan="3">Sodium Sulfate</th>
                  </tr>
                </tbody>
              </table>


            </div>
          </div>
        </div>
        <!-- End Soundness Test Cycles -->

        <!-- Qualitative Examination of Coarse Sizes -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Qualitative Examination of Coarse Sizes</h5>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th rowspan="3">Sieve Size</th>
                    <th colspan="8">Particles Exhibiting Distress</th>
                    <th rowspan="3">Total No. of Particles Before Test</th>
                  </tr>
                  <tr>
                    <th colspan="2">Splitting</th>
                    <th colspan="2">Crumbling</th>
                    <th colspan="2">Cracking</th>
                    <th colspan="2">Flaking</th>
                  </tr>
                  <tr>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  function cleanArrayValues($str)
                  {
                    // Convierte la cadena a array
                    $arr = !empty($str) ? explode(',', $str) : [];
                    // Reemplaza "null" o NULL real por cadena vacía
                    return array_map(function ($v) {
                      return (strtolower(trim($v)) === 'null' || $v === null) ? '' : trim($v);
                    }, $arr);
                  }

                  // Convertimos cada campo de la BD
                  $splittingNo      = cleanArrayValues($Search['SplittingNo'] ?? '');
                  $splittingPct     = cleanArrayValues($Search['SplittingPct'] ?? '');
                  $crumblingNo      = cleanArrayValues($Search['CrumblingNo'] ?? '');
                  $crumblingPct     = cleanArrayValues($Search['CrumblingPct'] ?? '');
                  $crackingNo       = cleanArrayValues($Search['CrackingNo'] ?? '');
                  $crackingPct      = cleanArrayValues($Search['CrackingPct'] ?? '');
                  $flakingNo        = cleanArrayValues($Search['FlakingNo'] ?? '');
                  $flakingPct       = cleanArrayValues($Search['FlakingPct'] ?? '');
                  $totalParticles   = cleanArrayValues($Search['TotalParticles'] ?? '');

                  // Tamaños
                  $sieveSizes = [
                    "37.5mm(11⁄2 in) to 19.0 mm (3/4 in)",
                    "63 mm (2 1⁄2 in) to 37.5 mm (1 1⁄2 in)",
                    "75 mm (3 in) to 63 mm (2 1⁄2 in)",
                    "91 mm (3 1⁄2 in) to 75 mm (3 in)",
                    "100 mm (4 in) to 90 mm (3 1⁄2 in)"
                  ];

                  foreach ($sieveSizes as $index => $size) {
                    echo '<tr>';
                    echo '<th>' . htmlspecialchars($size) . '</th>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="SplittingNo' . ($index + 1) . '" id="SplittingNo' . ($index + 1) . '"
                    value="' . htmlspecialchars($splittingNo[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="SplittingPct' . ($index + 1) . '" id="SplittingPct' . ($index + 1) . '" readonly tabindex="-1"
                    value="' . htmlspecialchars($splittingPct[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="CrumblingNo' . ($index + 1) . '" id="CrumblingNo' . ($index + 1) . '"
                    value="' . htmlspecialchars($crumblingNo[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="CrumblingPct' . ($index + 1) . '" id="CrumblingPct' . ($index + 1) . '" readonly tabindex="-1"
                    value="' . htmlspecialchars($crumblingPct[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="CrackingNo' . ($index + 1) . '" id="CrackingNo' . ($index + 1) . '"
                    value="' . htmlspecialchars($crackingNo[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="CrackingPct' . ($index + 1) . '" id="CrackingPct' . ($index + 1) . '" readonly tabindex="-1"
                    value="' . htmlspecialchars($crackingPct[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="FlakingNo' . ($index + 1) . '" id="FlakingNo' . ($index + 1) . '"
                    value="' . htmlspecialchars($flakingNo[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="FlakingPct' . ($index + 1) . '" id="FlakingPct' . ($index + 1) . '" readonly tabindex="-1"
                    value="' . htmlspecialchars($flakingPct[$index] ?? '') . '" /></td>';

                    echo '<td><input type="text" class="form-control" style="border: none"
                    name="TotalParticles' . ($index + 1) . '" id="TotalParticles' . ($index + 1) . '"
                    value="' . htmlspecialchars($totalParticles[$index] ?? '') . '" /></td>';

                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>



            </div>
          </div>
        </div>
        <!-- End Qualitative Examination of Coarse Sizes -->

        <!-- Table for Soundness Fine and Coarse Aggregate -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Results</h5>
              <?php
              $rows = [
                ["Minus 150 µm (No. 100)", "100g", "StarWeightRet1", "---", "", "FinalWeightRet1", "PercentagePassing1", "WeightedLoss1"],
                ["300 µm (No. 50) to No. 100", "100g", "StarWeightRet2", "---", "No. 100", "FinalWeightRet2", "PercentagePassing2", "WeightedLoss2"],
                ["600 µm (No. 30) to No. 50", "100g", "StarWeightRet3", "---", "No. 50", "FinalWeightRet3", "PercentagePassing3", "WeightedLoss3"],
                ["1.18 mm (No. 16) to No. 30", "100g", "StarWeightRet4", "---", "No. 30", "FinalWeightRet4", "PercentagePassing4", "WeightedLoss4"],
                ["2.36 mm (No. 8) to No. 16", "100g", "StarWeightRet5", "---", "No. 16", "FinalWeightRet5", "PercentagePassing5", "WeightedLoss5"],
                ["4.75 mm (No. 4) to No. 8", "100g", "StarWeightRet6", "---", "No. 8", "FinalWeightRet6", "PercentagePassing6", "WeightedLoss6"],
                ["9.5 mm (3⁄8 in.) to No. 4", "100g", "StarWeightRet7", "---", "No. 4", "FinalWeightRet7", "PercentagePassing7", "WeightedLoss7"],
              ];

              $cols = count($rows[0]); // Número de columnas
              ?>

              <!-- Soundness Test of Fine Aggregate -->
              <?php
              // Convertimos las columnas reales de la BD a arrays
              $starWeightRet      = explode(',', $Search['StarWeightRetFine'] ?? '');
              $finalWeightRet     = explode(',', $Search['FinalWeightRetFine'] ?? '');
              $percentagePassing  = explode(',', $Search['PercentagePassingFine'] ?? '');
              $weightedLoss       = explode(',', $Search['WeightedLossFine'] ?? '');

              // Reemplazamos "null" por vacío
              $starWeightRet     = array_map(fn($v) => strtolower(trim($v)) === 'null' ? '' : $v, $starWeightRet);
              $finalWeightRet    = array_map(fn($v) => strtolower(trim($v)) === 'null' ? '' : $v, $finalWeightRet);
              $percentagePassing = array_map(fn($v) => strtolower(trim($v)) === 'null' ? '' : $v, $percentagePassing);
              $weightedLoss      = array_map(fn($v) => strtolower(trim($v)) === 'null' ? '' : $v, $weightedLoss);

              $cols = count($rows[0]);
              ?>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td>Screen</td>
                    <td>Weight of Sample for each size (required)</td>
                    <td>Star Weight Ret</td>
                    <td>Combined Fractions</td>
                    <td>Designated Sieve After Test</td>
                    <td>Final Weight Ret</td>
                    <td>Percentage Passing Designated Sieve After Test</td>
                    <td>Weighted Percentage of Mass Loss</td>
                  </tr>
                  <tr>
                    <th colspan="8">Soundness Test of Fine Aggregate</th>
                  </tr>

                  <?php foreach ($rows as $rowIndex => $row): ?>
                    <tr>
                      <?php for ($i = 0; $i < $cols; $i++): ?>
                        <td>
                          <?php
                          // Determinamos el valor real de la base según la columna
                          if ($i == 2) $value = htmlspecialchars($starWeightRet[$rowIndex] ?? '');
                          elseif ($i == 5) $value = htmlspecialchars($finalWeightRet[$rowIndex] ?? '');
                          elseif ($i == 6) $value = htmlspecialchars($percentagePassing[$rowIndex] ?? '');
                          elseif ($i == 7) $value = htmlspecialchars($weightedLoss[$rowIndex] ?? '');
                          else $value = '';

                          // Definimos readonly solo para columnas 6 y 7
                          $readonly = in_array($i, [6, 7]) ? ' readonly tabindex="-1"' : '';

                          if (in_array($i, [2, 5, 6, 7])):
                          ?>
                            <input type="text" style="border: none" class="form-control"
                              name="<?= $row[$i] ?>" id="<?= $row[$i] ?>" value="<?= $value ?>" <?= $readonly ?> />
                          <?php else: ?>
                            <?= $row[$i] ?>
                          <?php endif; ?>
                        </td>
                      <?php endfor; ?>
                    </tr>
                  <?php endforeach; ?>

                  <tr>
                    <td colspan="2">Totals</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalStarWeightRet" id="TotalStarWeightRet" value="<?php echo ($Search['TotalStarWeightRetFine']); ?>" readonly tabindex="-1" /></td>
                    <td>---</td>
                    <td>---</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalFinalWeightRet" id="TotalFinalWeightRet" value="<?php echo ($Search['TotalFinalWeightRetFine']); ?>" readonly tabindex="-1" /></td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalWeightedLoss" id="TotalWeightedLoss" value="<?php echo ($Search['TotalWeightedLossFine']); ?>" readonly tabindex="-1" /></td>
                  </tr>
                </tbody>
              </table>

              <!-- End Soundness Test of Fine Aggregate -->

              <!-- Soundness Test of Coarse Aggregate -->
              <?php
              // Función para obtener valor o vacío si es null
              function getValue($search, $key, $index)
              {
                if (!isset($search[$key])) return '';

                // Convertir la cadena separada por comas a arreglo
                $values = explode(',', $search[$key]);

                // Manejar el caso de 'null' como vacío
                if (!isset($values[$index]) || strtolower(trim($values[$index])) === 'null') {
                  return '';
                }

                return $values[$index];
              }

              ?>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th colspan="8">Soundness Test of Coarse Aggregate</th>
                  </tr>
                  <tr>
                    <td>9.5mm(3⁄8 in.) to 4.75 mm (No. 4)</td>
                    <td>(300+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse1" id="StarWeightRetCoarse1" value="<?= getValue($Search, 'StarWeightRetCoarse', 0) ?>" /></td>
                    <td>4.0 mm (No. 5)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse1" id="FinalWeightRetCoarse1" value="<?= getValue($Search, 'FinalWeightRetCoarse', 0) ?>" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse1" id="PercentagePassingCoarse1" value="<?= getValue($Search, 'PercentagePassingCoarse', 0) ?>" readonly tabindex="-1" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse1" id="WeightedLossCoarse1" value="<?= getValue($Search, 'WeightedLossCoarse', 0) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>12.5 mm (1⁄2 in) to 9.5 mm (3⁄8 in)</td>
                    <td>(330+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse2" id="StarWeightRetCoarse2" value="<?= getValue($Search, 'StarWeightRetCoarse', 1) ?>" /></td>
                    <td rowspan="2">8.0 mm (5⁄16 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse2" id="FinalWeightRetCoarse2" value="<?= getValue($Search, 'FinalWeightRetCoarse', 1) ?>" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse2" id="PercentagePassingCoarse2" value="<?= getValue($Search, 'PercentagePassingCoarse', 1) ?>" readonly tabindex="-1" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse2" id="WeightedLossCoarse2" value="<?= getValue($Search, 'WeightedLossCoarse', 1) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>19.0mm(3⁄4 in) to 12.5 mm (1⁄2 in)</td>
                    <td>(670+-10)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse3" id="StarWeightRetCoarse3" value="<?= getValue($Search, 'StarWeightRetCoarse', 2) ?>" /></td>
                  </tr>
                  <tr>
                    <td>25 mm (1 in) to 19.0 mm (3⁄4 in)</td>
                    <td>(500+-30)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse4" id="StarWeightRetCoarse4" value="<?= getValue($Search, 'StarWeightRetCoarse', 3) ?>" /></td>
                    <td rowspan="2">16.0 mm (5⁄8 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse3" id="FinalWeightRetCoarse3" value="<?= getValue($Search, 'FinalWeightRetCoarse', 2) ?>" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse3" id="PercentagePassingCoarse3" value="<?= getValue($Search, 'PercentagePassingCoarse', 2) ?>" readonly tabindex="-1" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse3" id="WeightedLossCoarse3" value="<?= getValue($Search, 'WeightedLossCoarse', 2) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>37.5mm(11⁄2 in) to 25.0 mm (1 in.)</td>
                    <td>(1000+-50)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse5" id="StarWeightRetCoarse5" value="<?= getValue($Search, 'StarWeightRetCoarse', 4) ?>" /></td>
                  </tr>
                  <tr>
                    <td>50 mm (2 in) to 37.5 mm (1 1⁄2 in)</td>
                    <td>(2000+-200)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse6" id="StarWeightRetCoarse6" value="<?= getValue($Search, 'StarWeightRetCoarse', 5) ?>" /></td>
                    <td rowspan="2">31.5 mm (1 1⁄4 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse4" id="FinalWeightRetCoarse4" value="<?= getValue($Search, 'FinalWeightRetCoarse', 3) ?>" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse4" id="PercentagePassingCoarse4" value="<?= getValue($Search, 'PercentagePassingCoarse', 3) ?>" readonly tabindex="-1" /></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse4" id="WeightedLossCoarse4" value="<?= getValue($Search, 'WeightedLossCoarse', 3) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>63 mm (2 1⁄2 in) to 50 mm (2 in)</td>
                    <td>(3000+-300)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse7" id="StarWeightRetCoarse7" value="<?= getValue($Search, 'StarWeightRetCoarse', 6) ?>" /></td>
                  </tr>
                  <tr>
                    <td>75 mm (3 in) to 63 mm (2 1⁄2 in)</td>
                    <td>(7000+-100)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse8" id="StarWeightRetCoarse8" value="<?= getValue($Search, 'StarWeightRetCoarse', 7) ?>" /></td>
                    <td>50 mm (2 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse5" id="FinalWeightRetCoarse5" value="<?= getValue($Search, 'FinalWeightRetCoarse', 4) ?>" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse5" id="PercentagePassingCoarse5" value="<?= getValue($Search, 'PercentagePassingCoarse', 4) ?>" readonly tabindex="-1" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse5" id="WeightedLossCoarse5" value="<?= getValue($Search, 'WeightedLossCoarse', 4) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>90 mm (3 1⁄2 in) to 75 mm (3 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse9" id="StarWeightRetCoarse9" value="<?= getValue($Search, 'StarWeightRetCoarse', 8) ?>" /></td>
                    <td>63 mm (2 1⁄2 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse6" id="FinalWeightRetCoarse6" value="<?= getValue($Search, 'FinalWeightRetCoarse', 5) ?>" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse6" id="PercentagePassingCoarse6" value="<?= getValue($Search, 'PercentagePassingCoarse', 5) ?>" readonly tabindex="-1" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse6" id="WeightedLossCoarse6" value="<?= getValue($Search, 'WeightedLossCoarse', 5) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td>100 mm (4 in) to 90 mm (3 1⁄2 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse10" id="StarWeightRetCoarse10" value="<?= getValue($Search, 'StarWeightRetCoarse', 9) ?>" /></td>
                    <td>75 mm (3 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse7" id="FinalWeightRetCoarse7" value="<?= getValue($Search, 'FinalWeightRetCoarse', 6) ?>" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse7" id="PercentagePassingCoarse7" value="<?= getValue($Search, 'PercentagePassingCoarse', 6) ?>" readonly tabindex="-1" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse7" id="WeightedLossCoarse7" value="<?= getValue($Search, 'WeightedLossCoarse', 6) ?>" readonly tabindex="-1" /></td>
                  </tr>
                  <tr>
                    <td colspan="2">Totals</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalStarWeightRetCoarse" id="TotalStarWeightRetCoarse" value="<?php echo ($Search['TotalStarWeightRetCoarse']); ?>" readonly tabindex="-1" /></td>
                    <td>---</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalFinalWeightRetCoarse" id="TotalFinalWeightRetCoarse" value="<?php echo ($Search['TotalFinalWeightRetCoarse']); ?>" readonly tabindex="-1" /></td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalWeightedLossCoarse" id="TotalWeightedLossCoarse" value="<?php echo ($Search['TotalWeightedLossCoarse']); ?>" readonly tabindex="-1" /></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Soundness Test of Coarse Aggregate -->

            </div>
          </div>
        </div>
        <!-- End Table for Soundness Fine and Coarse Aggregate -->

        <!-- Actions -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>

              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-snd">Update Essay</button>
              </div>

            </div>
          </div>
        </div>
        <!-- End Actions -->

      </form>
    </div>
  </section>
</main>
<!-- End #main -->

<script src="../js/Soundness.js"></script>
<?php include_once('../components/footer.php');  ?>