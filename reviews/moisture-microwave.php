<?php
$page_title = 'Moisture Microwave';
require_once('../config/load.php');
$Search = find_by_id('moisture_microwave', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-microwave'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['repeat-mc-microwave'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['reviewed-mc-microwave'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['delete_mc_microwave'])) {
    include('../database/moisture-content.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Moisture Microwave</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Moisture Microwave</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="moisture-microwave.php?id=<?php echo $Search['id']; ?>" method="post">

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
                    <option <?php if ($Search['Standard'] == 'ASTM-D4643') echo 'selected'; ?>>ASTM-D4643</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Method']); ?>">
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

        <div class="col-lg-8">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="MoistureMicrowave()">
                <tbody>
                  <tr>
                    <th scope="row">Tare Name</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TareName" id="TareName" value="<?php echo ($Search['Tare_Name']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Microwave model</th>
                    <td><input type="text" style="border: none;" class="form-control" name="MicrowaveModel" id="MicrowaveModel" value="<?php echo ($Search['Microwave_Model']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil1" id="WetSoil" value="<?php echo ($Search['Tare_Plus_Wet_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 1</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil2" id="WetSoil1" value="<?php echo ($Search['Tare_Plus_Wet_Soil_1']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 2</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil3" id="WetSoil2" value="<?php echo ($Search['Tare_Plus_Wet_Soil_2']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 3</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil4" id="WetSoil3" value="<?php echo ($Search['Tare_Plus_Wet_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 4</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil5" id="WetSoil4" value="<?php echo ($Search['Tare_Plus_Wet_Soil_4']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 5</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil6" id="WetSoil5" value="<?php echo ($Search['Tare_Plus_Wet_Soil_5']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Water, Ww (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Water" id="Water" readonly tabindex="-1" value="<?php echo ($Search['Water_Ww']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Tare" id="Tare" value="<?php echo ($Search['Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Soil, Ws (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs" id="DrySoilWs" readonly tabindex="-1" value="<?php echo ($Search['Dry_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Moisture" id="Moisture" readonly tabindex="-1" value="<?php echo ($Search['Moisture_Content_Porce']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
        </div>


        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-microwave">Update Essay</button>
                <a href="../pdf/MC-Microwave.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
                <button type="submit" class="btn btn-danger" name="delete_mc_microwave"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat-mc-microwave">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed-mc-microwave">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/Moisture-Content.js"></script>
<?php include_once('../components/footer.php');  ?>