<?php
$page_title = 'Hydraulic Conductivity - ASTM D2434';
require_once('../config/load.php');
?>



<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['d2434-save'])) {
    include('../database/d2434.php');
  }
}
function num($v){
  $v = str_replace(',', '.', trim((string)$v));
  return is_numeric($v) ? (float)$v : null;
}
function fmt($v, $d=2){
  if($v === null) return '';
  return number_format((float)$v, $d, '.', '');
}
function fmtQ($v){
  if($v === null) return '';
  $s = sprintf('%.6f', $v);
  return rtrim(rtrim($s,'0'),'.');
}

$prefill = [
  'D' => '0.113',
  'L' => '0.150',
  'A' => '0.010100',
  'V' => '0.0015',
  'SpecWeight' => '',
  'rows' => []
];

// Solo precargar cuando vienes del Entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send-to-d2434'])) {

  $e = $_POST['entry'] ?? [];

  // Básicos
  $D_cm  = num($e['D_cm'] ?? null);
  $L_cm  = num($e['L_cm'] ?? null);
  $t_min = num($e['t_min'] ?? null);

  $w_equipo = num($e['w_equipo'] ?? null);
  $w_total  = num($e['w_total'] ?? null);

  // Alturas por bloque (cm)
  $h1_1 = num($e['h1_1_cm'] ?? null);
  $h2_1 = num($e['h2_1_cm'] ?? null);

  $h1_2 = num($e['h1_2_cm'] ?? null);
  $h2_2 = num($e['h2_2_cm'] ?? null);

  $h1_3 = num($e['h1_3_cm'] ?? null);
  $h2_3 = num($e['h2_3_cm'] ?? null);

  // Volúmenes (mL) y temperaturas por corrida
  $v1 = $e['v1'] ?? [];  $t1 = $e['t1'] ?? [];
  $v2 = $e['v2'] ?? [];  $t2 = $e['t2'] ?? [];
  $v3 = $e['v3'] ?? [];  $t3 = $e['t3'] ?? [];

  // Convertir
  $D = ($D_cm !== null) ? $D_cm / 100 : null;  // cm->m
  $L = ($L_cm !== null) ? $L_cm / 100 : null;  // cm->m
  $tsec = ($t_min !== null) ? $t_min * 60 : 60;// min->s

  // A y V
  if ($D !== null && $L !== null && $D > 0 && $L > 0) {
    $A = pi() * ($D*$D) / 4;
    $V = $A * $L;
    $prefill['D'] = fmt($D,3);
    $prefill['L'] = fmt($L,3);
    $prefill['A'] = fmt($A,6);
    $prefill['V'] = fmt($V,6);
  }

  // Peso espécimen
  if($w_total !== null && $w_equipo !== null){
    $prefill['SpecWeight'] = fmt($w_total - $w_equipo, 3);
  }

  // Helper para 5 corridas (con temperatura por corrida)
  $fill5 = function($startTest, $h1_cm, $h2_cm, $vols, $temps) use (&$prefill, $tsec) {
    for($k=0;$k<5;$k++){
      $testNo = $startTest + $k;

      $vol_ml = isset($vols[$k]) ? num($vols[$k]) : null;
      $Q_m3   = ($vol_ml !== null) ? $vol_ml / 1e6 : null; // mL->m3

      $tempC  = isset($temps[$k]) ? num($temps[$k]) : null;

      $prefill['rows'][$testNo] = [
        'h1'   => ($h1_cm !== null) ? fmt($h1_cm/100,2) : '',
        'h2'   => ($h2_cm !== null) ? fmt($h2_cm/100,2) : '',
        'Q'    => fmtQ($Q_m3),
        't'    => (string)(int)$tsec,
        'Temp' => ($tempC !== null) ? fmt($tempC,2) : ''
      ];
    }
  };

  $fill5(1,  $h1_1, $h2_1, $v1, $t1);
  $fill5(6,  $h1_2, $h2_2, $v2, $t2);
  $fill5(11, $h1_3, $h2_3, $v3, $t3);
}
?>


<?php page_require_level(2); ?>
<?php include_once('../components/header.php'); ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Hydraulic Conductivity (Constant Head)</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">ASTM D2434</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">

      <form class="row" action="constant_head.php" method="post" id="d2434Form">


        <?php echo display_msg($msg); ?>

        <div id="product_info"></div>

        <!-- =========================
             CARD 1: Laboratory / Sample Info
        ========================== -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
            
               Trial Information
              </h5>

              <div class="row g-3">
                <!-- Laboratory -->
               
                <div class="col-md-4">
                  <label class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician">
                </div>
                

                <!-- Standard / Dates -->
                <div class="col-md-4">
                  <label class="form-label">Test Standard</label>
                  <select class="form-select" name="Standard">
                    <option value="ASTM D2434" selected>ASTM D2434</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Test Date</label>
                  <input type="date" class="form-control" name="TestDate">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Report Date</label>
                  <input type="date" class="form-control" name="ReportDate">
                </div>

                <!-- Method -->
                <div class="col-md-4">
                  <label class="form-label">Test Method</label>
                  <select class="form-select" name="TestMethod">
                    <option value="Constant Head" selected>Constant Head</option>
                    <option value="Falling Head">Falling Head</option>
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
               

              </div>
            </div>
          </div>
        </div>

        <!-- =========================
             CARD 2: Specimen / Density / Geometry
        ========================== -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Specimen Properties</h5>

              <div class="row g-3">

                <div class="col-md-4">
                  <label class="form-label">Maximum Dry Density (kg/m³)</label>
                  <input type="text" class="form-control" name="MDD" id="MDD">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Optimum Moisture Content (%)</label>
                  <input type="text" class="form-control" name="OMC" id="OMC">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Specific Gravity (Gs)</label>
                  <input type="text" class="form-control" name="Gs" id="Gs" value="2.78">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Diameter D (m)</label>
                  <input type="text" class="form-control" name="D" id="D" value="<?=h($prefill['D'] ?? '0.113')?>" oninput="D2434_CalcGeom()">

                </div>

                <div class="col-md-3">
                  <label class="form-label">Length L (m)</label>
                  <input type="text" class="form-control" name="L" id="L" value="<?=h($prefill['L'] ?? '0.150')?>" oninput="D2434_CalcGeom()">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Area A (m²)</label>
                 <input type="text" class="form-control" name="A" id="A" value="<?=h($prefill['A'] ?? '0.010100')?>"readonly>

                </div>

                <div class="col-md-3">
                  <label class="form-label">Volume V (m³)</label>
                  <input type="text" class="form-control" name="V" id="V"
       value="<?=h($prefill['V'] ?? '0.0015')?>"
       readonly>

                </div>

                <div class="col-md-4">
                  <label class="form-label">Weight of Specimen (kg)</label>
                  <input type="text" class="form-control" name="SpecWeight" id="SpecWeight" value="<?=h($prefill['SpecWeight'] ?? '')?>" oninput="D2434_CalcDensity()">

                </div>

                <div class="col-md-4">
                  <label class="form-label">Density of Specimen (kg/m³)</label>
                  <input type="text" class="form-control" name="SpecDensity" id="SpecDensity" readonly>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Relative Density (%)</label>
                  <input type="text" class="form-control" name="RelDensity" id="RelDensity">
                </div>

              </div>

            </div>
          </div>
        </div>

        <!-- =========================
             TABLE: Test Data
        ========================== -->
        <div class="col-lg-9">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Data (15 Runs)</h5>

              <div class="table-responsive">
                <table class="table table-bordered table-sm" oninput="D2434_Recalc()">
                  <thead class="table-light">
                    <tr>
                      <th>Test #</th>
                      <th>h1 (m)</th>
                      <th>h2 (m)</th>
                      <th>h (m)</th>
                      <th>Q (m³)</th>
                      <th>t (s)</th>
                      <th>v = Q/(A·t)</th>
                      <th>i = h/L</th>
                      <th>Temp (°C)</th>
                      <th>μ(T)</th>
                      <th>K (m/s)</th>
                      <th>K20 (m/s)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=1;$i<=15;$i++): ?>
                    <tr>
                      <td class="text-center"><?php echo $i; ?></td>

                      <td><input class="form-control form-control-sm" name="rows[<?php echo $i; ?>][h1]" id="h1_<?php echo $i; ?>" value="<?=h($prefill['rows'][$i]['h1'] ?? '')?>"></td>

                      <td><input class="form-control form-control-sm" name="rows[<?php echo $i; ?>][h2]" id="h2_<?php echo $i; ?>" value="<?=h($prefill['rows'][$i]['h2'] ?? '')?>"></td>

                      <td><input class="form-control form-control-sm" name="rows[<?php echo $i; ?>][h]"  id="h_<?php echo $i; ?>" readonly></td>

                      <td><input class="form-control form-control-sm"
           name="rows[<?php echo $i; ?>][Q]" id="Q_<?php echo $i; ?>"
           value="<?=h($prefill['rows'][$i]['Q'] ?? '')?>"></td>

                      <td><input class="form-control form-control-sm"
           name="rows[<?php echo $i; ?>][t]" id="t_<?php echo $i; ?>"
           value="<?=h($prefill['rows'][$i]['t'] ?? '60')?>"></td>


                      <td><input class="form-control form-control-sm" id="v_<?php echo $i; ?>" readonly></td>
                      <td><input class="form-control form-control-sm" id="i_<?php echo $i; ?>" readonly></td>

                     <td><input class="form-control form-control-sm"
           name="rows[<?php echo $i; ?>][Temp]" id="Temp_<?php echo $i; ?>"
           value="<?=h($prefill['rows'][$i]['Temp'] ?? '')?>"></td>

                      <td><input class="form-control form-control-sm" id="mu_<?php echo $i; ?>" readonly></td>

                      <td><input class="form-control form-control-sm" id="K_<?php echo $i; ?>" readonly></td>
                      <td><input class="form-control form-control-sm" id="K20_<?php echo $i; ?>" readonly></td>
                    </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-md-4">
                  <label class="form-label">Average K20 (m/s)</label>
                  <input type="text" class="form-control" name="AvgK20" id="AvgK20" readonly>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Hydraulic Behavior (K20)</label>
                  <input type="text" class="form-control" name="HydBehavior" id="HydBehavior" readonly>
                </div>
                <div class="col-md-4">
                  <label class="form-label">μ(20°C)</label>
                  <input type="text" class="form-control" value="1.002" readonly>
                </div>
              </div>

            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Laboratory Comments</h5>
              <textarea class="form-control" name="Comments" style="height: 140px;"></textarea>
            </div>
          </div>

        </div>

        <!-- =========================
             ACTIONS
        ========================== -->
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>

              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="d2434-save">
                  Save Essay
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                  Print
                </button>
              </div>

            </div>
          </div>
        </div>

      </form>

    </div>
  </section>

</main>

<script src="../js/d2434.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof D2434_Recalc === 'function') D2434_Recalc();
    if (typeof D2434_CalcGeom === 'function') D2434_CalcGeom();
  });
</script>
<?php include_once('../components/footer.php'); ?>
