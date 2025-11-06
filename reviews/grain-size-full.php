<?php
$page_title = 'Grain Size Full';
require_once('../config/load.php');
$Search = find_by_id('grain_size_full', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['UpdateGSFull'])) {
    include('../database/grain-size/gs-full/update.php');
  } elseif (isset($_POST['RepeatGSFull'])) {
    include('../database/grain-size/gs-full/repeat.php');
  } elseif (isset($_POST['ReviewedGSFull'])) {
    include('../database/grain-size/gs-full/reviewed.php');
  } elseif (isset($_POST['DeleteGSFull'])) {
    include('../database/grain-size/gs-full/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Grain Size Full</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../pages/home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Grain Size Full</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="../reviews/grain-size-full.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <!-- Trial Information -->
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <!-- Multi Columns Form -->
              <div class="row g-3">
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'FM13-11 (ASTM-D6913 and D5519)') echo 'selected'; ?>>FM13-11 (ASTM-D6913 and D5519)</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Split_Method'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    <option <?php if ($Search['Split_Method'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="materialSelect" class="form-label">Material</label>
                  <select id="materialSelect" class="form-select" name="materialSelect">
                    <option value="">-- Selecciona un material --</option>
                    <option value="Common" <?php if ($Search['specs_type'] == 'Common') echo 'selected'; ?>>Common</option>
                    <option value="TRF" <?php if ($Search['specs_type'] == 'TRF') echo 'selected'; ?>>TRF</option>
                    <option value="UFF" <?php if ($Search['specs_type'] == 'UFF') echo 'selected'; ?>>UFF</option>
                    <option value="FRF" <?php if ($Search['specs_type'] == 'FRF') echo 'selected'; ?>>FRF</option>
                    <option value="IRF" <?php if ($Search['specs_type'] == 'IRF') echo 'selected'; ?>>IRF</option>
                    <option value="RF" <?php if ($Search['specs_type'] == 'RF') echo 'selected'; ?>>RF</option>
                    <option value="BF" <?php if ($Search['specs_type'] == 'BF') echo 'selected'; ?>>BF</option>
                    <option value="LQ2" <?php if ($Search['specs_type'] == 'LQ2') echo 'selected'; ?>>LQ2</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-4">
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
        <!-- End Trial Information -->

        <!-- Formato Extendido GS -->
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
                  $columnasBase = [
                    "Screen40",
                    "Screen30",
                    "Screen20",
                    "Screen13",
                    "Screen12",
                    "Screen10",
                    "Screen8",
                    "Screen6",
                    "Screen4",
                    "Screen3",
                    "Screen2",
                    "Screen1p5",
                    "Screen1",
                    "Screen3p4",
                    "Screen1p2",
                    "Screen3p8",
                    "ScreenNo4",
                    "ScreenNo20",
                    "ScreenNo200",
                    "ScreenPan"
                  ];

                  // Extraemos arrays de cada ScreenXX
                  foreach ($columnasBase as $col) {
                    $screenData[$col] = isset($Search[$col]) ? array_map('trim', explode(',', $Search[$col])) : [];
                  }

                  // Extraemos los totales
                  $screenTotals = isset($Search["ScreenTotal"]) ? array_map('trim', explode(',', $Search["ScreenTotal"])) : [];

                  $datos = [
                    ["40\"", "screen40_1", "screen40_2", "screen40_3", "screen40_4", "screen40_5", "screen40_6", "screen40_7", "screen40_8", "screen40_9", "screen40_10", "sTotal_1"],
                    ["30\"", "screen30_1", "screen30_2", "screen30_3", "screen30_4", "screen30_5", "screen30_6", "screen30_7", "screen30_8", "screen30_9", "screen30_10", "sTotal_2"],
                    ["20\"", "screen20_1", "screen20_2", "screen20_3", "screen20_4", "screen20_5", "screen20_6", "screen20_7", "screen20_8", "screen20_9", "screen20_10", "sTotal_3"],
                    ["13\"", "screen13_1", "screen13_2", "screen13_3", "screen13_4", "screen13_5", "screen13_6", "screen13_7", "screen13_8", "screen13_9", "screen13_10", "sTotal_4"],
                    ["12\"", "screen12_1", "screen12_2", "screen12_3", "screen12_4", "screen12_5", "screen12_6", "screen12_7", "screen12_8", "screen12_9", "screen12_10", "sTotal_5"],
                    ["10\"", "screen10_1", "screen10_2", "screen10_3", "screen10_4", "screen10_5", "screen10_6", "screen10_7", "screen10_8", "screen10_9", "screen10_10", "sTotal_6"],
                    ["8\"", "screen8_1", "screen8_2", "screen8_3", "screen8_4", "screen8_5", "screen8_6", "screen8_7", "screen8_8", "screen8_9", "screen8_10", "sTotal_7"],
                    ["6\"", "screen6_1", "screen6_2", "screen6_3", "screen6_4", "screen6_5", "screen6_6", "screen6_7", "screen6_8", "screen6_9", "screen6_10", "sTotal_8"],
                    ["4\"", "screen4_1", "screen4_2", "screen4_3", "screen4_4", "screen4_5", "screen4_6", "screen4_7", "screen4_8", "screen4_9", "screen4_10", "sTotal_9"],
                    ["3\"", "screen3_1", "screen3_2", "screen3_3", "screen3_4", "screen3_5", "screen3_6", "screen3_7", "screen3_8", "screen3_9", "screen3_10", "sTotal_10"],
                    ["2\"", "screen2_1", "screen2_2", "screen2_3", "screen2_4", "screen2_5", "screen2_6", "screen2_7", "screen2_8", "screen2_9", "screen2_10", "sTotal_11"],
                    ["1.5\"", "screen1p5_1", "screen1p5_2", "screen1p5_3", "screen1p5_4", "screen1p5_5", "screen1p5_6", "screen1p5_7", "screen1p5_8", "screen1p5_9", "screen1p5_10", "sTotal_12"],
                    ["1\"", "screen1_1", "screen1_2", "screen1_3", "screen1_4", "screen1_5", "screen1_6", "screen1_7", "screen1_8", "screen1_9", "screen1_10", "sTotal_13"],
                    ["3/4\"", "screen3p4_1", "screen3p4_2", "screen3p4_3", "screen3p4_4", "screen3p4_5", "screen3p4_6", "screen3p4_7", "screen3p4_8", "screen3p4_9", "screen3p4_10", "sTotal_14"],
                    ["1/2\"", "screen1p2_1", "screen1p2_2", "screen1p2_3", "screen1p2_4", "screen1p2_5", "screen1p2_6", "screen1p2_7", "screen1p2_8", "screen1p2_9", "screen1p2_10", "sTotal_15"],
                    ["3/8\"", "screen3p8_1", "screen3p8_2", "screen3p8_3", "screen3p8_4", "screen3p8_5", "screen3p8_6", "screen3p8_7", "screen3p8_8", "screen3p8_9", "screen3p8_10", "sTotal_16"],
                    ["No4", "screenNo4_1", "screenNo4_2", "screenNo4_3", "screenNo4_4", "screenNo4_5", "screenNo4_6", "screenNo4_7", "screenNo4_8", "screenNo4_9", "screenNo4_10", "sTotal_17"],
                    ["No20", "screenNo20_1", "screenNo20_2", "screenNo20_3", "screenNo20_4", "screenNo20_5", "screenNo20_6", "screenNo20_7", "screenNo20_8", "screenNo20_9", "screenNo20_10", "sTotal_18"],
                    ["No200", "screenNo200_1", "screenNo200_2", "screenNo200_3", "screenNo200_4", "screenNo200_5", "screenNo200_6", "screenNo200_7", "screenNo200_8", "screenNo200_9", "screenNo200_10", "sTotal_19"],
                    ["Pan", "screenPan_1", "screenPan_2", "screenPan_3", "screenPan_4", "screenPan_5", "screenPan_6", "screenPan_7", "screenPan_8", "screenPan_9", "screenPan_10", "sTotal_20"],
                  ];

                  // Aquí debes decidir qué fila imprimir, por ejemplo:
                  foreach ($datos as $fila) {
                    echo "<tr>";
                    foreach ($fila as $i => $campo) {
                      if ($i == 0) {
                        // Cabecera fila (el nombre del tamiz)
                        echo "<th scope='row'>" . htmlspecialchars($campo) . "</th>";
                      } else {
                        // Si es un campo total: sTotal_1, sTotal_2, etc.
                        if (strpos($campo, 'sTotal_') === 0) {
                          $indexTotal = intval(str_replace('sTotal_', '', $campo)) - 1;
                          $valor = isset($screenTotals[$indexTotal]) ? $screenTotals[$indexTotal] : '';
                        } else {
                          // Extraemos pantalla e índice
                          $partes = explode('_', $campo); // ej: screen40_3
                          $pantallaLower = strtolower($partes[0]); // 'screen40'

                          // Buscamos en $columnasBase el que coincida ignorando mayúsculas/minúsculas
                          $pantallaKey = null;
                          foreach ($columnasBase as $c) {
                            if (strtolower($c) === $pantallaLower) {
                              $pantallaKey = $c;
                              break;
                            }
                          }

                          $indice = isset($partes[1]) ? intval($partes[1]) - 1 : 0;

                          $valor = '';
                          if ($pantallaKey !== null && isset($screenData[$pantallaKey][$indice])) {
                            $valor = $screenData[$pantallaKey][$indice];
                          }
                        }
                        echo "<td><input type='text' style='border:none;' class='form-control' name='" . htmlspecialchars($campo) . "' id='" . htmlspecialchars($campo) . "' value='" . htmlspecialchars($valor) . "'></td>";
                      }
                    }
                    echo "</tr>";
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

        </div>
        <!-- End Formato Extendido GS -->

        <!-- Total Material Pasante <3" Humedo -->
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
                  $WtPassHumedoLess3 = explode(',', $Search["WtPassHumedoLess3"]);
                  $idCounter = 1;

                  for ($i = 0; $i < 11; $i++) {
                    echo '<tr>';
                    for ($j = 0; $j < 5; $j++) {
                      $index = ($i * 5) + $j; // Índice plano para recorrer todos los valores
                      $WtPassHumedo = isset($WtPassHumedoLess3[$index]) ? htmlspecialchars(trim($WtPassHumedoLess3[$index])) : '';
                      $id = "WtPhumedo_" . $idCounter;
                      echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '" value="' . $WtPassHumedo . '"></td>';
                      $idCounter++;
                    }

                    if ($i === 0) {
                      $tdmp = isset($Search['TotalPassHumedoLess3']) ? htmlspecialchars($Search['TotalPassHumedoLess3']) : '';
                      echo '<td rowspan="11"><input type="text" style="border: none;" class="form-control" name="TDMPHumedo" id="TDMPHumedo" value="' . $tdmp . '"></td>';
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
        <!-- End Total Material Pasante <3" Humedo -->

        <!-- Total Muestra Representativa <3" Seco Sucio -->
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
                  $WtPassSecoSucioLess3 = explode(',', $Search["WtPassSecoSucioLess3"]);
                  $idCounter = 1;
                  $index = 0; // Índice para recorrer el array

                  for ($i = 0; $i < 4; $i++) {
                    echo '<tr>';
                    for ($j = 0; $j < 2; $j++) {
                      $WtReSecoSucio = isset($WtPassSecoSucioLess3[$index]) ? htmlspecialchars(trim($WtPassSecoSucioLess3[$index])) : '';
                      $id = "WtReSecoSucio_" . $idCounter;
                      echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '" value="' . $WtReSecoSucio . '"></td>';
                      $idCounter++;
                      $index++; // Avanzar al siguiente dato del array
                    }

                    // Columna combinada con rowspan=4, solo en la primera fila
                    if ($i === 0) {
                      $TDMRSecoSucio = isset($Search['TotalPassSecoSucioLess3']) ? htmlspecialchars($Search['TotalPassSecoSucioLess3']) : '';
                      echo '<td rowspan="4"><input type="text" style="border: none;" class="form-control" name="TDMRSecoSucio" id="TDMRSecoSucio" value="' . $TDMRSecoSucio . '"></td>';
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
                  $columnasBase = [
                    "More3p",
                    "Lees3P",
                    "TotalPesoSecoSucio",
                    "TotalPesoLavado",
                    "PerdidaPorLavado"
                  ];

                  // Extraer los valores del array $Search
                  $TMRSSData = [];
                  foreach ($columnasBase as $col) {
                    $TMRSSData[$col] = isset($Search[$col]) ? trim($Search[$col]) : '';
                  }

                  // Estructura: [Etiqueta, nombre del input (HTML), clave del dato (PHP)]
                  $datos = [
                    [">3\"", "More3Ex", "More3p"],
                    ["<3\"", "Less3Ex", "Lees3P"],
                    ["Total Peso Seco Sucio", "TotalPesoSecoSucio", "TotalPesoSecoSucio"],
                    ["Total Peso Lavado", "TotalPesoLavado", "TotalPesoLavado"],
                    ["Pérdida por Lavado", "PerdidaPorLavado", "PerdidaPorLavado"],
                  ];

                  // Mostrar tabla
                  foreach ($datos as $fila) {
                    $label = $fila[0];
                    $inputName = $fila[1];
                    $dataKey = $fila[2];

                    $valor = isset($TMRSSData[$dataKey]) ? htmlspecialchars($TMRSSData[$dataKey]) : '';

                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($label) . '</th>';
                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $inputName . '" id="' . $inputName . '" value="' . $valor . '"></td>';
                    echo '</tr>';
                  }
                  ?>




                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>


        </div>
        <!-- End Total Muestra Representativa <3" Seco Sucio -->

        <!-- Menos % Humedad & GS -->
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
                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $id . '" id="' . $id . '" value="' . $Search[$id] . '"></td>';

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
                  $columnasBase = [
                    "WtRet",
                    "Ret",
                    "CumRet",
                    "Pass"
                  ];

                  foreach ($columnasBase as $col) {
                    $screenData[$col] = isset($Search[$col]) ? array_map('trim', explode(',', $Search[$col])) : [];
                  }

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
                        // Extraer el prefijo para saber de qué columna viene
                        preg_match('/^(WtRet|Ret|CumRet|Pass)(\d+)$/', $valor, $matches);
                        $col = $matches[1];
                        $i = intval($matches[2]) - 1;

                        // Buscar valor si existe
                        $val = isset($screenData[$col][$i]) ? htmlspecialchars($screenData[$col][$i]) : '';

                        // Campos solo lectura
                        $readonly = ($index >= 3 && $index <= 8) ? 'readonly tabindex="-1"' : '';

                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" value="' . $val . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                  <tr>
                    <th scope="row">Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PanWtRet" id="PanWtRet" value="<?php echo ($Search['PanWtRen']); ?>" readonly tabindex="-1"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row">Total Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" value="<?php echo ($Search['TotalWtRet']); ?>" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" value="<?php echo ($Search['TotalRet']); ?>" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" value="<?php echo ($Search['TotalCumRet']); ?>" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" value="<?php echo ($Search['TotalPass']); ?>" readonly tabindex="-1"></td>
                  </tr>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>

          </div>


        </div>
        <!-- End Menos % Humedad & GS -->

        <!-- Humedad & Clasificacion & Grafica -->
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">MOISTURE CONTENT</h5>
              <!-- Tabla the Moisture Content -->
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
                  $columnasBase = [
                    "Container",
                    "WtSoilTare",
                    "WtSoilDry",
                    "WtWater",
                    "TareMC",
                    "WtDrySoil",
                    "MC"
                  ];

                  foreach ($columnasBase as $col) {
                    $moistureData[$col] = isset($Search[$col]) ? array_map('trim', explode(',', $Search[$col])) : [];
                  }

                  $datos = array(
                    array("(B) Container", "Container1", "Container2", "Container3", "Container4"),
                    array("(C) Wt Wet Soil + Tare, g", "WetSoil1", "WetSoil2", "WetSoil3", "WetSoil4"),
                    array("(D) Wt Dry Soil + Tare, g", "WetDry1", "WetDry2", "WetDry3", "WetDry4"),
                    array("(E) Wt Water, g = (C-D)", "WetWater1", "WetWater2", "WetWater3", "WetWater4"),
                    array("(F) Tare, g", "TareMC1", "TareMC2", "TareMC3", "TareMC4"),
                    array("(G) Wt Dry Soil, g = (D-F)", "WtDrySoil1", "WtDrySoil2", "WtDrySoil3", "WtDrySoil4"),
                    array("(H) Moisture Content, % = (E/G)", "MoisturePercet1", "MoisturePercet2", "MoisturePercet3", "MoisturePercet4")
                  );

                  foreach ($datos as $filaIndex => $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        // Determinar a qué columna base corresponde esta fila
                        $columnaBase = $columnasBase[$filaIndex];
                        // Obtener el valor correspondiente si existe
                        $valorInput = isset($moistureData[$columnaBase][$index - 1]) ? htmlspecialchars($moistureData[$columnaBase][$index - 1]) : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" value="' . $valorInput . '"></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Tabla the Moisture Content -->

              <!-- Grain Size Graph For the GrainSizeRockGraph -->
              <h5 class="card-title"></h5>
              <div id="GrainSizeRockGraph" style="min-height: 400px;" class="echart"></div>
              <!-- end Grain Size Graph For the GrainSizeRockGraph -->

              <h5 class="card-title">Classification as per ASTM D2487:</h5>
              <div>
                <input type="hidden" name="ClassificationUSCS1" id="ClassificationUSCS1">
                <input type="text" class="form-control-plaintext" name="classificationCombined" id="classificationCombined" value="<?php echo ($Search['Classification1']); ?>" readonly tabindex="-1">
                <input type="text" class="form-control-plaintext" name="ClassificationUSCS2" id="ClassificationUSCS2" value="<?php echo ($Search['Classification2']); ?>" readonly tabindex="-1">
              </div>
            </div>

          </div>


        </div>
        <!-- End Humedad & Clasificacion & Grafica -->

        <!-- Sumary Grain Size Distribution Table -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Summary Grain Size Distribution Parameter</h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Coarser than Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="CoarserGravel" id="CoarserGravel" value="<?php echo ($Search['Coarser_than_Gravel']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Gravel" id="Gravel" value="<?php echo ($Search['Gravel']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Sand%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Sand" id="Sand" value="<?php echo ($Search['Sand']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Fines%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Fines" id="Fines" value="<?php echo ($Search['Fines']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D10 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D10" id="D10" value="<?php echo ($Search['D10']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D15 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D15" id="D15" value="<?php echo ($Search['D15']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D30 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D30" id="D30" value="<?php echo ($Search['D30']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D60 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D60" id="D60" value="<?php echo ($Search['D60']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">D85 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D85" id="D85" value="<?php echo ($Search['D85']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cc :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cc" id="Cc" value="<?php echo ($Search['Cc']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cu :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cu" id="Cu" value="<?php echo ($Search['Cu']); ?>" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
        <!-- end Sumary Grain Size Distribution Table -->

        <!-- Data hidden factor MCavg Correction -->
        <input type="hidden" name="MoistureContentAvg" id="MoistureContentAvg" value="<?php echo ($Search['MoistureContentAvg']); ?>">
        <input type="hidden" name="TotalDryWtSampleLess3g" id="TotalDryWtSampleLess3g" value="<?php echo ($Search['TotalDryWtSampleLess3g']); ?>">
        <input type="hidden" name="ConvertionFactor" id="ConvertionFactor" value="<?php echo ($Search['ConvertionFactor']); ?>">

        <?php
        $specsArray = isset($Search['Specs']) ? explode(',', $Search['Specs']) : [];

        for ($i = 0; $i < 8; $i++) {
          $id = "Specs" . ($i + 1);
          $value = isset($specsArray[$i]) ? htmlspecialchars(trim($specsArray[$i])) : '';
          echo '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $value . '">';
        }
        ?>
        <!-- End Data hidden factor MCavg Correction -->

        <!-- Buttons for realized actions -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="UpdateGSFull">Update</button>
                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" data-exportar="GS-<?php echo $Search['specs_type']; ?>-Build">Contruccion</a></li>
                  </ul>
                </div>
                <button type="submit" class="btn btn-danger" name="DeleteGSFull"><i class="bi bi-trash"></i></button>
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="RepeatGSFull">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="ReviewedGSFull">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>
        <!-- End Buttons for realized actions -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/grain-size/specs.js?v1"></script>
<script type="module" src="../js/grain-size/gs-full.js?v1"></script>
<?php include_once('../components/footer.php');  ?>