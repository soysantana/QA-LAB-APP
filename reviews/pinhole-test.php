<?php
$page_title = 'Pinhole Test';
require_once('../config/load.php');
$Search = find_by_id('pinhole_test', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['Update_PH'])) {
    include('../database/pinhole-test.php');
  } elseif (isset($_POST['Repeat_PH'])) {
    include('../database/pinhole-test.php');
  } elseif (isset($_POST['Reviewed_PH'])) {
    include('../database/pinhole-test.php');
  } elseif (isset($_POST['delete_ph'])) {
    include('../database/pinhole-test.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Pinhole Test</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Pinhole Test</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row" oninput="Pinhole()">

      <form class="row" action="pinhole-test.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <!-- Multi Columns Form -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option value="ASTM-D4647">ASTM-D4647</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
                </div>
                <div class="col-12">
                  <textarea hidden class="form-control" name="Graph" id="Graph" style="height: 100px;"><?php echo ($Search['Graph']); ?></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Moisture Content before test MC(%)</th>
                    <th scope="col"><input type="text" style="border: none;" class="form-control" id="mcBefore" name="mcBefore" value="<?php echo ($Search['MC_Before_Test']); ?>" readonly tabindex="-1"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">Specific Gravity (Estimated or Measured)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="sgEM" name="sgEM" value="<?php echo ($Search['Specific_Gravity']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Max Dry Density (g/cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="maxDryDensity" name="maxDryDensity" value="<?php echo ($Search['Max_Dry_Density']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Optimum Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="optimumMC" name="optimumMC" value="<?php echo ($Search['Optimum_MC']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="welSoilMold" name="welSoilMold" value="<?php echo ($Search['Wet_Soil_Mold']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wtMold" name="wtMold" value="<?php echo ($Search['Wet_Mold']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wtWetSoil" name="wtWetSoil" value="<?php echo ($Search['Wet_Soil']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Logitud Del Specimen (cm)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="longitudSpecimen" name="longitudSpecimen" value="<?php echo ($Search['Specimen_Length']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Vol Specimen (cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="volSpecimen" name="volSpecimen" value="<?php echo ($Search['Vol_Specimen']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wet Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wetDensity" name="wetDensity" value="<?php echo ($Search['Wet_Density']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="dryDensityGCM3" name="dryDensityGCM3" value="<?php echo ($Search['Dry_Density']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">% Compaction</th>
                    <td><input type="text" style="border: none;" class="form-control" id="porceCompaction" name="porceCompaction" value="<?php echo ($Search['Porce_Compaction']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content after test, %</th>
                    <td><input type="text" style="border: none;" class="form-control" id="mcAfter" name="mcAfter" value="<?php echo ($Search['MC_After_Test']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wire Punch Diameter (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wirePuchDiameter" name="wirePuchDiameter" value="<?php echo ($Search['Wire_Punch_Diameter']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
        </div>

        <div class="col-lg-7">

          <div class="row">
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">MC Before</h5>
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <th scope="row">Tare Name</th>
                        <td><input type="text" style="border: none;" class="form-control" id="nameBefore" name="nameBefore" value="<?php echo ($Search['Tare_Name_MC_Before']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Oven Temperature (°C)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="tempBefore" name="tempBefore" value="<?php echo ($Search['Oven_Temp_MC_Before']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare Plus Wet Soil (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="wetSoil1" name="wetSoil1" value="<?php echo ($Search['Tare_Wet_Soil_MC_Before']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare Plus Dry Soil (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="drySoil1" name="drySoil1" value="<?php echo ($Search['Tare_Dry_Soil_MC_Before']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Water, Ww (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="water1" name="water1" value="<?php echo ($Search['Water_MC_Before']); ?>" readonly tabindex="-1"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="tare1" name="tare1" value="<?php echo ($Search['Tare_MC_Before']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Dry Soil, Ws (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="drySoilWs1" name="drySoilWs1" value="<?php echo ($Search['Dry_Soil_MC_Before']); ?>" readonly tabindex="-1"></td>
                      </tr>
                      <tr>
                        <th scope="row">Moisture Content (%)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="mc1" name="mc1" value="<?php echo ($Search['Porce_MC_Before']); ?>" readonly tabindex="-1"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">MC After</h5>
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <th scope="row">Tare Name</th>
                        <td><input type="text" style="border: none;" class="form-control" id="nameAfter" name="nameAfter" value="<?php echo ($Search['Tare_Name_MC_After']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Oven Temperature (°C)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="tempAfter" name="tempAfter" value="<?php echo ($Search['Oven_Temp_MC_After']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare Plus Wet Soil (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="wetSoil2" name="wetSoil2" value="<?php echo ($Search['Tare_Wet_Soil_MC_After']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare Plus Dry Soil (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="drySoil2" name="drySoil2" value="<?php echo ($Search['Tare_Dry_Soil_MC_After']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Water, Ww (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="water2" name="water2" value="<?php echo ($Search['Water_MC_After']); ?>" readonly tabindex="-1"></td>
                      </tr>
                      <tr>
                        <th scope="row">Tare (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="tare2" name="tare2" value="<?php echo ($Search['Tare_MC_After']); ?>"></td>
                      </tr>
                      <tr>
                        <th scope="row">Dry Soil, Ws (g)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="drySoilWs2" name="drySoilWs2" value="<?php echo ($Search['Dry_Soil_MC_After']); ?>" readonly tabindex="-1"></td>
                      </tr>
                      <tr>
                        <th scope="row">Moisture Content (%)</th>
                        <td><input type="text" style="border: none;" class="form-control" id="mc2" name="mc2" value="<?php echo ($Search['Porce_MC_After']); ?>" readonly tabindex="-1"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <!-- Multi Point Liquid Limit Plot Chart -->
              <div id="Pinhole" style="min-height: 430px;" class="echart"></div>
              <!-- End Multi Point Liquid Limit Plot Chart -->

            </div>
          </div>

        </div>

        <div class="col-lg-10">

          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="row" colspan="9">Flow</th>
                    </tr>
                    <tr>
                      <th scope="col">Head</th>
                      <th scope="col">mL</th>
                      <th scope="col">Seg</th>
                      <th scope="col">Flow Rate</th>
                      <th scope="col">From Side</th>
                      <th scope="col">From Top</th>
                      <th scope="col">Observation</th>
                      <th scope="col">Hole Size After Test mm</th>
                      <th scope="col">Dispersive Classification1</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    for ($i = 1; $i <= 22; $i++) {
                      if ($i == 1) {
                        // Primera fila de 12 rows con rowspan
                        echo '<tr>';
                        echo '<th scope="row" rowspan="12">50</th>';
                      } elseif ($i == 13) {
                        // Primera fila de 5 rows con rowspan
                        echo '<tr>';
                        echo '<th scope="row" rowspan="5">180</th>';
                      } elseif ($i == 18) {
                        // Primera fila de las últimas 5 rows con rowspan
                        echo '<tr>';
                        echo '<th scope="row" rowspan="5">380</th>';
                      } else {
                        echo '<tr>';
                      }

                      echo '<td><input type="text" style="border: none;" class="form-control" id="ML_' . $i . '" name="ML_' . $i . '" value="' . $Search['ML_' . $i] . '"></td>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="Seg_' . $i . '" name="Seg_' . $i . '" value="' . $Search['Seg_' . $i] . '"></td>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_' . $i . '" name="Flow_Rate_' . $i . '" value="' . $Search['Flow_Rate_' . $i] . '"></td>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="From_Side_' . $i . '" name="From_Side_' . $i . '" value="' . $Search['From_Side_' . $i] . '"></td>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="From_Top_' . $i . '" name="From_Top_' . $i . '" value="' . $Search['From_Top_' . $i] . '"></td>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="Observation_' . $i . '" name="Observation_' . $i . '" value="' . $Search['Observation_' . $i] . '"></td>';

                      if ($i == 1) {
                        // Celdas con rowspan de 22 rows
                        echo '<td rowspan="22"><input type="text" style="border: none;" class="form-control" id="Hole_Size_After" name="Hole_Size_After" value="' . $Search['Hole_Size_After'] . '"></td>';
                        echo '<td rowspan="22"><input type="text" style="border: none;" class="form-control" id="Dispersive_Classification" name="Dispersive_Classification" value="' . $Search['Dispersive_Classification'] . '"></td>';
                      }

                      echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>

              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="Update_PH">Update Essay</button>
                <a href="../pdf/Pinhole-Naranjo.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
                <button type="submit" class="btn btn-danger" name="delete_ph"><i class="bi bi-trash"></i></button>
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="Repeat_PH">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="Reviewed_PH">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>

      </form><!-- End Form -->

    </div>
  </section>

</main><!-- End #main -->


<script src="../js/Pinhole-Test.js"></script>
<script src="../libs/graph/Pinhole-Test.js"></script>
<?php include_once('../components/footer.php');  ?>