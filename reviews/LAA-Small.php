<?php
  $page_title = 'Los Angeles Abrasion';
  require_once('../config/load.php');
  $Search = find_by_id('los_angeles_abrasion_coarse_filter', $_GET['id']);
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Update_LAA_Coarse_Filter'])) {
        include('../database/los-angeles-abrasion-coarse-filter.php');
    } elseif (isset($_POST['Repeat_LAA_Coarse_Filter'])) {
        include('../database/los-angeles-abrasion-coarse-filter.php');
    } elseif (isset($_POST['Reviewed_LAA_Coarse_Filter'])) {
        include('../database/los-angeles-abrasion-coarse-filter.php');
    } elseif (isset($_POST['delete_LAA_Coarse_Filter'])) {
        include('../database/los-angeles-abrasion-coarse-filter.php');
    }
  }
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Los Angeles Abrasion For Small Size Coarse</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Los Angeles Abrasion</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <form action="LAA-Small.php?id=<?php echo $Search['id']; ?>" method="post" class="row">

  <div class="col-md-7">
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
                <option <?php if ($Search['Standard'] == 'ASTM-C131') echo 'selected'; ?>>ASTM-C131</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod" name="TestMethod" value="<?php echo ($Search['Methods']); ?>">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician" name="Technician" value="<?php echo ($Search['Technician']); ?>">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting" name="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"><?php echo ($Search['Comments']); ?></textarea>
            </div>
          </div>

        </div>
      </div>

    </div>

    <div class="col-lg-5">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Selected Grading</th>
                    <td>
                        <select class="form-control" id="SelectGrading" name="SelectGrading">
                            <option selected>Choose...</option>
                            <option <?php if ($Search['Grading'] == 'A') echo 'selected'; ?>>A</option>
                            <option <?php if ($Search['Grading'] == 'B') echo 'selected'; ?>>B</option>
                            <option <?php if ($Search['Grading'] == 'C') echo 'selected'; ?>>C</option>
                            <option <?php if ($Search['Grading'] == 'D') echo 'selected'; ?>>D</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of the Spheres (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigSpheres" name="WeigSpheres" value="<?php echo ($Search['Weight_Spheres']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Revolutions</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Revolutions" name="Revolutions" value="<?php echo ($Search['Revolutions']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>
    </div>

    <div class="col-lg-7">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Results</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="laaLarge()">
                <tbody>
                  <tr>
                    <th scope="row">Initial Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="InitWeig" name="InitWeig" value="<?php echo ($Search['Initial_Weight']); ?>"></td>
                    <th scope="row">Final Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="FinalWeig" name="FinalWeig" value="<?php echo ($Search['Final_Weight']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight Loss (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLoss" name="WeigLoss" value="<?php echo ($Search['Weight_Loss']); ?>" readonly tabindex="-1"></td>
                    <th scope="row">Weight Loss (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLossPorce" name="WeigLossPorce" value="<?php echo ($Search['Weight_Loss_Porce']); ?>" readonly tabindex="-1"></td>
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
          <button type="submit" class="btn btn-success" name="Update_LAA_Coarse_Filter">Update Essay</button>
          <a href="../pdf/laa-cf.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
          <button type="submit" class="btn btn-danger" name="delete_LAA_Coarse_Filter"><i class="bi bi-trash"></i></button>
          <?php if (user_can_access(1)): ?>
          <button type="submit" class="btn btn-primary" name="Repeat_LAA_Coarse_Filter">Repeat</button>
          <button type="submit" class="btn btn-primary" name="Reviewed_LAA_Coarse_Filter">Reviewed</button>
          <?php endif; ?>
        </div>

      </div>
    </div>
  
  </div>
  </form><!-- End Multi Columns Form -->
  </div>
</section>

</main><!-- End #main -->

<script src="../js/Los-Angeles-Abrasion.js"></script>
<?php include_once('../components/footer.php');  ?>