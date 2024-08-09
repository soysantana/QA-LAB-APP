<?php
  $page_title = 'Point Load';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('point_load', $_GET['id']);
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Point Load</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Point Load</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section">
    <div class="row" oninput="PLT()">

    <form class="row" action="../database/point-load.php?id=<?php echo $Search['id']; ?>" method="post" enctype="multipart/form-data">

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
                  <option <?php if ($Search['Standard'] == 'ASTM-D5731') echo 'selected'; ?>>ASTM-D5731</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="MethodTest" class="form-label">Test Method</label>
                <select id="MethodTest" class="form-select" name="TestMethod">
                  <option selected>Choose...</option>
                  <option <?php if ($Search['Methods'] == 'Diametral') echo 'selected'; ?>>Diametral</option>
                  <option <?php if ($Search['Methods'] == 'Axial') echo 'selected'; ?>>Axial</option>
                  <option <?php if ($Search['Methods'] == 'Block') echo 'selected'; ?>>Block</option>
                  <option <?php if ($Search['Methods'] == 'Irregular Lump') echo 'selected'; ?>>Irregular Lump</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="ExEquip" class="form-label">Extraction Equipment:</label>
                <input type="text" class="form-control" id="ExEquip" name="ExEquip" value="<?php echo ($Search['Extraction_Equipment']); ?>" />
              </div>
              <div class="col-md-6">
                <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
                <input type="text" class="form-control" id="CutterEquip" name="CuttEquip" value="<?php echo ($Search['Cutter_Equipment']); ?>" />
              </div>
              <div class="col-md-6">
                <label for="Technician" class="form-label">Technician</label>
                <input type="text" class="form-control" id="Technician" name="Technician" value="<?php echo ($Search['Technician']); ?>" />
              </div>
              <div class="col-md-6">
                <label for="DateTesting" class="form-label">Date of Testing</label>
                <input type="date" class="form-control" id="DateTesting" name="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>" />
              </div>
              <div class="col-12">
                <label for="Comments" class="form-label">Comments</label>
                <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"><?php echo ($Search['Comments']); ?></textarea>
              </div>
            </div>
            <!-- End Multi Columns Form -->
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Point Load Test Device Values</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Effective Area of Jack Piston (m²)</th>
                  <td><input type="text" style="border: none;" class="form-control" id="JackPiston" value="<?php echo ($Search['JackPiston']); ?>" name="JackPiston" /></td>
                </tr>
                <tr>
                  <th scope="row">k₁ value (assumed value to correlate Is50 to UCS):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="K1assumed" value="<?php echo ($Search['K1assumed']); ?>" name="K1assumed" /></td>
                </tr>
                <tr>
                  <th scope="row">k₂ value (assumed):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="K2assumed" value="<?php echo ($Search['K2assumed']); ?>" name="K2assumed" /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Testing Information</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row" colspan="3">Test Type (A, B, C, D):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="TypeABCD" name="TypeABCD" value="<?php echo ($Search['TypeABCD']); ?>" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">Dimension L (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionL" name="DimensionL" value="<?php echo ($Search['DimensionL']); ?>" /></td>
                  <th scope="row">Dimension D or W (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionD" name="DimensionD" value="<?php echo ($Search['DimensionD']); ?>" /></td>
                </tr>
                <tr>
                  <th scope="row">Plattens Separation (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="PlattensSeparation" name="PlattensSeparation" value="<?php echo ($Search['PlattensSeparation']); ?>" /></td>
                  <th scope="row">Load Direction:</th>
                  <td>
                    <select id="LoadDirection" class="form-select" name="LoadDirection">
                      <option selected>Choose...</option>
                      <option <?php if ($Search['LoadDirection'] == 'Perpendicular') echo 'selected'; ?>>⊥</option>
                      <option <?php if ($Search['LoadDirection'] == 'Parallel') echo 'selected'; ?>>//</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Gauge Reading (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="GaugeReading" name="GaugeReading" value="<?php echo ($Search['GaugeReading']); ?>" /></td>
                  <th scope="row">Failure Load (MN):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="FailureLoad" name="FailureLoad" value="<?php echo ($Search['FailureLoad']); ?>" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">De (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Demm" name="Demm" value="<?php echo ($Search['Demm']); ?>" readonly tabindex="-1" /></td>
                  <th scope="row">Is (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="IsMpa" name="IsMpa" value="<?php echo ($Search['IsMpa']); ?>" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">F:</th>
                  <td><input type="text" style="border: none;" class="form-control" id="F" name="F" value="<?php echo ($Search['F']); ?>" readonly tabindex="-1" /></td>
                  <th scope="row">Is 50:</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Is50" name="Is50" value="<?php echo ($Search['Is50']); ?>" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">UCS From k1 (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSK1Mpa" name="UCSK1Mpa" value="<?php echo ($Search['UCSK1Mpa']); ?>" readonly tabindex="-1" /></td>
                  <th scope="row">UCS From k2 (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSK2Mpa" name="UCSK2Mpa" value="<?php echo ($Search['UCSK2Mpa']); ?>" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row" colspan="3">Strenght Classification :</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Classification" name="Classification" value="<?php echo ($Search['Classification']); ?>" readonly tabindex="-1" /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Actions</h5>
            <!-- Actions Buttons -->
            <div class="d-grid gap-2 mt-3">
              <button type="submit" class="btn btn-success" name="Update_PLT">Update Essay</button>
              <button type="submit" class="btn btn-primary" name="Repeat_PLT">Repeat</button>
              <button type="submit" class="btn btn-primary" name="Reviewed_PLT">Reviewed</button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen Before Test</h5>
            <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
            <div id="imageContainerBefore" class="image-container mt-3">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['SpecimenBefore']); ?>" alt="SpecimenBefore" class="img-fluid" width="300px">
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen After Test</h5>
            <input class="form-control" type="file" name="SpecimenAfter" onchange="showImage(this, 'after')" />
            <div id="imageContainerAfter" class="image-container mt-3">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['SpecimenAfter']); ?>" alt="SpecimenAfter" class="img-fluid" width="300px">
            </div>
          </div>
        </div>
      </div>

      </form>

    </div>
  </section>

</main>
<!-- End #main -->

<script src="../js/Point-Load.js"></script>
<?php include_once('../components/footer.php');  ?>