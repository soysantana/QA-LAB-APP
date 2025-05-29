<?php
$page_title = 'Specific Gravity Coarse';
require_once('../config/load.php');
$Search = find_by_id('specific_gravity_coarse', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-sg-coarse'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['repeat-sg-coarse'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['reviewed-sg-coarse'])) {
    include('../database/specific-gravity.php');
  } elseif (isset($_POST['delete_sg_coarse'])) {
    include('../database/specific-gravity.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Specific Gravity Coarse Aggregate</h1>
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

      <form class="row" action="specific-gravity-coarse-aggregates.php?id=<?php echo $Search['id']; ?>" method="post">

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
                    <option <?php if ($Search['Standard'] == 'ASTM-C127') echo 'selected'; ?>>ASTM-C127</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                  <div class="col-md-6">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Split_Method'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    <option <?php if ($Search['Split_Method'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                  </select>
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
              <table class="table table-bordered" oninput="SGCOARSE()">
                <thead>
                  <tr>
                    <th scope="col" colspan="2">Oven Dry (A)</th>
                    <th scope="col" colspan="2">Saturated Surface Dry (B)</th>
                    <th scope="col" colspan="2">Sample immersed (C)</th>
                  </tr>
                  <tr>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array("3.5", "3", "2.5", "2", "1.5", "1", "3/4", "1/2", "3/8", "No. 4", "Total");

                  for ($i = 1; $i < count($datos); $i++) {
                    echo "<tr>";

                    echo "<th scope='row'>$datos[$i]</th>";

                    echo "<td><input type='text' style='border: none;' class='form-control' name='OvenDry{$i}' id='OvenDry{$i}' value='" . $Search["Oven_Dry_{$i}"] . "'></td>";

                    echo "<th scope='row'>$datos[$i]</th>";

                    echo "<td><input type='text' style='border: none;' class='form-control' name='SurfaceDry{$i}' id='SurfaceDry{$i}' value='" . $Search["Surface_Dry_{$i}"] . "'></td>";

                    echo "<th scope='row'>$datos[$i]</th>";

                    echo "<td><input type='text' style='border: none;' class='form-control' name='SampImmers{$i}' id='SampImmers{$i}' value='" . $Search["Samp_Immers_{$i}"] . "'></td>";

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
                    <th scope="col">Specific Gravity (OD) (A/(B-C))=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravityOD" id="SpecificGravityOD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_OD']); ?>"></td>
                    <th scope="col">Specific Gravity (SSD) (B/(B-C))=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravitySSD" id="SpecificGravitySSD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_SSD']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="col">Apparent Specific Gravity (A/(A-C))=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ApparentSpecificGravity" id="ApparentSpecificGravity" readonly tabindex="-1" value="<?php echo ($Search['Apparent_Specific_Gravity']); ?>"></td>
                    <th scope="col">Percent of Absortion ((B-A)/A)*100=</th>
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
                  <button type="submit" class="btn btn-success" name="update-sg-coarse">Update Essay</button>

                  <div class="btn-group dropup" role="group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-printer"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="../pdf/SG-CF-Naranjo.php?id=<?php echo ($Search['id']); ?>">Naranjo</a></li>
                      <li><a class="dropdown-item" href="../pdf/SG-CF-Build.php?id=<?php echo ($Search['id']); ?>">Contruccion</a></li>
                    </ul>
                  </div>

                  <button type="submit" class="btn btn-danger" name="delete_sg_coarse"><i class="bi bi-trash"></i></button>
                </div>

                <div class="btn-group mt-2" role="group">
                  <?php if (user_can_access(1)): ?>
                    <button type="submit" class="btn btn-primary" name="repeat-sg-coarse">Repeat</button>
                    <button type="submit" class="btn btn-primary" name="reviewed-sg-coarse">Reviewed</button>
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