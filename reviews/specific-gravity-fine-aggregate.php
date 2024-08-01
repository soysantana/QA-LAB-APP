<?php
  $page_title = 'Specific Gravity Fine';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('specific_gravity_fine', $_GET['id']);
?>

<?php page_require_level(1); ?>
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

    <div class="col-lg-6">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="update-sg-fine">Update Essay</button>
          <a href="../pdf/sg-fine.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
        </div>

        <div class="btn-group mt-2" role="group">
          <button type="submit" class="btn btn-primary" name="repeat-sg-fine">Repeat</button>
          <button type="submit" class="btn btn-primary" name="reviewed-sg-fine">Reviewed</button>
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