<?php
  $page_title = 'Soundness Test';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('soundness', $_GET['id']);
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Update_SND'])) {
        include('../database/soundness.php');
    } elseif (isset($_POST['Repeat_SND'])) {
        include('../database/soundness.php');
    } elseif (isset($_POST['Reviewed_SND'])) {
        include('../database/soundness.php');
    }
  }
?>

<?php page_require_level(1); ?>
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
    <div class="row" oninput="Soundness()">
      <form class="row" action="soundness.php?id=<?php echo $Search['id']; ?>" method="post">

      <div class="col-md-4"><?php echo display_msg($msg); ?></div>

      <div id="product_info">
       <div class="col-lg-12">
       <div class="card">
        <div class="card-body">
          <h5 class="card-title">Sample Information</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="ProjectName" class="form-label">Project Name</label>
              <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo ($Search['Project_Name']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Client" class="form-label">Client</label>
              <input type="text" class="form-control" name="Client" id="Client" value="<?php echo ($Search['Client']); ?>">
            </div>
            <div class="col-md-4">
              <label for="ProjectNumber" class="form-label">Project Number</label>
              <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo ($Search['Project_Number']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Structure" class="form-label">Structure</label>
              <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo ($Search['Structure']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Area" class="form-label">Work Area</label>
              <input type="text" class="form-control" name="Area" id="Area" value="<?php echo ($Search['Area']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Source" class="form-label">Borrow Source</label>
              <input type="text" class="form-control" name="Source" id="Source" value="<?php echo ($Search['Source']); ?>">
            </div>
            <div class="col-md-4">
              <label for="MType" class="form-label">Material Type</label>
              <input type="text" class="form-control" name="MType" id="MType" value="<?php echo ($Search['Material_Type']); ?>">
            </div>
            <div class="col-md-4">
              <label for="SType" class="form-label">Sample Type</label>
              <input type="text" class="form-control" name="SType" id="SType" value="<?php echo ($Search['Sample_Type']); ?>">
            </div>
            <div class="col-md-4">
              <label for="SampleName" class="form-label">Sample Name</label>
              <input type="text" class="form-control" name="SampleName" id="SampleName" value="<?php echo ($Search['Sample_ID']); ?>">
            </div>
            <div class="col-md-4">
              <label for="SampleNumber" class="form-label">Sample Number</label>
              <input type="text" class="form-control" name="SampleNumber" id="SampleNumber" value="<?php echo ($Search['Sample_Number']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Sample Date" class="form-label">Sample Date</label>
              <input type="text" class="form-control" name="CollectionDate" id="Sample Date" value="<?php echo ($Search['Sample_Date']); ?>">
            </div>
            <div class="col-md-4">
              <label for="SampleBy" class="form-label">Sample By</label>
              <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo ($Search['Sample_By']); ?>">
            </div>
            <div class="col-md-4">
              <label for="Depth From" class="form-label">Depth From</label>
              <input type="text" class="form-control" name="DepthFrom" id="Depth From" value="<?php echo ($Search['Depth_From']); ?>">
            </div>
            <div class="col-md-2">
              <label for="Depth To" class="form-label">Depth To</label>
              <input type="text" class="form-control" name="DepthTo" id="Depth To" value="<?php echo ($Search['Depth_To']); ?>">
            </div>
            <div class="col-md-2">
              <label for="North" class="form-label">North</label>
              <input type="text" class="form-control" name="North" id="North" value="<?php echo ($Search['North']); ?>">
            </div>
            <div class="col-md-2">
              <label for="East" class="form-label">East</label>
              <input type="text" class="form-control" name="East" id="East" value="<?php echo ($Search['East']); ?>">
            </div>
            <div class="col-md-2">
              <label for="Elevation" class="form-label">Elevation</label>
              <input type="text" class="form-control" name="Elev" id="Elevation" value="<?php echo ($Search['Elev']); ?>">
            </div>
          </div>
        </div>
       </div>
       </div>
      </div>

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <!-- Multi Columns Form -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == '') echo 'selected'; ?>></option>
                    <option <?php if ($Search['Standard'] == 'ASTM-C88') echo 'selected'; ?>>ASTM-C88</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>" />
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>" />
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div>
              <!-- End Multi Columns Form -->
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grain Size Distribution</h5>

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

<?php
// Definimos los datos de la tabla
$data = [
    ["Screen" => '4"', "mm" => 100],
    ["Screen" => '3 1/2"', "mm" => 90],
    ["Screen" => '3"', "mm" => 75],
    ["Screen" => '2 1/2"', "mm" => 63],
    ["Screen" => '2"', "mm" => 50],
    ["Screen" => '1 1/2"', "mm" => 38],
    ["Screen" => '1"', "mm" => 25],
    ["Screen" => '3/4"', "mm" => 19],
    ["Screen" => '1/2"', "mm" => 12.5],
    ["Screen" => '3/8"', "mm" => 9.5],
    ["Screen" => 'No. 4', "mm" => 4.75],
    ["Screen" => 'No. 8', "mm" => 2.36],
    ["Screen" => 'No. 16', "mm" => 1.18],
    ["Screen" => 'No. 30', "mm" => 0.6],
    ["Screen" => 'No. 50', "mm" => 0.3],
    ["Screen" => 'No. 100', "mm" => 0.15],
    ["Screen" => 'Pan', "mm" => ""],
    ["Screen" => 'Total Pan', "mm" => ""],
    ["Screen" => 'Total', "mm" => ""],
];

echo '<table class="table table-bordered">';
echo '<thead>';
echo '  <tr>';
echo '    <th scope="col">Screen</th>';
echo '    <th scope="col">(mm)</th>';
echo '    <th scope="col">Wt. Ret</th>';
echo '    <th scope="col">% Ret</th>';
echo '  </tr>';
echo '</thead>';
echo '<tbody>';

$counter = 1; // Inicializamos un contador

// Iteramos sobre los datos para crear las filas de la tabla
foreach ($data as $row) {
    // Verificamos si la fila es "Pan", "Total Pan" o "Total" y asignamos ID personalizados
    if ($row['Screen'] === 'Pan') {
        $wtRetId = 'WtRetPan';
        $pctRetId = 'PctRetPan';
    } elseif ($row['Screen'] === 'Total Pan') {
        $wtRetId = 'WtRetTotalPan';
        $pctRetId = 'PctRetTotalPan';
    } elseif ($row['Screen'] === 'Total') {
        $wtRetId = 'WtRetTotal';
        $pctRetId = 'PctRetTotal';
    } else {
        $wtRetId = 'WtRet' . $counter;
        $pctRetId = 'PctRet' . $counter;
    }
    
    echo '<tr>';
    echo '<th scope="row">' . $row['Screen'] . '</th>';
    echo '<th>' . $row['mm'] . '</th>';
    echo '<td><input type="text" style="border: none" class="form-control" name="' . $wtRetId . '" id="' . $wtRetId . '" value="' . $Search[$wtRetId] . '" /></td>';
    echo '<td><input type="text" style="border: none" class="form-control" name="' . $pctRetId . '" id="' . $pctRetId . '" value="' . $Search[$pctRetId] . '" readonly tabindex="-1" /></td>';
    echo '</tr>';
    $counter++; // Incrementamos el contador en cada iteración
}

echo '</tbody>';
echo '</table>';
?>




              <!-- End Bordered Table -->
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grain Size Distribution for Soundness</h5>

              <?php
// Define the data for the table
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

<table class="table table-bordered">
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
        <?php $i = 1; foreach ($sizes as $size => $mm): ?>
        <tr>
            <th scope="row"><?php echo htmlspecialchars($size); ?></th>
            <th><?php echo htmlspecialchars($mm); ?></th>
            <td>
                <input type="text" style="border: none" class="form-control" name="WtRetCoarse<?php echo $i; ?>" id="WtRetCoarse<?php echo $i; ?>" value="<?php echo ($Search['WtRetCoarse' .$i]); ?>" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="PctRetCoarse<?php echo $i; ?>" id="PctRetCoarse<?php echo $i; ?>" value="<?php echo ($Search['PctRetCoarse' .$i]); ?>" readonly tabindex="-1" />
            </td>
        </tr>
        <?php $i++; endforeach; ?>
        <tr>
            <th scope="row" colspan="2">Total</th>
            <td>
                <input type="text" style="border: none" class="form-control" name="WtRetCoarseTotal" id="WtRetCoarseTotal" value="<?php echo ($Search['WtRetCoarseTotal']); ?>" readonly tabindex="-1" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="PctRetCoarseTotal" id="PctRetCoarseTotal" value="<?php echo ($Search['PctRetCoarseTotal']); ?>" readonly tabindex="-1" />
            </td>
        </tr>
    </tbody>
</table>



<?php
// Define the data for the table
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

<table class="table table-bordered">
    <tbody>
        <tr>
            <td colspan="4">Fine Aggregate</td>
        </tr>
        <?php $i = 1; foreach ($sizes as $size => $mm): ?>
        <tr>
            <th scope="row"><?php echo htmlspecialchars($size); ?></th>
            <th><?php echo htmlspecialchars($mm); ?></th>
            <td>
                <input type="text" style="border: none" class="form-control" name="WtRetFine<?php echo $i; ?>" id="WtRetFine<?php echo $i; ?>" value="<?php echo ($Search['WtRetFine' .$i]); ?>" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="PctRetFine<?php echo $i; ?>" id="PctRetFine<?php echo $i; ?>" value="<?php echo ($Search['PctRetFine' .$i]); ?>" readonly tabindex="-1" />
            </td>
        </tr>
        <?php $i++; endforeach; ?>
        <tr>
            <th scope="row" colspan="2">Total</th>
            <td>
                <input type="text" style="border: none" class="form-control" name="WtRetFineTotal" id="WtRetFineTotal" value="<?php echo ($Search['WtRetFineTotal']); ?>" readonly tabindex="-1" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="PctRetFineTotal" id="PctRetFineTotal" value="<?php echo ($Search['PctRetFineTotal']); ?>" readonly tabindex="-1" />
            </td>
        </tr>
    </tbody>
</table>


            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Information</h5>

              <?php
// Define the data for the table
$cycles = range(1, 5); // Array of cycle numbers
?>

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
        <?php foreach ($cycles as $cycle): ?>
        <tr>
            <th><?php echo htmlspecialchars($cycle); ?></th>
            <td>
                <input type="date" style="border: none" class="form-control" name="StartDate<?php echo $cycle; ?>" id="StartDate<?php echo $cycle; ?>" value="<?php echo ($Search['StartDate' .$cycle]); ?>" tabindex="-1" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="RoomTemp<?php echo $cycle; ?>" id="RoomTemp<?php echo $cycle; ?>" value="<?php echo ($Search['RoomTemp' .$cycle]); ?>" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="SolutionTemp<?php echo $cycle; ?>" id="SolutionTemp<?php echo $cycle; ?>" value="<?php echo ($Search['SolutionTemp' .$cycle]); ?>" />
            </td>
            <td>
                <input type="text" style="border: none" class="form-control" name="SpecificGravity<?php echo $cycle; ?>" id="SpecificGravity<?php echo $cycle; ?>" value="<?php echo ($Search['SpecificGravity' .$cycle]); ?>" />
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

        <div class="col-lg-7">
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
        // Define sieve sizes from 1 to 8
        $sieveSizes = [
            "37.5mm(11⁄2 in) to 19.0 mm (3/4 in)",
            "63 mm (2 1⁄2 in) to 37.5 mm (1 1⁄2 in)",
            "75 mm (3 in) to 63 mm (2 1⁄2 in)",
            "91 mm (3 1⁄2 in) to 75 mm (3 in)",
            "100 mm (4 in) to 90 mm (3 1⁄2 in)"
        ];

        foreach ($sieveSizes as $index => $size) {
            echo '<tr>';
            echo '<th>' . ($size) . '</th>'; // Use numbers 1 to 8
            echo '<td><input type="text" style="border: none" class="form-control" name="SplittingNo' . ($index + 1) . '" id="SplittingNo' . ($index + 1) . '" value="' . $Search['SplittingNo' . ($index + 1)] . '" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="SplittingPct' . ($index + 1) . '" id="SplittingPct' . ($index + 1) . '" value="' . $Search['SplittingPct' . ($index + 1)] . '" readonly tabindex="-1" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="CrumblingNo' . ($index + 1) . '" id="CrumblingNo' . ($index + 1) . '" value="' . $Search['CrumblingNo' . ($index + 1)] . '" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="CrumblingPct' . ($index + 1) . '" id="CrumblingPct' . ($index + 1) . '" value="' . $Search['CrumblingPct' . ($index + 1)] . '" readonly tabindex="-1" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="CrackingNo' . ($index + 1) . '" id="CrackingNo' . ($index + 1) . '" value="' . $Search['CrackingNo' . ($index + 1)] . '" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="CrackingPct' . ($index + 1) . '" id="CrackingPct' . ($index + 1) . '" value="' . $Search['CrackingPct' . ($index + 1)] . '" readonly tabindex="-1" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="FlakingNo' . ($index + 1) . '" id="FlakingNo' . ($index + 1) . '" value="' . $Search['FlakingNo' . ($index + 1)] . '" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="FlakingPct' . ($index + 1) . '" id="FlakingPct' . ($index + 1) . '" value="' . $Search['FlakingPct' . ($index + 1)] . '" readonly tabindex="-1" /></td>';
            echo '<td><input type="text" style="border: none" class="form-control" name="TotalParticles' . ($index + 1) . '" id="TotalParticles' . ($index + 1) . '" value="' . $Search['TotalParticles' . ($index + 1)] . '" /></td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>



            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Results</h5>
              <!-- Soundness Test of Fine Aggregate -->
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

        <?php foreach ($rows as $index => $row): ?>
<tr>
    <?php for ($i = 0; $i < $cols; $i++): ?>
        <td>
            <?php if ($i == 2 || $i == 5): // Campos sin readonly ?>
                <input type="text" style="border: none" class="form-control" 
                       name="<?= $row[$i] ?>" id="<?= $row[$i] ?>" value="<?= $Search[$row[$i]] ?>"/>
            <?php elseif ($i == 6 || $i == 7): // PercentagePassing y WeightedLoss ?>
                <input type="text" style="border: none" class="form-control" 
                       name="<?= $row[$i] ?>" id="<?= $row[$i] ?>" value="<?= $Search[$row[$i]] ?>" readonly tabindex="-1"/>
            <?php else: ?>
                <?= $row[$i] ?>
            <?php endif; ?>
        </td>
    <?php endfor; ?>
</tr>
<?php endforeach; ?>


        <tr>
            <td colspan="2">Totals</td>
            <td><input type="text" style="border: none" class="form-control" name="TotalStarWeightRet" id="TotalStarWeightRet" value="<?php echo ($Search['TotalStarWeightRet']); ?>" readonly tabindex="-1"/></td>
            <td>---</td>
            <td>---</td>
            <td><input type="text" style="border: none" class="form-control" name="TotalFinalWeightRet" id="TotalFinalWeightRet" value="<?php echo ($Search['TotalFinalWeightRet']); ?>" readonly tabindex="-1"/></td>
            <td></td>
            <td><input type="text" style="border: none" class="form-control" name="TotalWeightedLoss" id="TotalWeightedLoss" value="<?php echo ($Search['TotalWeightedLoss']); ?>" readonly tabindex="-1"/></td>
        </tr>
    </tbody>
</table>


              
              <!-- Soundness Test of Fine Aggregate -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th colspan="8">Soundness Test of Coarse Aggregate</th>
                  </tr>
                  <tr>
                    <td>9.5mm(3⁄8 in.) to 4.75 mm (No. 4)</td>
                    <td>(300+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse1" id="StarWeightRetCoarse1" value="<?php echo ($Search['StarWeightRetCoarse1']); ?>"/></td>
                    <td>---</td>
                    <td>4.0 mm (No. 5)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse1" id="FinalWeightRetCoarse1" value="<?php echo ($Search['FinalWeightRetCoarse1']); ?>"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse1" id="PercentagePassingCoarse1" value="<?php echo ($Search['PercentagePassingCoarse1']); ?>" readonly tabindex="-1"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse1" id="WeightedLossCoarse1" value="<?php echo ($Search['WeightedLossCoarse1']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td>12.5 mm (1⁄2 in) to 9.5 mm (3⁄8 in)</td>
                    <td>(330+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse2" id="StarWeightRetCoarse2" value="<?php echo ($Search['StarWeightRetCoarse2']); ?>"/></td>
                    <td rowspan="2">19.0mm(3⁄4 in) to 9.5mm(3⁄8 in)</td>
                    <td rowspan="2">8.0 mm (5⁄16 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse2" id="FinalWeightRetCoarse2" value="<?php echo ($Search['FinalWeightRetCoarse2']); ?>"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse2" id="PercentagePassingCoarse2" value="<?php echo ($Search['PercentagePassingCoarse2']); ?>" readonly tabindex="-1"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse2" id="WeightedLossCoarse2" value="<?php echo ($Search['WeightedLossCoarse2']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td>19.0mm(3⁄4 in) to 12.5 mm (1⁄2 in)</td>
                    <td>(670+-10)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse3" id="StarWeightRetCoarse3" value="<?php echo ($Search['StarWeightRetCoarse3']); ?>"/></td>
                  </tr>
                  <tr>
                    <td>25 mm (1 in) to 19.0 mm (3⁄4 in)</td>
                    <td>(500+-30)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse4" id="StarWeightRetCoarse4" value="<?php echo ($Search['StarWeightRetCoarse4']); ?>"/></td>
                    <td rowspan="2">37.5mm(1 1⁄2 in) to 19.0mm(3⁄4 in)</td>
                    <td rowspan="2">16.0 mm (5⁄8 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse3" id="FinalWeightRetCoarse3" value="<?php echo ($Search['FinalWeightRetCoarse3']); ?>"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse3" id="PercentagePassingCoarse3" value="<?php echo ($Search['PercentagePassingCoarse3']); ?>" readonly tabindex="-1"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse3" id="WeightedLossCoarse3" value="<?php echo ($Search['WeightedLossCoarse3']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td>37.5mm(11⁄2 in) to 25.0 mm (1 in.)</td>
                    <td>(1000+-50)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse5" id="StarWeightRetCoarse5" value="<?php echo ($Search['StarWeightRetCoarse5']); ?>"/></td>
                  </tr>
                  <tr>
                    <td>50 mm (2 in) to 37.5 mm (1 1⁄2 in)</td>
                    <td>(2000+-200)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse6" id="StarWeightRetCoarse6" value="<?php echo ($Search['StarWeightRetCoarse6']); ?>"/></td>
                    <td rowspan="2">63mm(2 1⁄2 in) to 37.5mm(1 1⁄2 in)</td>
                    <td rowspan="2">31.5 mm (1 1⁄4 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse4" id="FinalWeightRetCoarse4" value="<?php echo ($Search['FinalWeightRetCoarse4']); ?>"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse4" id="PercentagePassingCoarse4" value="<?php echo ($Search['PercentagePassingCoarse4']); ?>" readonly tabindex="-1"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse4" id="WeightedLossCoarse4" value="<?php echo ($Search['WeightedLossCoarse4']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td>63 mm (2 1⁄2 in) to 50 mm (2 in)</td>
                    <td>(3000+-300)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse7" id="StarWeightRetCoarse7" value="<?php echo ($Search['StarWeightRetCoarse7']); ?>"/></td>
                  </tr>
                  <tr>
                    <td>75 mm (3 in) to 63 mm (2 1⁄2 in)</td>
                    <td>(7000+-100)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse8" id="StarWeightRetCoarse8" value="<?php echo ($Search['StarWeightRetCoarse8']); ?>"/></td>
                    <td rowspan="3"> 100mm(4 in.) to  90mm(2 1⁄2 in.)</td>
                    <td>50 mm (2 in)</td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse5" id="FinalWeightRetCoarse5" value="<?php echo ($Search['FinalWeightRetCoarse5']); ?>"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse5" id="PercentagePassingCoarse5" value="<?php echo ($Search['PercentagePassingCoarse5']); ?>" readonly tabindex="-1"/></td>
                    <td rowspan="2"><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse5" id="WeightedLossCoarse5" value="<?php echo ($Search['WeightedLossCoarse5']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td>90 mm (3 1⁄2 in) to 75 mm (3 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse9" id="StarWeightRetCoarse9" value="<?php echo ($Search['StarWeightRetCoarse9']); ?>"/></td>
                    <td>63 mm (2 1⁄2 in)</td>
                  </tr>
                  <tr>
                    <td>100 mm (4 in) to 90 mm (3 1⁄2 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="StarWeightRetCoarse10" id="StarWeightRetCoarse10" value="<?php echo ($Search['StarWeightRetCoarse10']); ?>"/></td>
                    <td>75 mm (3 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="FinalWeightRetCoarse6" id="FinalWeightRetCoarse6" value="<?php echo ($Search['FinalWeightRetCoarse6']); ?>"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PercentagePassingCoarse6" id="PercentagePassingCoarse6" value="<?php echo ($Search['PercentagePassingCoarse6']); ?>" readonly tabindex="-1"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="WeightedLossCoarse6" id="WeightedLossCoarse6" value="<?php echo ($Search['WeightedLossCoarse6']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                  <tr>
                    <td colspan="2">Totals</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalWeightRetCoarse" id="TotalWeightRetCoarse" value="<?php echo ($Search['TotalWeightRetCoarse']); ?>" readonly tabindex="-1"/></td>
                    <td>---</td>
                    <td>---</td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalFinalWeightRetCoarse" id="TotalFinalWeightRetCoarse" value="<?php echo ($Search['TotalFinalWeightRetCoarse']); ?>" readonly tabindex="-1"/></td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="TotalWeightedLossCoarse" id="TotalWeightedLossCoarse" value="<?php echo ($Search['TotalWeightedLossCoarse']); ?>" readonly tabindex="-1"/></td>
                  </tr>
                </tbody>
              </table>

              <!-- End Default Table Example -->
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="Update_SND">Update Essay</button>
                <button type="submit" class="btn btn-primary" name="Repeat_SND">Repeat</button>
                <button type="submit" class="btn btn-primary" name="Reviewed_SND">Reviewed</button>
              </div>

            </div>
          </div>
        </div>

      </form>
      <!-- End Form -->
    </div>
  </section>
</main>
<!-- End #main -->

<script src="../js/Soundness.js"></script>
<?php include_once('../components/footer.php');  ?>