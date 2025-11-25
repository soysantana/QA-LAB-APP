<?php
$page_title = 'Sand Castle Test';
require_once('../config/load.php');
$Search = find_by_id('sand_castle_test', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update'])) {
    include('../database/sand-castle/update.php');
  } elseif (isset($_POST['reviewed'])) {
    include('../database/sand-castle/reviewed.php');
  } elseif (isset($_POST['repeat'])) {
    include('../database/sand-castle/repeat.php');
  } elseif (isset($_POST['delete'])) {
    include('../database/sand-castle/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Sand Castle Test (SCT)</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Forms</a></li>
        <li class="breadcrumb-item active">Sand Castle</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="sand-castle-test.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <!-- Test Information -->
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-3">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option <?php if ($Search['Standard'] == 'PV-15-10') echo 'selected'; ?>>PV-15-10</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried" <?php if ($Search['Preparation_Method'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                    <option value="Air Dried" <?php if ($Search['Preparation_Method'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                    <option value="Microwave Dried" <?php if ($Search['Preparation_Method'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                    <option value="Wet" <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual" <?php if ($Search['Split_Method'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    <option value="Mechanical" <?php if ($Search['Split_Method'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="natMc" class="form-label">Natural MC (%)</label>
                  <input type="text" class="form-control" name="natMc" id="natMc" value="<?php echo ($Search['natMc']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="optimunMc" class="form-label">Optimun MC (%)</label>
                  <input type="text" class="form-control" name="optimunMc" id="optimunMc" value="<?php echo ($Search['optimunMc']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div>

            </div>
          </div>

        </div>
        <!-- End Test Information -->

        <!-- Collapsed -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Results</h5>

              <table class="table table-bordered" id="collapsedTable">
                <tbody>
                  <tr>
                    <th scope="row">Times of:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Time" id="Time" value="<?php echo $Search['Time']; ?>"></td>
                  </tr>
                  <?php
                  $collapsedArray = explode(',', $Search['Collapsed']);
                  $collapsedArray = array_filter($collapsedArray, fn($v) => $v !== 'null');

                  foreach ($collapsedArray as $index => $value) {
                    $num = $index + 1;
                    echo "
                    <tr class='collapsed-row'>
                    <th scope='row'>{$num}st Collapsed</th>
                    <td><input type='text' class='form-control' name='Collapsed[]' id='Collapsed_{$num}' value='" . (($value !== 'null') ? $value : '') . "' style='border: none;'></td>
                    </tr>";
                  }
                  ?>
                  <tr id="timeSetRow">
                    <th scope="row">Time of Set (Min):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TimeSet" id="TimeSet"  value="<?php echo $Search['TimeSet']; ?>"></td>
                  </tr>
                </tbody>
              </table>

              <button type="button" class="btn btn-secondary" id="addCollapsed">+ Agregar Collapsed</button>
            </div>
          </div>
        </div>
        <!-- End Collapsed -->

        <!-- Approximate & Final Height -->
        <div class="col-lg-4">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Approximate Initial Height (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="initialHeight" id="initialHeight" value="<?php echo $Search['initialHeight']; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">(A) Final Height (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="FinalHeight" id="FinalHeight" value="<?php echo $Search['FinalHeight']; ?>"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>

          <!-- Spread Redius Measures & Slope Angle Calculation  -->
          <div class="">

            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Spread Radius Measures</h5>

                <table class="table table-bordered">
                  <thead>
                    <th class="text-center">Measure No.</th>
                    <th class="text-center">mm</th>
                  </thead>
                  <tbody>
                    <?php
                    $RadiusValues             = explode(',',  str_replace('null', '', $Search["Radius"]));
                    for ($i = 1; $i <= 5; $i++) {
                      $RadiusValue       = isset($RadiusValues[$i - 1]) ? trim($RadiusValues[$i - 1]) : '';
                      echo '<tr>';
                      echo '<th class="text-center" scope="row">' . $i . '</th>';
                      echo '<td><input type="text" style="border: none;" class="text-center form-control" name="Radius' . $i . '" id="Radius' . $i . '" value="' . $RadiusValue . '"></td>';
                      echo '</tr>';
                    }

                    ?>
                  </tbody>
                </table>

                <h5 class="card-title">Slope Angle Calculation </h5>

                <table class="table table-bordered">
                  <thead>
                    <th class="text-center">Angle No.</th>
                    <th class="text-center">Degrees</th>
                  </thead>
                  <tbody>
                    <?php
                    $AngleValues             = explode(',',  str_replace('null', '', $Search["Angle"]));
                    for ($i = 1; $i <= 5; $i++) {
                      $AngleValue       = isset($AngleValues[$i - 1]) ? trim($AngleValues[$i - 1]) : '';
                      echo '<tr>';
                      echo '<th class="text-center" scope="row">' . $i . '</th>';
                      echo '<td><input type="text" style="border: none;" class="text-center form-control" name="Angle' . $i . '" id="Angle' . $i . '" value="' . $AngleValue . '" readonly tabindex="-1"></td>';
                      echo '</tr>';
                    }

                    ?>
                    <th class="text-center" scope="row">Average</th>
                    <td><input type="text" style="border: none;" class="text-center form-control" name="Average" id="Average" value="<?= $Search['Average']; ?>" readonly tabindex="-1"></td>
                  </tbody>
                </table>


              </div>
            </div>

          </div>
          <!-- End Spread Redius Measures & Slope Angle Calculation -->

        </div>
        <!-- End Approximate & Final Height -->


        <!-- Actions Buttons -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update">Actualizar</button>
                <button type="button" class="btn btn-primary" name="search">Buscar Humedad</button>
                <div id="mensaje-container"></div>
                <a href="../pdf/SCT-Build.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
                <button type="submit" class="btn btn-danger" name="delete"><i class="bi bi-trash"></i></button>
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed">Reviewed</button>
                <?php endif; ?>
              </div>

              <h5 class="card-title"></h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Test Result</th>
                    <td><input type="text" style="border: none;" class="form-control" name="testResult" id="testResult" value="<?= $Search['testResult']; ?>"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script type="module" src="../js/sand-castle/sand-castle-sct.js"></script>
<?php include_once('../components/footer.php');  ?>