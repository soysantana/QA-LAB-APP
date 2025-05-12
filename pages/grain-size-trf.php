<?php
$page_title = 'Grain Size Upstream Transition Fill';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['gs_utf'])) {
    include('../database/grain-size/gs-full/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Grain Size Upstream Transition Fill</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Grain Size Upstream Transition Fill</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="grain-size-trf.php" method="post">

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
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option value="ASTM-D6913">ASTM-D6913</option>
                  </select>
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
                <div class="col-12">
                  <textarea hidden class="form-control" name="Graph" id="Graph" style="height: 100px;"></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">FORMATO EXTENDIDO PARA GRANULOMETRIAS</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Screen</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                    <th scope="col">4</th>
                    <th scope="col">5</th>
                    <th scope="col">6</th>
                    <th scope="col">7</th>
                    <th scope="col">8</th>
                    <th scope="col">9</th>
                    <th scope="col">10</th>
                    <th scope="col">Total</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("40\"", "screen40_1", "screen40_2", "screen40_3", "screen40_4", "screen40_5", "screen40_6", "screen40_7", "screen40_8", "screen40_9", "screen40_10", "sTotal_1"),
                    array("30\"", "screen30_1", "screen30_2", "screen30_3", "screen30_4", "screen30_5", "screen30_6", "screen30_7", "screen30_8", "screen30_9", "screen30_10", "sTotal_2"),
                    array("20\"", "screen20_1", "screen20_2", "screen20_3", "screen20_4", "screen20_5", "screen20_6", "screen20_7", "screen20_8", "screen20_9", "screen20_10", "sTotal_3"),
                    array("13\"", "screen13_1", "screen13_2", "screen13_3", "screen13_4", "screen13_5", "screen13_6", "screen13_7", "screen13_8", "screen13_9", "screen13_10", "sTotal_4"),
                    array("12\"", "screen12_1", "screen12_2", "screen12_3", "screen12_4", "screen12_5", "screen12_6", "screen12_7", "screen12_8", "screen12_9", "screen12_10", "sTotal_5"),
                    array("10\"", "screen10_1", "screen10_2", "screen10_3", "screen10_4", "screen10_5", "screen10_6", "screen10_7", "screen10_8", "screen10_9", "screen10_10", "sTotal_6"),
                    array("8\"", "screen8_1", "screen8_2", "screen8_3", "screen8_4", "screen8_5", "screen8_6", "screen8_7", "screen8_8", "screen8_9", "screen8_10", "sTotal_7"),
                    array("6\"", "screen6_1", "screen6_2", "screen6_3", "screen6_4", "screen6_5", "screen6_6", "screen6_7", "screen6_8", "screen6_9", "screen6_10", "sTotal_8"),
                    array("4\"", "screen4_1", "screen4_2", "screen4_3", "screen4_4", "screen4_5", "screen4_6", "screen4_7", "screen4_8", "screen4_9", "screen4_10", "sTotal_9"),
                    array("3\"", "screen3_1", "screen3_2", "screen3_3", "screen3_4", "screen3_5", "screen3_6", "screen3_7", "screen3_8", "screen3_9", "screen3_10", "sTotal_10"),
                    array("2\"", "screen2_1", "screen2_2", "screen2_3", "screen2_4", "screen2_5", "screen2_6", "screen2_7", "screen2_8", "screen2_9", "screen2_10", "sTotal_11"),
                    array("1.5\"", "screen1p5_1", "screen1p5_2", "screen1p5_3", "screen1p5_4", "screen1p5_5", "screen1p5_6", "screen1p5_7", "screen1p5_8", "screen1p5_9", "screen1p5_10", "sTotal_12"),
                    array("1\"", "screen1_1", "screen1_2", "screen1_3", "screen1_4", "screen1_5", "screen1_6", "screen1_7", "screen1_8", "screen1_9", "screen1_10", "sTotal_13"),
                    array("3/4\"", "screen3p4_1", "screen3p4_2", "screen3p4_3", "screen3p4_4", "screen3p4_5", "screen3p4_6", "screen3p4_7", "screen3p4_8", "screen3p4_9", "screen3p4_10", "sTotal_14"),
                    array("1/2\"", "screen1p2_1", "screen1p2_2", "screen1p2_3", "screen1p2_4", "screen1p2_5", "screen1p2_6", "screen1p2_7", "screen1p2_8", "screen1p2_9", "screen1p2_10", "sTotal_15"),
                    array("3/8\"", "screen3p8_1", "screen3p8_2", "screen3p8_3", "screen3p8_4", "screen3p8_5", "screen3p8_6", "screen3p8_7", "screen3p8_8", "screen3p8_9", "screen3p8_10", "sTotal_16"),
                    array("No4", "screenNo4_1", "screenNo4_2", "screenNo4_3", "screenNo4_4", "screenNo4_5", "screenNo4_6", "screenNo4_7", "screenNo4_8", "screenNo4_9", "screenNo4_10", "sTotal_17"),
                    array("No20", "screenNo20_1", "screenNo20_2", "screenNo20_3", "screenNo20_4", "screenNo20_5", "screenNo20_6", "screenNo20_7", "screenNo20_8", "screenNo20_9", "screenNo20_10", "sTotal_18"),
                    array("No200", "screenNo200_1", "screenNo200_2", "screenNo200_3", "screenNo200_4", "screenNo200_5", "screenNo200_6", "screenNo200_7", "screenNo200_8", "screenNo200_9", "screenNo200_10", "sTotal_19"),
                    array("Pan", "screenPan_1", "screenPan_2", "screenPan_3", "screenPan_4", "screenPan_5", "screenPan_6", "screenPan_7", "screenPan_8", "screenPan_9", "screenPan_10", "sTotal_20"),
                    // Puedes agregar más filas según sea necesario
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = ($index === count($fila) - 1) ? 'readonly tabindex="-1"' : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

        </div>

        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Total de Material Pasante &lt;3" Húmedo</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                </thead>
                <tbody>

                  <?php
                  // Generar 11 filas
                  $idCounter = 1; // Contador para los IDs
                  for ($i = 1; $i <= 11; $i++) {
                    echo '<tr>';
                    // Generar las primeras 5 columnas con inputs
                    for ($j = 1; $j <= 5; $j++) {
                      $id = "WtPhumedo_" . $idCounter; // Generar un ID único para cada input
                      echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '"></td>';
                      $idCounter++; // Incrementar el contador de IDs
                    }
                    // Generar la sexta columna solo en la primera fila con rowspan=11
                    if ($i === 1) {
                      echo '<td rowspan="11"><input type="text" style="border: none;" class="form-control" name="TDMPHumedo" id="TDMPHumedo"></td>';
                    }
                    echo '</tr>';
                  }
                  ?>


                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>


        </div>

        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Total de Muestra Representativa &lt;3" Seco Sucio</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                </thead>
                <tbody>

                  <?php
                  // Generar 11 filas
                  $idCounter = 1; // Contador para los IDs
                  for ($i = 1; $i <= 4; $i++) {
                    echo '<tr>';
                    // Generar las primeras 5 columnas con inputs
                    for ($j = 1; $j <= 2; $j++) {
                      $id = "WtReSecoSucio_" . $idCounter; // Generar un ID único para cada input
                      echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '"></td>';
                      $idCounter++; // Incrementar el contador de IDs
                    }
                    // Generar la sexta columna solo en la primera fila con rowspan=11
                    if ($i === 1) {
                      echo '<td rowspan="4"><input type="text" style="border: none;" class="form-control" name="TDMRSecoSucio" id="TDMRSecoSucio"></td>';
                    }
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>

            <div class="card-body">
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("&gt;3&quot;", "More3Ex"),
                    array("&lt;3&quot;", "Less3Ex"),
                    array("Total Peso Seco Sucio", "TotalPesoSecoSucio"),
                    array("Total Peso Lavado", "TotalPesoLavado"),
                    array("Perdida por Lavado", "PerdidaPorLavado"),
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    // Primera columna: texto
                    echo '<th scope="row">' . $fila[0] . '</th>';

                    // Segunda columna: input con ID y name del segundo elemento
                    $id = $fila[1]; // ID único tomado del array
                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '"></td>';

                    echo '</tr>';
                  }
                  ?>


                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>


        </div>


        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Eso menos % Humedad</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Peso Seco Sucio", "PesoSecoSucio"),
                    array("Peso Lavado", "PesoLavado"),
                    array("Pan Lavado", "PanLavado"),
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    // Primera columna: texto
                    echo '<th scope="row">' . $fila[0] . '</th>';

                    // Segunda columna: input con ID y name del segundo elemento
                    $id = $fila[1]; // ID único tomado del array
                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '"></td>';

                    echo '</tr>';
                  }
                  ?>


                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>

            <div class="card-body">
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Sieve</th>
                    <th scope="col">Wt Ret</th>
                    <th scope="col">% Ret</th>
                    <th scope="col">Cum % Ret</th>
                    <th scope="col">% Pass</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("40\"", "WtRet1", "Ret1", "CumRet1", "Pass1"),
                    array("30\"", "WtRet2", "Ret2", "CumRet2", "Pass2"),
                    array("20\"", "WtRet3", "Ret3", "CumRet3", "Pass3"),
                    array("13\"", "WtRet4", "Ret4", "CumRet4", "Pass4"),
                    array("12\"", "WtRet5", "Ret5", "CumRet5", "Pass5"),
                    array("10\"", "WtRet6", "Ret6", "CumRet6", "Pass6"),
                    array("8\"", "WtRet7", "Ret7", "CumRet7", "Pass7"),
                    array("6\"", "WtRet8", "Ret8", "CumRet8", "Pass8"),
                    array("4\"", "WtRet9", "Ret9", "CumRet9", "Pass9"),
                    array("3\"", "WtRet10", "Ret10", "CumRet10", "Pass10"),
                    array("2\"", "WtRet11", "Ret11", "CumRet11", "Pass11"),
                    array("1.5\"", "WtRet12", "Ret12", "CumRet12", "Pass12"),
                    array("1\"", "WtRet13", "Ret13", "CumRet13", "Pass13"),
                    array("3/4\"", "WtRet14", "Ret14", "CumRet14", "Pass14"),
                    array("1/2\"", "WtRet15", "Ret15", "CumRet15", "Pass15"),
                    array("3/8\"", "WtRet16", "Ret16", "CumRet16", "Pass16"),
                    array("No. 4", "WtRet17", "Ret17", "CumRet17", "Pass17"),
                    array("No. 20", "WtRet18", "Ret18", "CumRet18", "Pass18"),
                    array("No. 200", "WtRet19", "Ret19", "CumRet19", "Pass19"),
                    // Puedes agregar más filas según sea necesario
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = ($index >= 3 && $index <= 8) ? 'readonly tabindex="-1"' : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                  <tr>
                    <th scope="row">Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PanWtRet" id="PanWtRet" readonly tabindex="-1"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row">Total Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" readonly tabindex="-1"></td>
                  </tr>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>

          </div>


        </div>

        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">MOISTURE CONTENT</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Sample Number</th>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                    <th scope="col">4</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("(B) Container", "Container1", "Container2", "Container3", "Container4"),
                    array("(C) Wt Wet Soil + Tare, g", "WetSoil1", "WetSoil2", "WetSoil3", "WetSoil4"),
                    array("(D) Wt Dry Soil + Tare, g", "WetDry1", "WetDry2", "WetDry3", "WetDry4"),
                    array("(E) Wt Water, g = (C-D)", "WetWater1", "WetWater2", "WetWater3", "WetWater4"),
                    array("(F) Tare, g", "TareMC1", "TareMC2", "TareMC3", "TareMC4"),
                    array("(G) Wt Dry Soil, g = (D-F)", "WtDrySoil1", "WtDrySoil2", "WtDrySoil3", "WtDrySoil4"),
                    array("(H) Moisture Content, % = (E/G)", "MoisturePercet1", "MoisturePercet2", "MoisturePercet3", "MoisturePercet4")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '"></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Bordered Table -->

              <!-- Grain Size Graph For the GrainSizeRockGraph -->
              <h5 class="card-title"></h5>
              <div id="GrainSizeRockGraph" style="min-height: 400px;" class="echart"></div>
              <!-- end Grain Size Graph For the GrainSizeRockGraph -->

              <h5 class="card-title">Classification as per ASTM D2487:</h5>
              <div><input type="text" class="form-control" name="classification" id="classification" readonly tabindex="-1"></div>

            </div>

          </div>


        </div>

        <!-- Sumary Grain Size Distribution Table -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Summary Grain Size Distribution Parameter</h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Coarser than Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="CoarserGravel" id="CoarserGravel" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Gravel" id="Gravel" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Sand%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Sand" id="Sand" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Fines%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Fines" id="Fines" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D10 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D10" id="D10" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D15 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D15" id="D15" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D30 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D30" id="D30" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D60 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D60" id="D60" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D85 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D85" id="D85" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cc :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cc" id="Cc" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cu :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cu" id="Cu" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
        <!-- end Sumary Grain Size Distribution Table -->




      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/grain-size/gs-trf.js"></script>
<script src="../libs/graph/Grain-Size-Full.js"></script>
<?php include_once('../components/footer.php');  ?>