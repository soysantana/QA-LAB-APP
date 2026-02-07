<?php
$page_title = 'Vibrating Hammer';
$class_form = ' ';
$form_show = 'show';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['Save'])) {
    include('../database/sp-vibratory/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Vibrating Hammer</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="sp-menu.php">Forms</a></li>
        <li class="breadcrumb-item active">Vibrating Hammer</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="sp-vibratory.php" method="post">

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
                <div class="col-md-3">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option value="ASTM-D7382">ASTM-D7382</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods" required>
                    <option value="" selected disabled>Choose...</option>
                    <option value="Manual">Manual</option>
                    <option value="Mechanical">Mechanical</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician">
                </div>
                <div class="col-md-3">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting">
                </div>
                <div class="col-md-2">
                  <label for="NatMc" class="form-label">Natural Mc %</label>
                  <input type="text" class="form-control" name="NatMc" id="NatMc">
                </div>
                <div class="col-md-2">
                  <label for="SpecGravity" class="form-label">Specific Gravity</label>
                  <input type="text" class="form-control" name="SpecGravity" id="SpecGravity">
                </div>
                <div class="col-md-2">
                  <label for="MaxParticleSize" class="form-label">Max Particle Size</label>
                  <input type="text" class="form-control" name="MaxParticleSize" id="MaxParticleSize">
                </div>
                <div class="col-md-2">
                  <label for="Gravel" class="form-label">Gravel %</label>
                  <input type="text" class="form-control" name="Gravel" id="Gravel">
                </div>
                <div class="col-md-2">
                  <label for="Sand" class="form-label">Sand %</label>
                  <input type="text" class="form-control" name="Sand" id="Sand">
                </div>
                <div class="col-md-2">
                  <label for="Fines" class="form-label">Fines%</label>
                  <input type="text" class="form-control" name="Fines" id="Fines">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>
        </div>

        <!-- Seco Vibrado Table -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <!-- Seco Vibrado Table -->
              <h5 class="card-title">Seco Vibrado</h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Trial Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Mold No.", "ModVibrar1", "ModVibrar2", "ModVibrar3"),
                    array("Wt Mold + and base plater (Kg)", "WtMoldVibrar1", "WtMoldVibrar2", "WtMoldVibrar3"),
                    array("Vol Mold  (m3)", "VolMoldVibrar1", "VolMoldVibrar2", "VolMoldVibrar3"),
                    array("Mass Mold, Base Plate and Soil", "MassMoldBaseVibrar1", "MassMoldBaseVibrar2", "MassMoldBaseVibrar3"),
                    array("Mass of soil (Kg)", "MassSoilVibrar1", "MassSoilVibrar2", "MassSoilVibrar3"),
                    array("Soil Dry Density (Kg/m3)", "SoilDryVibrar1", "SoilDryVibrar2", "SoilDryVibrar3"),
                    array("Soil Unit Weght Kn/m3", "SoilUnitVibrar1", "SoilUnitVibrar2", "SoilUnitVibrar3")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = (
                          ($index >= 1 && $index <= 3 && strpos($valor, 'MassSoilVibrar') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'SoilDryVibrar') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'SoilUnitVibrar') !== false)
                        ) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Seco Vibrado Table -->
            </div>
          </div>
        </div>
        <!-- Seco Vibrado Table -->

        <!-- Seco Sin Vibrar Table -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Seco Sin Vibrar</h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Trial Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Mold No.", "Mod1", "Mod2", "Mod3"),
                    array("Wt Mold + and base plater (Kg)", "WtMold1", "WtMold2", "WtMold3"),
                    array("Vol Mold  (m3)", "VolMold1", "VolMold2", "VolMold3"),
                    array("Mass Mold, Base Plate and Soil", "MassMoldBase1", "MassMoldBase2", "MassMoldBase3"),
                    array("Mass of soil (Kg)", "MassSoil1", "MassSoil2", "MassSoil3"),
                    array("Soil Dry Density (Kg/m3)", "SoilDry1", "SoilDry2", "SoilDry3"),
                    array("Soil Unit Weght Kn/m3", "SoilUnit1", "SoilUnit2", "SoilUnit3")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = (
                          ($index >= 1 && $index <= 3 && strpos($valor, 'MassSoil') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'SoilDry') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'SoilUnit') !== false)
                        ) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- End Seco Sin Vibrar Table -->

        <!-- Actions Buttons -->
        <div class="col-lg-2">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Acciones</h5>
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="Save">Guardar</button>
                <button type="button" class="btn btn-primary" name="SearchMC">Buscar datos</button>
                <div id="mensaje-container"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Saturado Table -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <!-- Saturado Table -->
              <h5 class="card-title">Saturado</h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Trial Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Mold No.", "ModSatured1", "ModSatured2", "ModSatured3"),
                    array("Wt Mold + and base plater (Kg)", "WtMoldSatured1", "WtMoldSatured2", "WtMoldSatured3"),
                    array("Vol Mold  (m3)", "VolMoldSatured1", "VolMoldSatured2", "VolMoldSatured3"),
                    array("Tare Number", "TareSatured1", "TareSatured2", "TareSatured3"),
                    array("Mass of tare  (Kg)", "MassTareSatured1", "MassTareSatured2", "MassTareSatured3"),
                    array("Mass of tare and oven dry soil (Kg)", "MassTareDrySatured1", "MassTareDrySatured2", "MassTareDrySatured3"),
                    array("Mass of soil (Kg)", "MassSoilSatured1", "MassSoilSatured2", "MassSoilSatured3"),
                    array("Dry Density Compacted, (kg/m3 )", "DryDensitySatured1", "DryDensitySatured2", "DryDensitySatured3"),
                    array("Dry Unit weight (kN/m3 )", "DryUnitSatured1", "DryUnitSatured2", "DryUnitSatured3")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = (
                          ($index >= 1 && $index <= 3 && strpos($valor, 'MassSoilSatured') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'DryDensitySatured') !== false) ||
                          ($index >= 1 && $index <= 3 && strpos($valor, 'DryUnitSatured') !== false)
                        ) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Saturado Table -->
            </div>
          </div>
        </div>
        <!-- Saturado Table -->

        <!-- Resumen General Table -->
        <div class="col-lg-7">
          <div class="card">
            <div class="card-body">
              <!-- Seco Vibrado -->
              <h5 class="card-title">Seco Vibrado</h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col">Max Dry Unit Weight (kN/m3) =</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoVibradoMaxDryUnitDensity" id="SecoVibradoMaxDryUnitDensity" readonly tabindex="-1"></td>
                    <th scope="col">Water Content Effective Compaction (%)=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoVibradoWaterContentEffective" id="SecoVibradoWaterContentEffective" readonly tabindex="-1"></td>
                    <th scope="col">Test Condition</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoVibradoTestCondition" id="SecoVibradoTestCondition" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Seco Vibrado -->
              <!-- Seco Sin Vibrar -->
              <h5 class="card-title">Seco Sin Vibrar</h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col">Max Dry Unit Weight (kN/m3) =</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoSinVibrarMaxDryUnitDensity" id="SecoSinVibrarMaxDryUnitDensity" readonly tabindex="-1"></td>
                    <th scope="col">Water Content Effective Compaction (%)=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoSinVibrarWaterContentEffective" id="SecoSinVibrarWaterContentEffective" readonly tabindex="-1"></td>
                    <th scope="col">Test Condition</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SecoSinVibrarTestCondition" id="SecoSinVibrarTestCondition" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Seco Sin Vibrar -->
              <!-- Saturado -->
              <h5 class="card-title">Saturado</h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="col">Max Dry Unit Weight (kN/m3) =</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SaturadoMaxDryUnitDensity" id="SaturadoMaxDryUnitDensity" readonly tabindex="-1"></td>
                    <th scope="col">Water Content Effective Compaction (%)=</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SaturadoWaterContentEffective" id="SaturadoWaterContentEffective" readonly tabindex="-1"></td>
                    <th scope="col">Test Condition</th>
                    <td><input type="text" style="border: none;" class="form-control" name="SaturadoTestCondition" id="SaturadoTestCondition" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Saturado -->
            </div>
          </div>
        </div>
        <!-- End Resumen GeneralTable -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script type="module" src="../js/sp-vibratory.js"></script>
<?php include_once('../components/footer.php');  ?>