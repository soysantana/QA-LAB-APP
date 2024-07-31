<?php
  $page_title = 'Unixial Compresive Strenght';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('unixial_compressive', $_GET['id']);
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Unixial Compresive Strenght</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">UCS</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section">
    <div class="row">

    <form class="row" action="../database/unixial-compressive.php" method="post" enctype="multipart/form-data">

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
                  <option <?php if ($Search['Standard'] == 'ASTM-D7012') echo 'selected'; ?>>ASTM-D7012</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="TestDevice" class="form-label">Test Devices</label>
                <input type="text" class="form-control" id="TestDevice" name="TestDevice" value="<?php echo ($Search['Test_Device']); ?>" />
              </div>
              <div class="col-md-6">
                <label for="ExEquip" class="form-label">Extraction Equipment:</label>
                <input type="text" class="form-control" id="ExEquip" name="ExEquip" value="<?php echo ($Search['Extraction_Equipment']); ?>" />
              </div>
              <div class="col-md-6">
                <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
                <input type="text" class="form-control" id="CutterEquip" name="CutterEquip" value="<?php echo ($Search['Cutter_Equipment']); ?>" />
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

      <div class="col-lg-8" oninput="UCS()">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Testing Information</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row" colspan="5">Compressive Strenght of Intact Rock Core Specimens</th>
                </tr>
                <tr>
                  <th scope="row">Dimension D (cm)</th>
                  <th scope="row">Dimension H (cm)</th>
                  <th scope="row">Relation h/D</th>
                  <th scope="row">Area (m²)</th>
                  <th scope="row">Volume (m³)</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionD" name="DimensionD" value="<?php echo ($Search['DimensionD']); ?>" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionH" name="DimensionH" value="<?php echo ($Search['DimensionH']); ?>" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="RelationHD" name="RelationHD" value="<?php echo ($Search['RelationHD']); ?>" readonly tabindex="-1" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="AreaM2" name="AreaM2" value="<?php echo ($Search['AreaM2']); ?>" readonly tabindex="-1"  /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="VolM3" name="VolM3" value="<?php echo ($Search['VolM3']); ?>" readonly tabindex="-1"  /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->

            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Weight of the Core (Kg)</th>
                  <th scope="row">Unit Weight of the Core (Kg/m³)</th>
                  <th scope="row">Failure Load (KN)</th>
                  <th scope="row">Test Timing (s)</th>
                  <th scope="row">Load Proportion (Mpa/s)</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="WeightKg" name="WeightKg" value="<?php echo ($Search['WeightKg']); ?>" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="UnitWeigKgm3" name="UnitWeigKgm3" value="<?php echo ($Search['UnitWeigKgm3']); ?>" readonly tabindex="-1" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="FailLoadKn" name="FailLoadKn" value="<?php echo ($Search['FailLoadKn']); ?>" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="TestTimingS" name="TestTimingS" value="<?php echo ($Search['TestTimingS']); ?>" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="LoadpMpas" name="LoadpMpas" value="<?php echo ($Search['LoadpMpas']); ?>" /></td>
                </tr>
              </tbody>
            </table>

            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Uniaxial Compressive Strenght (Mpa)</th>
                  <th scope="row">Failure Type</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSMpa" name="UCSMpa" value="<?php echo ($Search['UCSMpa']); ?>" /></td>
                  <td>
                    <select id="FailureType" class="form-select" name="FailureType">
                      <option selected>Choose...</option>
                      <option <?php if ($Search['FailureType'] == 'Simple extension') echo 'selected'; ?>>Simple extension</option>
                      <option <?php if ($Search['FailureType'] == 'Simple shear') echo 'selected'; ?>>Simple shear</option>
                      <option <?php if ($Search['FailureType'] == 'Multiple extension') echo 'selected'; ?>>Multiple extension</option>
                      <option <?php if ($Search['FailureType'] == 'Multiple fracturing') echo 'selected'; ?>>Multiple fracturing</option>
                      <option <?php if ($Search['FailureType'] == 'Multiple shear') echo 'selected'; ?>>Multiple shear</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
      <div class="card">
          <div class="card-body">
            <h5 class="card-title">Actions</h5>
            <!-- Actions Buttons -->
            <div class="d-grid gap-2 mt-3">
              <button type="submit" name="update-unixial-compressive" class="btn btn-success">Save Essay</button>
            </div>
          </div>
        </div>
      </div>

        <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Graphic Failure Load versus Time</h5>
            <input class="form-control" type="file" name="Graphic" onchange="showImage(this, 'graphic')" />
            <div id="imageContainerGraphic" class="image-container mt-3">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['Graphic']); ?>" alt="Graphic Failure" class="img-fluid" width="300px">
            </div>
          </div>
        </div>
        </div>

        <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen Before Test</h5>
            <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
            <div id="imageContainerBefore" class="image-container mt-3">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['SpecimenBefore']); ?>" alt="SpecimenBefore" class="img-fluid" width="300px">
            </div>
          </div>
        </div>
        </div>

        <div class="col-lg-4">
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

    </div>
  </section>

</main>
<!-- End #main -->

<script src="../js/unixial-compressive.js"></script>
<?php include_once('../components/footer.php');  ?>