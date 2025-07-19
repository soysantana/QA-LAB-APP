<?php
$page_title = 'Los Angeles Abrasion';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['save'])) {
    include('../database/LAA/small/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Los Angeles Abrasion For Small Size Coarse</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="LAA-menu.php">Forms</a></li>
        <li class="breadcrumb-item active">Los Angeles Abrasion</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form action="LAA-Small.php" method="post" class="row">

        <div class="col-md-7">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <div id="product_info"></div>
        <!-- End Sample Information -->

        <!-- Laboratory Information -->
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option value="ASTM-C131">ASTM-C131</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" id="Technician" name="Technician">
                </div>
                <div class="col-md-4">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod">
                </div>
                <div class="col-md-4">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried">Oven Dried</option>
                    <option value="Air Dried">Air Dried</option>
                    <option value="Microwave Dried">Microwave Dried</option>
                    <option value="Wet">Wet</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual">Manual</option>
                    <option value="Mechanical">Mechanical</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" id="DateTesting" name="DateTesting">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" id="Comments" name="Comments" style="height: 100px;"></textarea>
                </div>
              </div>

            </div>
          </div>

        </div>
        <!-- End Laboratory Information -->

        <!-- Testing Information -->
        <div class="col-lg-5">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Nominal Maximum Size</th>
                    <td><input type="text" style="border: none;" class="form-control" id="NominalMaxSize" name="NominalMaxSize"></td>
                  </tr>
                  <tr>
                    <th scope="row">Selected Grading</th>
                    <td>
                      <select class="form-control" id="SelectGrading" name="SelectGrading">
                        <option selected>Choose...</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of the Spheres (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigSpheres" name="WeigSpheres"></td>
                  </tr>
                  <tr>
                    <th scope="row">Revolutions</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Revolutions" name="Revolutions"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
        <!-- End Testing Information -->

        <!-- Results for the testing -->
        <div class="col-lg-7">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Results</h5>

              <table class="table table-bordered" oninput="laaLarge()">
                <tbody>
                  <tr>
                    <th scope="row">Initial Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="InitWeig" name="InitWeig"></td>
                    <th scope="row">Final Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="FinalWeig" name="FinalWeig"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight Loss (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLoss" name="WeigLoss" readonly tabindex="-1"></td>
                    <th scope="row">Weight Loss (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLossPorce" name="WeigLossPorce" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
        <!-- End Results for the testing -->

        <!-- Actions Buttons -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>

              <div class="d-grid gap-2 mt-3">
                <button type="submit" name="save" class="btn btn-success">Save Essay</button>
              </div>

            </div>
          </div>

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/Los-Angeles-Abrasion.js"></script>
<?php include_once('../components/footer.php');  ?>