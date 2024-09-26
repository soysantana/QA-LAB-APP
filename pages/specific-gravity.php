<?php
  $page_title = 'Specific Gravity';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['specific-gravity'])) {
        include('../database/specific-gravity.php');
    } 
  }
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Specific Gravity</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Specific Gravity</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <form class="row" action="specific-gravity.php" method="post">

  <div class="col-md-4">
  <?php echo display_msg($msg); ?>
  </div>

  <div id="product_info"></div>

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
                <option value="ASTM-D854">ASTM-D854</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" name="TestMethod" id="TestMethod">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" name="Technician" id="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" name="DateTesting" id="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
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
              <table class="table table-bordered" oninput="SGSOIL()">
                <tbody>
                  <tr>
                    <th scope="row">Pycnometer used (mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnUsed" id="PycnUsed"></td>
                  </tr>
                  <tr>
                    <th scope="row">Pycnometer Number</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnNumber" id="PycnNumber"></td>
                  </tr>
                  <tr>
                    <th scope="row">Test Temperatur Tt (°C)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TestTemp" id="TestTemp"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average calibrated mass of the dry pycnometer Mp (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="MassDryPycn" id="MassDryPycn"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average calibrated volume of the pycnometer Vp (mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="VolumePycn" id="VolumePycn" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Density of water at the test temperature (g/mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DensityWaterTemp" id="DensityWaterTemp" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Calibration Weight of Pynometer and water athe the calibration temperature Mpw,c (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnWaterTemp" id="PycnWaterTemp"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Dry Soil + Tare (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightTare" id="WeightTare"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Dry Soil Ms (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightSoil" id="WeightSoil" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Pycnometer + Soil + Water Mpws,t (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightPycnSoilWaterMpws" id="WeightPycnSoilWaterMpws"></td>
                  </tr>
                  <tr>
                    <th scope="row">Test Temperatur Tt (°C)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TestTempAfter" id="TestTempAfter"></td>
                  </tr>
                  <tr>
                    <th scope="row">Density of water at the test temperature (g/mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DensityWaterTempAfter" id="DensityWaterTempAfter" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Calibration Weight of Pynometer and water athe the calibration temperature Mpw,c (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnWaterTempAfter" id="PycnWaterTempAfter" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Specific Gravity of Soil Solid the test temp Gt</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SgSoilTemp" id="SgSoilTemp" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Temperature Coefficent K</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TempCoefficent" id="TempCoefficent" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Specific gravity of soil solid at 20 °C</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SgSolid" id="SgSolid" readonly tabindex="-1"></td>
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
          <button type="submit" class="btn btn-success" name="specific-gravity">Save Essay</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true">SG Options</button>
        </div>

      </div>
    </div>
  
  </div>

  </form>

  </div>
</section>

</main><!-- End #main -->


<div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hey! select an option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <li>
                    <a href="specific-gravity-coarse-aggregates.php"
                        <span>Specific Gravity Coarse Filter</span>
                    </a>
                </li>
                <li>
                    <a href="specific-gravity-fine-aggregate.php"
                        <span>Specific Gravity Fine Filter</span>
                    </a>
                </li>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/Specific-Gravity.js"></script>
<?php include_once('../components/footer.php');  ?>