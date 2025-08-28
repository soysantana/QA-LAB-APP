<?php
$page_title = 'Standard Proctor';
$class_form = ' ';
$form_show = 'show';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['SaveSP'])) {
    include('../database/standard-proctor/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Standard Proctor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Standard Proctor</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="standard-proctor.php" method="post">

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
                    <option value="ASTM-D698">ASTM-D698</option>
                  </select>
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
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod">
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician">
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting">
                </div>
                <div class="col-md-6">
                  <label for="NatMc" class="form-label">Natural Mc %</label>
                  <input type="text" class="form-control" name="NatMc" id="NatMc">
                </div>
                <div class="col-md-6">
                  <label for="SpecGravity" class="form-label">Specific Gravity</label>
                  <input type="text" class="form-control" name="SpecGravity" id="SpecGravity">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Trial Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                    <th scope="col">4</th>
                    <th scope="col">5</th>
                    <th scope="col">6</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Wt Wet Soil + Mold (gr)", "WetSoilMod1", "WetSoilMod2", "WetSoilMod3", "WetSoilMod4", "WetSoilMod5", "WetSoilMod6"),
                    array("Wt Mold (gr)", "WtMold1", "WtMold2", "WtMold3", "WtMold4", "WtMold5", "WtMold6"),
                    array("Wt Wet Soil (gr)", "WtSoil1", "WtSoil2", "WtSoil3", "WtSoil4", "WtSoil5", "WtSoil6"),
                    array("Vol Mold  (cm3)", "VolMold1", "VolMold2", "VolMold3", "VolMold4", "VolMold5", "VolMold6"),
                    array("Wet Density (kg/m3 )", "WetDensity1", "WetDensity2", "WetDensity3", "WetDensity4", "WetDensity5", "WetDensity6"),
                    array("Dry Density, (kg/m3 )", "DryDensity1", "DryDensity2", "DryDensity3", "DryDensity4", "DryDensity5", "DryDensity6"),
                    array("Dry Density Corrected", "DensyCorrected1", "DensyCorrected2", "DensyCorrected3", "DensyCorrected4", "DensyCorrected5", "DensyCorrected6"),
                    // Puedes agregar más filas según sea necesario
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = (
                          ($index >= 1 && $index <= 6 && strpos($valor, 'WtSoil') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'WetDensity') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'DryDensity') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'DensyCorrected') !== false)
                        ) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

              <h5 class="card-title"></h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Trial Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                    <th scope="col">4</th>
                    <th scope="col">5</th>
                    <th scope="col">6</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Container", "Container1", "Container2", "Container3", "Container4", "Container5", "Container6"),
                    array("Wt Wet Soil + Tare (gr)", "WetSoilTare1", "WetSoilTare2", "WetSoilTare3", "WetSoilTare4", "WetSoilTare5", "WetSoilTare6"),
                    array("Wt Dry Soil + Tare (gr)", "WetDryTare1", "WetDryTare2", "WetDryTare3", "WetDryTare4", "WetDryTare5", "WetDryTare6"),
                    array("Wt Water (gr)", "WtWater1", "WtWater2", "WtWater3", "WtWater4", "WtWater5", "WtWater6"),
                    array("Tare (gr)", "Tare1", "Tare2", "Tare3", "Tare4", "Tare5", "Tare6"),
                    array("Wt Dry Soil (gr)", "DrySoil1", "DrySoil2", "DrySoil3", "DrySoil4", "DrySoil5", "DrySoil6"),
                    array("Moisture Content (%)", "MoisturePorce1", "MoisturePorce2", "MoisturePorce3", "MoisturePorce4", "MoisturePorce5", "MoisturePorce6"),
                    array("Moisture Content Corrected", "MCcorrected1", "MCcorrected2", "MCcorrected3", "MCcorrected4", "MCcorrected5", "MCcorrected6"),
                    // Puedes agregar más filas según sea necesario
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = (
                          ($index >= 1 && $index <= 6 && strpos($valor, 'WtWater') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'DrySoil') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'MoisturePorce') !== false) ||
                          ($index >= 1 && $index <= 6 && strpos($valor, 'MCcorrected') !== false)
                        ) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col">Max Dry Density</th>
                    <td><input type="text" style="border: none;" class="form-control" name="MaxDryDensity" id="MaxDryDensity" readonly tabindex="-1"></td>
                    <!-- ohter -->
                    <th scope="col">Optimum Moisture Content</th>
                    <td><input type="text" style="border: none;" class="form-control" name="OptimumMoisture" id="OptimumMoisture" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Correction of Unit Weight and Water Content for Soils Containing Oversize Particles ASTM D4718</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col" colspan="3">Corrected Dry unit weight of the total material (combined finer and oversize fractions) (Kg/mᵌ)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="CorrectedDryUnitWeigt" id="CorrectedDryUnitWeigt" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="col" colspan="3">Corrected water contetn of combined finer and oversize fractions expressed in percent ωT (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="CorrectedWaterContentFiner" id="CorrectedWaterContentFiner" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wc (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WcPorce" id="WcPorce"></td>
                    <th scope="row">ɣDF</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Ydf" id="Ydf" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Pc (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PcPorce" id="PcPorce"></td>
                    <th scope="row">PF (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PfPorce" id="PfPorce"></td>
                  </tr>
                  <tr>
                    <th scope="row">Gm</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Gm" id="Gm"></td>
                    <th scope="row">ɣDT</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Ydt" id="Ydt" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">ɣω (KN/mᵌ)</th>
                    <td colspan="3"><input type="text" style="border: none;" class="form-control" name="YwKnm" id="YwKnm"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

        </div>

        <div class="col-lg-9">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <!-- Standard Proctor -->
              <div id="StandardProctor" style="min-height: 400px;" class="echart"></div>
              <!-- End Standard Proctor -->

            </div>
          </div>

        </div>

        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="SaveSP">Save Essay</button>
                <button type="button" class="btn btn-primary" onclick="search()">Search Moisture</button>
                <button type="button" class="btn btn-primary" onclick="search()">Seach Gravity</button>
                <div id="mensaje-container"></div>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script type="module" src="../js/Standard-Proctor.js"></script>
<script src="../libs/graph/Standard-Proctor.js"></script>
<?php include_once('../components/footer.php');  ?>