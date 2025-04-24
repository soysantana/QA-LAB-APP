<?php
$page_title = 'Moisture Content';
require_once('../config/load.php');
$Search = find_by_id('moisture_oven', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-mc-oven'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['repeat-mc-oven'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['reviewed-mc-oven'])) {
    include('../database/moisture-content.php');
  } elseif (isset($_POST['delete_mc_oven'])) {
    include('../database/moisture-content.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Moisture Oven</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Moisture Oven</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="moisture-oven.php?id=<?php echo $Search['id']; ?>" method="post">

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
                    <option <?php if ($Search['Standard'] == 'ASTM-D2216') echo 'selected'; ?>>ASTM-D2216</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="OvenTemp" class="form-label">Oven Temperature (°C)</label>
                  <select id="OvenTemp" class="form-select" name="OvenTemp">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Temperature'] == '110 °C') echo 'selected'; ?>>110 °C</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod" value="<?php echo ($Search['Method']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" id="Technician" name="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" id="DateTesting" name="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"><?php echo ($Search['Comments']); ?></textarea>
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
              <table class="table table-bordered" oninput="MoistureOven()">
                <tbody>
                  <tr>
                    <th scope="row">Tare Name</th>
                    <td><input type="text" style="border: none;" class="form-control" id="TareName" name="TareName" value="<?php echo ($Search['Tare_Name']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WetSoil" name="WetSoil" value="<?php echo ($Search['Tare_Plus_Wet_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Dry Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="DrySoil" name="DrySoil" value="<?php echo ($Search['Tare_Plus_Dry_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Water, Ww (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Water" name="Water" readonly tabindex="-1" value="<?php echo ($Search['Water_Ww']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Tare" name="Tare" value="<?php echo ($Search['Tare_g']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Soil, Ws (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="DrySoilWs" name="DrySoilWs" readonly tabindex="-1" value="<?php echo ($Search['Dry_Soil_Ws']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Moisture" name="Moisture" readonly tabindex="-1" value="<?php echo ($Search['Moisture_Content_Porce']); ?>"></td>
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
                <button type="submit" class="btn btn-success" name="update-mc-oven">Update Essay</button>

                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../pdf/MC-Oven-Naranjo.php?id=<?php echo ($Search['id']); ?>">Naranjo</a></li>
                    <li><a class="dropdown-item" href="../pdf/MC-Oven-Build.php?id=<?php echo ($Search['id']); ?>">Contruccion</a></li>
                  </ul>
                </div>

                <button type="submit" class="btn btn-danger" name="delete_mc_oven"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="button" class="btn btn-primary open-modal-btn" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed-mc-oven">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

          <div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title"><?php echo $Search['Sample_ID'] . '-' . $Search['Sample_Number']; ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-md-12">
                      <label for="Rcomment" class="form-label">Comment</label>
                      <input type="text" class="form-control" name="Rcomment" id="Rcomment">
                    </div>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success" name="repeat-mc-oven">Repeat</button>
                </div>

              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script>
  function modaldelete() {
    // Utiliza el método modal() de Bootstrap para mostrar el modal
    $('#ModalDelete').modal('show');
  }

  function Delete() {
    // Verifica si se ha guardado un ID
    if (selectedId !== undefined) {
      // Concatena el ID al final de la URL en el atributo 'action' del formulario
      document.getElementById("deleteForm").action = "../database/sample-tracking.php?id=" + selectedId;

      // Envía el formulario
      document.getElementById("deleteForm").submit();
    } else {
      console.log('No se ha seleccionado ningún ID para eliminar.');
    }
  }
</script>

<script src="../js/Moisture-Content.js"></script>
<?php include_once('../components/footer.php');  ?>