<?php
$page_title = 'Moisture Content';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['moisture-oven'])) {
    include('../database/moisture-content.php');
  }
}
?>

<?php page_require_level(2); ?>
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

      <form class="row" action="moisture-oven.php" method="post">

        <?php echo display_msg($msg); ?>

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
                    <option value="ASTM-D2216">ASTM-D2216</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="OvenTemp" class="form-label">Oven Temperature (°C)</label>
                  <select id="OvenTemp" class="form-select" name="OvenTemp">
                    <option selected>Choose...</option>
                    <option value="110 °C">110 °C</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod">
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" id="Technician" name="Technician">
                </div>
                <div class="col-md-4">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" id="DateTesting" name="DateTesting">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"></textarea>
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
                    <td><input type="text" style="border: none;" class="form-control" id="TareName" name="TareName"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WetSoil" name="WetSoil"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Dry Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="DrySoil" name="DrySoil"></td>
                  </tr>
                  <tr>
                    <th scope="row">Water, Ww (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Water" name="Water" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Tare" name="Tare"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Soil, Ws (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="DrySoilWs" name="DrySoilWs" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Moisture" name="Moisture" readonly tabindex="-1"></td>
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
                <button type="submit" class="btn btn-success" name="moisture-oven">Save Essay</button>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/Moisture-Content.js"></script>
<?php include_once('../components/footer.php');  ?>