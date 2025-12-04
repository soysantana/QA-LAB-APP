<?php
$page_title = 'Grain Size Upstream Transition Fill';
require_once('../config/load.php');
$Search = find_by_id('grain_size_upstream_transition_fill', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_gs_utf'])) {
    include('../database/grain-size/gs-utf/update.php');
  } elseif (isset($_POST['repeat_gs_utf'])) {
    include('../database/grain-size/gs-utf/repeat.php');
  } elseif (isset($_POST['reviewed_gs_utf'])) {
    include('../database/grain-size/gs-utf/reviewed.php');
  } elseif (isset($_POST['delete_gs_utf'])) {
    include('../database/grain-size/gs-utf/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
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

      <form class="row" action="grain-size-upstream-transition-fill.php?id=<?php echo $Search['id']; ?>" method="post">

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
                <div class="col-md-6">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option <?php if ($Search['Standard'] == 'ASTM-C136') echo 'selected'; ?>>ASTM-C136</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                    <option <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Split_Method'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    <option <?php if ($Search['Split_Method'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
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
              <!-- Acid Reactivity -->
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
              <!-- End Acid Reactivity -->
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
                    array("4\"", "101.6", "WtRet1", "Ret1", "CumRet1", "Pass1", "Specs1"),
                    array("3.5\"", "89", "WtRet2", "Ret2", "CumRet2", "Pass2", "Specs2"),
                    array("3\"", "75", "WtRet3", "Ret3", "CumRet3", "Pass3", "Specs3"),
                    array("2.5\"", "63", "WtRet4", "Ret4", "CumRet4", "Pass4", "Specs4"),
                    array("2\"", "50.8", "WtRet5", "Ret5", "CumRet5", "Pass5", "Specs5"),
                    array("1.5\"", "37.5", "WtRet6", "Ret6", "CumRet6", "Pass6", "Specs6"),
                    array("1\"", "25", "WtRet7", "Ret7", "CumRet7", "Pass7", "Specs7"),
                    array("3/4\"", "19", "WtRet8", "Ret8", "CumRet8", "Pass8", "Specs8"),
                    array("1/2\"", "12.5", "WtRet9", "Ret9", "CumRet9", "Pass9", "Specs9"),
                    array("3/8\"", "9.5", "WtRet10", "Ret10", "CumRet10", "Pass10", "Specs10"),
                    array("No. 4", "4.75", "WtRet11", "Ret11", "CumRet11", "Pass11", "Specs11"),
                    array("No. 10", "2", "WtRet12", "Ret12", "CumRet12", "Pass12", "Specs12"),
                    array("No. 200", "0.075", "WtRet13", "Ret13", "CumRet13", "Pass13", "Specs13")
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 2) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } else {
                        $readonly = ($index >= 3 && $index <= 5) ? 'readonly tabindex="-1"' : '';
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
                <button type="submit" class="btn btn-success" name="update_gs_utf">Update Essay</button>

                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" data-exportar="GS-UTF-Build">Contruccion</a></li>
                    <li><a class="dropdown-item" data-exportar="GS-UTF-Acopio">Acopio</a></li>
                  </ul>
                </div>

                <button type="submit" class="btn btn-danger" name="delete_gs_utf"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat_gs_utf">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed_gs_utf">Reviewed</button>
                  <button type="button"class="btn btn-warning" id="btnReviewUTF">General Revision</button>

                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>
<!-- ================================
     MODAL — GENERAL UTF REVISION
================================= -->
<div class="modal fade" id="reviewModalUTF" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">General Revision — UTF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- =======================================
             GRADATION SUMMARY UTF (Sand + Fines)
        ======================================== -->
        <h5 class="mt-2">Gradation — UTF</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Parameter</th>
              <th>Value</th>
              <th>Required</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="reviewBodyGradUTF"></tbody>
        </table>

        <div class="alert alert-info mt-2" id="insightGradUTF"></div>

        <!-- =======================================
             REACTIVITY SUMMARY
        ======================================== -->
        <h5 class="mt-4">Reactivity Test</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Test</th>
              <th>Value</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="reviewBodyReactUTF"></tbody>
        </table>

        <div class="alert alert-info mt-2" id="insightReactUTF"></div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        <button type="button"
                class="btn btn-warning"
                id="btnSaveReviewUTF">
          Save Review
        </button>
      </div>

    </div>
  </div>
</div>




</main><!-- End #main -->

<script type="module" src="../js/grain-size/gs-utf.js"></script>

<script>
/*************************************************
 * 1. CLICK → REVISIÓN COMPLETA UTF
 *************************************************/
document.getElementById("btnReviewUTF").addEventListener("click", () => {

    /***********************************************
     *  ESPECIFICACIONES UTF (SAND / FINES)
     ***********************************************/
    const structure = document.getElementById("Structure").value.trim().toUpperCase();

    // SPEC LIMITS per structure
    const limits = {
        LLD:     { SandMin: 35, FinesMax: 6 },
        SD1:     { SandMin: 35, FinesMax: 6 },
        SD2:     { SandMin: 35, FinesMax: 6 },
        SD3:     { SandMin: 35, FinesMax: 6 },
        "PVDJ-AGG": { SandMin: 40, FinesMax: 4 },
        "PVDJ-AGG-DIO": { SandMin: 40, FinesMax: 4 }
    };

    let spec;

    if (["LLD","SD1","SD2","SD3"].includes(structure)) {
        spec = limits.LLD;
    } else if (structure.includes("PVDJ-AGG-DIO")) {
        spec = limits["PVDJ-AGG-DIO"];
    } else {
        spec = limits["PVDJ-AGG"];
    }

    /***********************************************
     * CAPTURAR VARIABLES CALCULADAS
     ***********************************************/
    const sand  = parseFloat(document.getElementById("Sand").value);
    const fines = parseFloat(document.getElementById("Fines").value);

    const reaction = document.getElementById("ReactionResult").value.trim();
    const acid = document.getElementById("AcidResult").value.trim();


    /***********************************************
     * 1) REVISIÓN DE SAND / FINES
     ***********************************************/
    let rowsGrad = "";
    let failuresGrad = [];

    // Sand
    const sandOK = (!isNaN(sand) && sand >= spec.SandMin);
    if (!sandOK) failuresGrad.push("Sand");

    rowsGrad += `
        <tr>
            <td>Sand %</td>
            <td>${isNaN(sand) ? "-" : sand.toFixed(2)}</td>
            <td>> ${spec.SandMin}</td>
            <td>${sandOK ? "<span class='badge bg-success'>Passed</span>" : "<span class='badge bg-danger'>Failed</span>"}</td>
        </tr>
    `;

    // Fines
    const finesOK = (!isNaN(fines) && fines <= spec.FinesMax);
    if (!finesOK) failuresGrad.push("Fines");

    rowsGrad += `
        <tr>
            <td>Fines %</td>
            <td>${isNaN(fines) ? "-" : fines.toFixed(2)}</td>
            <td>< ${spec.FinesMax}</td>
            <td>${finesOK ? "<span class='badge bg-success'>Passed</span>" : "<span class='badge bg-danger'>Failed</span>"}</td>
        </tr>
    `;

    document.getElementById("reviewBodyGradUTF").innerHTML = rowsGrad;

    // Insight GRAD
    let insightGradUTF = "";
    if (failuresGrad.length === 0) {
        insightGradUTF =
            `<b>Gradation UTF: Passed</b><br>Material meets all UTF requirements.`;
    } else {
        insightGradUTF =
            `Material does not meet on: <b>${failuresGrad.join(", ")}%.`;
    }

    document.getElementById("insightGradUTF").innerHTML = insightGradUTF;


    /***********************************************
     * 2) REACTIVITY CHECK
     ***********************************************/
    let failuresReact = [];

    const reactionPass = (reaction === "No Reaction" || reaction === "Weak Reaction" || reaction === "Moderate Reaction");
    const acidPass = (acid === "Accepted");

    if (!reactionPass) failuresReact.push("Reaction Strength");
    if (!acidPass) failuresReact.push("Acid Reactivity");

    let reactRows = `
        <tr>
            <td>Reaction Strength</td>
            <td>${reaction}</td>
            <td>${reactionPass ? "<span class='badge bg-success'>Passed</span>" : "<span class='badge bg-danger'>Failed</span>"}</td>
        </tr>
        <tr>
            <td>Acid Reactivity</td>
            <td>${acid}</td>
            <td>${acidPass ? "<span class='badge bg-success'>Passed</span>" : "<span class='badge bg-danger'>Failed</span>"}</td>
        </tr>
    `;

    document.getElementById("reviewBodyReactUTF").innerHTML = reactRows;

    // Insight REACT
    let insightReactUTF = "";

    if (failuresReact.length === 0) {
        insightReactUTF =
            ``;
    } else {
        insightReactUTF =
            `<b>Reactivity UTF: Failed</b><br>${failuresReact.join(", ")} out of spec.`;
    }

    document.getElementById("insightReactUTF").innerHTML = insightReactUTF;


    /***********************************************
     * 3) GUARDAR ESTADO EN VARIABLES GLOBALES
     ***********************************************/
    window.UTF_Gradation_Status = failuresGrad.length === 0 ? "Passed" : "Failed";
    window.UTF_Gradation_Insight = insightGradUTF.replace(/<[^>]*>?/gm, '');

    window.UTF_Reactivity_Status = failuresReact.length === 0 ? "Passed" : "Failed";
    window.UTF_Reactivity_Insight = insightReactUTF.replace(/<[^>]*>?/gm, '');


    /***********************************************
     * MOSTRAR MODAL
     ***********************************************/
    new bootstrap.Modal(document.getElementById("reviewModalUTF")).show();
});



/*************************************************
 * 4. BOTÓN → GUARDAR REVISIÓN UTF
 *************************************************/
document.getElementById("btnSaveReviewUTF").addEventListener("click", () => {

    const commonData = {
        Sample_ID: document.getElementById("SampleName").value,
        Sample_Number: document.getElementById("SampleNumber").value,
        Structure: document.getElementById("Structure").value,
        Area: document.getElementById("Area").value,
        Source: document.getElementById("Source").value,
        Material_Type: "UTF",
        Report_Date: new Date().toISOString().slice(0,19).replace("T"," ")
    };

    /*******************************
     * 1) REGISTRO UTF — GRADATION
     *******************************/
    const gradPayload = {
        ...commonData,
        Test_Type: "Gradation",
        Test_Condition: window.UTF_Gradation_Status,
        Noconformidad: window.UTF_Gradation_Insight,
        Comments: `At ${commonData.Area} - From ${commonData.Source}`
    };

    /*******************************
     * 2) REGISTRO UTF — REACTIVITY
     *******************************/
    const reactPayload = {
        ...commonData,
        Test_Type: "Acid Reactivity",
        Test_Condition: window.UTF_Reactivity_Status,
        Noconformidad: window.UTF_Reactivity_Insight,
        Comments: `At ${commonData.Area} - From ${commonData.Source}`
    };

    /*******************************
     * ENVIAR AMBOS REGISTROS
     *******************************/
    Promise.all([
        fetch("../database/insert_review.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(gradPayload)
        }),

        fetch("../database/insert_review.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(reactPayload)
        })
    ])
    .then(() => {
        alert("UTF Review saved successfully (2 records).");
    })
    .catch(err => console.error(err));

});
</script>


<?php include_once('../components/footer.php');  ?>