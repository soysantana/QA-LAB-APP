<?php
  $page_title = 'Specific Gravity';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('specific_gravity', $_GET['id']);
?>

<?php page_require_level(1); ?>
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

  <form class="row" action="../database/specific-gravity.php?id=<?php echo $Search['id']; ?>" method="post">

  <div class="col-md-4">
  <?php echo display_msg($msg); ?>
  </div>

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

    <div class="col-lg-8">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="SGSOIL()">
                <tbody>
                  <tr>
                    <th scope="row">Pycnometer used (mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnUsed" id="PycnUsed" value="<?php echo ($Search['Pycnometer_Used']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Pycnometer Number</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnNumber" id="PycnNumber" value="<?php echo ($Search['Pycnometer_Number']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Test Temperatur Tt (°C)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TestTemp" id="TestTemp" value="<?php echo ($Search['Test_Temperatur']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average calibrated mass of the dry pycnometer Mp (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="MassDryPycn" id="MassDryPycn" value="<?php echo ($Search['Average_Calibrated_Mass_Dry_Pycnometer_Mp']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average calibrated volume of the pycnometer Vp (mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="VolumePycn" id="VolumePycn" readonly tabindex="-1" value="<?php echo ($Search['Average_Calibrated_Volume_Pycnometer_Vp']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Density of water at the test temperature (g/mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DensityWaterTemp" id="DensityWaterTemp" readonly tabindex="-1" value="<?php echo ($Search['Density_Water_Test_Temperature']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Calibration Weight of Pynometer and water athe the calibration temperature Mpw,c (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnWaterTemp" id="PycnWaterTemp" value="<?php echo ($Search['Calibration_Weight_Pynometer_Temperature_Mpw']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Dry Soil + Tare (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightTare" id="WeightTare" value="<?php echo ($Search['Weight__Dry_Soil_Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Dry Soil Ms (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightSoil" id="WeightSoil" readonly tabindex="-1" value="<?php echo ($Search['Weight_Dry_Soil_Ms']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of Pycnometer + Soil + Water Mpws,t (gr):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightPycnSoilWaterMpws" id="WeightPycnSoilWaterMpws" value="<?php echo ($Search['Weight_Pycnometer_Soil_Water_Mpws']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Test Temperatur Tt (°C)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TestTempAfter" id="TestTempAfter" value="<?php echo ($Search['Test_Temperatur_After']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Density of water at the test temperature (g/mL)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DensityWaterTempAfter" id="DensityWaterTempAfter" readonly tabindex="-1" value="<?php echo ($Search['Density_Water_Test_Temperature_After']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Calibration Weight of Pynometer and water athe the calibration temperature Mpw,c (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PycnWaterTempAfter" id="PycnWaterTempAfter" readonly tabindex="-1" value="<?php echo ($Search['Calibration_Weight_Pynometer_Temp_After']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Specific Gravity of Soil Solid the test temp Gt</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SgSoilTemp" id="SgSoilTemp" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_Soil_Solid_Test_Temp_Gt']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Temperature Coefficent K</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TempCoefficent" id="TempCoefficent" readonly tabindex="-1" value="<?php echo ($Search['Temperature_Coefficent_K']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Specific gravity of soil solid at 20 °C</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SgSolid" id="SgSolid" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_Soil_Solid']); ?>"></td>
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
          <button type="submit" class="btn btn-success" name="update-sg">Update Essay</button>
          <a href="../pdf/sg.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
        </div>

        <div class="btn-group mt-2" role="group">
          <button type="submit" class="btn btn-primary" name="repeat-sg">Repeat</button>
          <button type="submit" class="btn btn-primary" name="reviewed-sg">Reviewed</button>
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