<?php
$page_title = 'Specific Gravity Coarse Filter';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['specific-gravity-coarse'])) {
    include('../database/specific-gravity/sg-cf/save.php');
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
        <li class="breadcrumb-item active">Specific Gravity Coarse Filter</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="specific-gravity-coarse-aggregates.php" method="post">

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
                    <option value="ASTM-C127">ASTM-C127</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod">
                </div>
                <div class="col-md-6">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried">Oven Dried</option>
                    <option value="Air Dried">Air Dried</option>
                    <option value="Microwave Dried">Microwave Dried</option>
                    <option value="Wet">Wet</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual">Manual</option>
                    <option value="Mechanical">Mechanical</option>
                  </select>
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

                    echo "<td><input type='text' style='border: none;' class='form-control' name='OvenDry{$i}' id='OvenDry{$i}'></td>";

                    echo "<th scope='row'>$datos[$i]</th>";

                    echo "<td><input type='text' style='border: none;' class='form-control' name='SurfaceDry{$i}' id='SurfaceDry{$i}'></td>";

                    echo "<th scope='row'>$datos[$i]</th>";

                    echo "<td><input type='text' style='border: none;' class='form-control' name='SampImmers{$i}' id='SampImmers{$i}'></td>";

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
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravityOD" id="SpecificGravityOD" readonly tabindex="-1"></td>
                    <th scope="col">Specific Gravity (SSD) (B/(B-C))=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SpecificGravitySSD" id="SpecificGravitySSD" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="col">Apparent Specific Gravity (A/(A-C))=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ApparentSpecificGravity" id="ApparentSpecificGravity" readonly tabindex="-1"></td>
                    <th scope="col">Percent of Absortion ((B-A)/A)*100=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PercentAbsortion" id="PercentAbsortion" readonly tabindex="-1"></td>
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
                  <button type="submit" class="btn btn-success" name="specific-gravity-coarse">Save Essay</button>
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