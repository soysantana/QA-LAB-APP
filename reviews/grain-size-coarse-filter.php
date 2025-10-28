<?php
$page_title = 'Grain Size Coarse Filter';
require_once('../config/load.php');
$Search = find_by_id('grain_size_coarse', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-gs-coarse'])) {
    include('../database/grain-size/gs-cf/update.php');
  } elseif (isset($_POST['repeat-gs-coarse'])) {
    include('../database/grain-size/gs-cf/repeat.php');
  } elseif (isset($_POST['reviewed-gs-coarse'])) {
    include('../database/grain-size/gs-cf/reviewed.php');
  } elseif (isset($_POST['delete_gs_coarse'])) {
    include('../database/grain-size/gs-cf/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Grain Size Coarse Filter</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Grain Size Coarse Filter</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="grain-size-coarse-filter.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <!-- Test Information -->
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-4">
                  <label for="specsType" class="form-label">Especificaciones</label>
                  <select id="specsType" class="form-select" name="specsType">
                    <option selected>Choose...</option>
                    <option value="CF-C">Construccion</option>
                    <option value="CF-D">Diorita</option>
                    <option value="CF-I">Agregado Investigacion</option>
                    <option value="CF-A">Agregado</option>
                    <option value="CF-N">Naranjo</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'ASTM-C136') echo 'selected'; ?>>ASTM-C136</option>
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
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="ProductionDate" class="form-label">Production Date</label>
                  <input type="date" class="form-control" name="ProductionDate" id="ProductionDate" value="<?php echo ($Search['ProductionDate']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- End Test Information -->

        <!-- Weighing Information and Acid Reactivity Test -->
        <div class="col-lg-5">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Weighing Information -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Container" id="Container" value="<?php echo ($Search['Container']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil" id="WetSoil" value="<?php echo ($Search['Wet_Soil_Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoilTare" id="DrySoilTare" value="<?php echo ($Search['Wet_Dry_Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Tare" id="Tare" value="<?php echo ($Search['Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoil" id="DrySoil" readonly tabindex="-1" value="<?php echo ($Search['Wt_Dry_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Washed (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Washed" id="Washed" value="<?php echo ($Search['Wt_Washed']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wash Pan (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WashPan" id="WashPan" readonly tabindex="-1" value="<?php echo ($Search['Wt_Wash_Pan']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Weighing Information -->

              <h5 class="card-title">Reactivity Test Method FM13-007</h5>
              <!-- Reactivity Test -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Total Sample Weight (g):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalWeight" id="TotalWeight" value="<?php echo ($Search['Total_Sample_Weight']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight used for the Test (g):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeigtTest" id="WeigtTest" value="<?php echo ($Search['Weight_Used_For_The_Test']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">A Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles1" id="Particles1" value="<?php echo ($Search['A_Particles_Reactive']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">B Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles2" id="Particles2" value="<?php echo ($Search['B_Particles_Reactive']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">C Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles3" id="Particles3" value="<?php echo ($Search['C_Particles_Reactive']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight Mat. Ret. No. 4 (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightNo4" id="WeightNo4" value="<?php echo ($Search['Weight_Mat_Ret_No_4']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Reactive Part. Ret. No.4 (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightReactiveNo4" id="WeightReactiveNo4" value="<?php echo ($Search['Weight_Reactive_Part_Ret_No_4']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Percent Reactive Particles (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PercentReactive" id="PercentReactive" value="<?php echo ($Search['Percent_Reactive_Particles']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average Particles Reactive:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AvgParticles" id="AvgParticles" readonly tabindex="-1" value="<?php echo ($Search['Average_Particles_Reactive']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Reaction Strength Result:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ReactionResult" id="ReactionResult" readonly tabindex="-1" value="<?php echo ($Search['Reaction_Strength_Result']); ?>"></td>
                  </tr>
                </tbody>
              </table>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Acid Reactivity Test Result</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AcidResult" id="AcidResult" readonly tabindex="-1" value="<?php echo ($Search['Acid_Reactivity_Test_Result']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Reactivity Test -->

            </div>
          </div>
        </div>
        <!-- End Weighing Information and Acid Reactivity Test -->

        <!-- Grain Size and Classification -->
        <div class="col-lg-7">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grain Size Distribution</h5>
              <!-- Data Grain Size -->
              <table id="grainTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Screen</th>
                    <th scope="col">(mm)</th>
                    <th scope="col">Wt Ret</th>
                    <th scope="col">% Ret</th>
                    <th scope="col">Cum % Ret</th>
                    <th scope="col">% Pass</th>
                    <th scope="col">Specs</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("5\"", "127", "WtRet1", "Ret1", "CumRet1", "Pass1", "Specs1"),
                    array("4\"", "101.6", "WtRet2", "Ret2", "CumRet2", "Pass2", "Specs2"),
                    array("3.5\"", "88.9", "WtRet3", "Ret3", "CumRet3", "Pass3", "Specs3"),
                    array("3\"", "76.2", "WtRet4", "Ret4", "CumRet4", "Pass4", "Specs4"),
                    array("2.5\"", "63.5", "WtRet5", "Ret5", "CumRet5", "Pass5", "Specs5"),
                    array("2\"", "50.8", "WtRet6", "Ret6", "CumRet6", "Pass6", "Specs6"),
                    array("1.5\"", "38.1", "WtRet7", "Ret7", "CumRet7", "Pass7", "Specs7"),
                    array("1\"", "25", "WtRet8", "Ret8", "CumRet8", "Pass8", "Specs8"),
                    array("3/4\"", "19", "WtRet9", "Ret9", "CumRet9", "Pass9", "Specs9"),
                    array("1/2\"", "12.7", "WtRet10", "Ret10", "CumRet10", "Pass10", "Specs10"),
                    array("3/8\"", "9.5", "WtRet11", "Ret11", "CumRet11", "Pass11", "Specs11"),
                    array("No. 4", "4.75", "WtRet12", "Ret12", "CumRet12", "Pass12", "Specs12"),
                    array("No. 10", "2", "WtRet13", "Ret13", "CumRet13", "Pass13", "Specs13"),
                    array("No. 16", "1.18", "WtRet14", "Ret14", "CumRet14", "Pass14", "Specs14"),
                    array("No. 20", "0.85", "WtRet15", "Ret15", "CumRet15", "Pass15", "Specs15"),
                    array("No. 50", "0.3", "WtRet16", "Ret16", "CumRet16", "Pass16", "Specs16"),
                    array("No. 60", "0.25", "WtRet17", "Ret17", "CumRet17", "Pass17", "Specs17"),
                    array("No. 200", "0.075", "WtRet18", "Ret18", "CumRet18", "Pass18", "Specs18")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 2) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = ($index >= 3 && $index <= 8) ? 'readonly tabindex="-1"' : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . ' value="' . $Search[$valor] . '"></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                  <tr>
                    <th scope="row" colspan="2">Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PanWtRen" id="PanWtRen" value="<?php echo ($Search['PanWtRen']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PanRet" id="PanRet" readonly tabindex="-1" value="<?php echo ($Search['PanRet']); ?>"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Total Pan</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" readonly tabindex="-1" value="<?php echo ($Search['TotalWtRet']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" readonly tabindex="-1" value="<?php echo ($Search['TotalRet']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" readonly tabindex="-1" value="<?php echo ($Search['TotalCumRet']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" readonly tabindex="-1" value="<?php echo ($Search['TotalPass']); ?>"></td>
                    <td></td>
                  </tr>

                </tbody>
              </table>
              <!-- End Data Grain Size -->
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Coarse Grained Classification using the USCS</h5>
              <!-- Classification USCS -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td><input type="text" style="border: none;" class="form-control text-center" name="ClassificationUSCS1" id="ClassificationUSCS1" value="<?php echo ($Search['ClassificationUSCS1']); ?>" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <td><input type="text" style="border: none;" class="form-control text-center" name="ClassificationUSCS2" id="ClassificationUSCS2" value="<?php echo ($Search['ClassificationUSCS2']); ?>" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Classification USCS -->
            </div>
          </div>

        </div>
        <!-- End Grain Size and Classification -->

        <!-- Chart the Grain Size Distribution -->
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <div id="GrainSizeChart" style="min-height: 400px;" class="echart"></div>

            </div>
          </div>

        </div>
        <!-- End Chart the Grain Size Distribution -->

        <!-- Summary GS Parameters -->
        <div class="col-lg-4">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Summary Grain Size Distribution Parameter</h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Coarser than Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="CoarserGravel" id="CoarserGravel" readonly tabindex="-1" value="<?php echo ($Search['Coarser_than_Gravel']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Gravel%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Gravel" id="Gravel" readonly tabindex="-1" value="<?php echo ($Search['Gravel']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Sand%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Sand" id="Sand" readonly tabindex="-1" value="<?php echo ($Search['Sand']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Fines%</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Fines" id="Fines" readonly tabindex="-1" value="<?php echo ($Search['Fines']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D10 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D10" id="D10" readonly tabindex="-1" value="<?php echo ($Search['D10']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D15 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D15" id="D15" readonly tabindex="-1" value="<?php echo ($Search['D15']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D30 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D30" id="D30" readonly tabindex="-1" value="<?php echo ($Search['D30']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D60 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D60" id="D60" readonly tabindex="-1" value="<?php echo ($Search['D60']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D85 (mm) :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="D85" id="D85" readonly tabindex="-1" value="<?php echo ($Search['D85']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cc :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cc" id="Cc" readonly tabindex="-1" value="<?php echo ($Search['Cc']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Cu :</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Cu" id="Cu" readonly tabindex="-1" value="<?php echo ($Search['Cu']); ?>"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
        <!-- End Summary GS Parameters -->

        <!-- Actions Buttons -->
        <div class="col-lg-2">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>

              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-gs-coarse">Update Essay</button>

                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" data-exportar="GS-CF-Build">Contruccion</a></li>
                    <li><a class="dropdown-item" data-exportar="GS-CF-Naranjo">Naranjo</a></li>
                    <li><a class="dropdown-item" data-exportar="GS-CF-Diorite">Diorita</a></li>
                    <li><a class="dropdown-item" data-exportar="GS-CF-Acopio">Acopio</a></li>
                  </ul>
                </div>

                <button type="submit" class="btn btn-danger" name="delete_gs_coarse"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat-gs-coarse">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed-gs-coarse">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>
          <!-- End Actions Buttons -->

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>

</main><!-- End #main -->



<script>
document.querySelectorAll('[data-exportar]').forEach(btn => {
  btn.addEventListener('click', async e => {
    const tipo = e.target.dataset.exportar;
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (!id) {
      alert("No se encontró el parámetro ID en la URL.");
      return;
    }

    // Obtén el gráfico si existe
    const chart = echarts.getInstanceByDom(document.getElementById('GrainSizeChart'));
    const chartBase64 = chart ? chart.getDataURL({
      pixelRatio: 1,
      backgroundColor: '#fff'
    }) : null;

    try {
     const res = await fetch(`/pdf/GS-CF-Acopio.php?id=${id}&mode=json`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ GrainSizeChart: chartBase64 || null })
});
      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data.error || `Error ${res.status}`);

      Swal.fire({
        icon: 'success',
        title: 'PDF generado correctamente',
        html: `
          <b>Archivo:</b> ${data.filename}<br>
          <b>Versión:</b> v${data.version}<br>
          <b>Estado:</b> ${data.result}
        `,
        confirmButtonText: 'Aceptar'
      });
    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error al generar el PDF',
        text: err.message
      });
    }
  });
});
</script>
<script type="module" src="../js/grain-size/gs-cf.js"></script>

<?php include_once('../components/footer.php');  ?>