<?php
$page_title = 'Specific Gravity Fine';
require_once('../config/load.php');
$Search = find_by_id('specific_gravity_fine', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-sg-fine'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['repeat-sg-fine'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['reviewed-sg-fine'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['delete_sg_fine'])) {
    include('../database/specific-gravity.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Specific Gravity Fine Aggregate</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Specific Gravity Fine</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="specific-gravity-fine-aggregate.php?id=<?php echo $Search['id']; ?>" method="post">

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
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'ASTM-D854') echo 'selected'; ?>>ASTM-D854</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
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
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-7">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="SGFINE()">
                <tbody>

                  <?php
                  $datos = array(
                    "A)Pycnometer Number :",
                    "B)Weight of Pycnometer(g): ",
                    "C)Weight of Dry Soil + Tare (g):",
                    "D)Weight of Dry Soil (g):",
                    "E)Weight of Saturated Surface Dry Soil in Air (g):",
                    "F)Temperature of Sample (°C) :",
                    "G)Weight of Pycnometer + Soil + Water (g):",
                    "H)Calibration Weight of Pycnometer at Desired Temperature (g):"
                  );

                  $id = array("PycnoNumber", "WeightPycno", "WeightDryTare", "WeightDry", "WeightSurfaceAir", "TempSample", "WeightPycnoWater", "CalibrationPycno");
                  $DBid = array("Pycnometer_Number", "Weight_Pycnometer", "Weight_Dry_Soil_Tare", "Weight_Dry_Soil", "Weight_Saturated_Surface_Dry_Soil_Air", "Temperature_Sample", "Weight_Pycnometer_Soil_Water", "Calibration_Weight_Pycnometer_Desired_Temperature");

                  foreach ($datos as $indice => $dato) {
                    echo "<tr>";
                    echo "<th scope='row'>$dato</th>";
                    echo "<td><input type='text' style='border: none;' class='form-control' name='{$id[$indice]}' id='{$id[$indice]}' value='" . $Search[$DBid[$indice]] . "'></td>";
                    echo "</tr>";
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
        </div>


        <div class="col-lg-5">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Results</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col">Specific Gravity D/(H+E-G)=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravityOD" id="SpecificGravityOD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_OD']); ?>"></td>
                    <th scope="col">Specific Gravity (SSD) (E/(H+E-G)=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravitySSD" id="SpecificGravitySSD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_SSD']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="col">Apparent Specific Gravity D/(H+E-G) =</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ApparentSpecificGravity" id="ApparentSpecificGravity" readonly tabindex="-1" value="<?php echo ($Search['Apparent_Specific_Gravity']); ?>"></td>
                    <th scope="col">Percent of Absortion (E-D)/D*100</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PercentAbsortion" id="PercentAbsortion" readonly tabindex="-1" value="<?php echo ($Search['Percent_Absortion']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>

          <div class="col-lg-7">

            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Actions</h5>
                <!-- Actions Buttons -->
                <div class="d-grid gap-2 mt-3">
                  <button type="submit" class="btn btn-success" name="update-sg-fine">Update Essay</button>

                  <div class="btn-group dropup" role="group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-printer"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="../pdf/SG-FF-Naranjo.php?id=<?php echo ($Search['id']); ?>">Naranjo</a></li>
                      <li><a class="dropdown-item" href="../pdf/SG-FF-Build.php?id=<?php echo ($Search['id']); ?>">Contruccion</a></li>
                    </ul>
                  </div>

                  <button type="submit" class="btn btn-danger" name="delete_sg_fine"><i class="bi bi-trash"></i></button>
                </div>

                <div class="btn-group mt-2" role="group">
                  <?php if (user_can_access(1)): ?>
                    <button type="submit" class="btn btn-primary" name="repeat-sg-fine">Repeat</button>
                    <button type="submit" class="btn btn-primary" name="reviewed-sg-fine">Reviewed</button>
                  <?php endif; ?>
                </div>

              </div>
            </div>

          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/Specific-Gravity.js"></script>
<?php include_once('../components/footer.php');  ?>