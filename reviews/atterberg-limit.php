<?php
$page_title = 'Atterberg Limit';
require_once('../config/load.php');
$Search = find_by_id('atterberg_limit', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-atterberg-limit'])) {
    include('../database/atterberg-limit.php');
  } elseif (isset($_POST['repeat-atterberg-limit'])) {
    include('../database/atterberg-limit.php');
  } elseif (isset($_POST['reviewed-atterberg-limit'])) {
    include('../database/atterberg-limit.php');
  } elseif (isset($_POST['delete_al'])) {
    include('../database/atterberg-limit.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Atterberg Limit</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Atterberg Limit</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row" oninput="LLyPL()">

      <form class="row" action="atterberg-limit.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <!-- Laboratory Information -->
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'ASTM-D4318') echo 'selected'; ?>>ASTM-D4318</option>
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
                  <label for="NatMc" class="form-label">Natural Mc %</label>
                  <input type="text" class="form-control" name="NatMc" id="NatMc" value="<?php echo ($Search['Nat_Mc']); ?>">
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
        <!-- End Laboratory Information -->

        <!-- Liquid Limit and Plastic Limit Trials -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Liquid Limit</h5>
              <!-- Table for Liquid Limit Trials -->
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
                  <tr>
                    <th scope="row">No. of Blows</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Blows1" id="Blows1" value="<?php echo ($Search['LL_Blows_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="Blows2" id="Blows2" value="<?php echo ($Search['LL_Blows_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="Blows3" id="Blows3" value="<?php echo ($Search['LL_Blows_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer1" id="LLContainer1" value="<?php echo ($Search['LL_Container_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer2" id="LLContainer2" value="<?php echo ($Search['LL_Container_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer3" id="LLContainer3" value="<?php echo ($Search['LL_Container_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil1" id="LLWetSoil1" value="<?php echo ($Search['LL_Wet_Soil_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil2" id="LLWetSoil2" value="<?php echo ($Search['LL_Wet_Soil_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil3" id="LLWetSoil3" value="<?php echo ($Search['LL_Wet_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare1" id="LLDrySoilTare1" value="<?php echo ($Search['LL_Dry_Soil_Tare_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare2" id="LLDrySoilTare2" value="<?php echo ($Search['LL_Dry_Soil_Tare_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare3" id="LLDrySoilTare3" value="<?php echo ($Search['LL_Dry_Soil_Tare_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater1" id="LLWater1" readonly tabindex="-1" value="<?php echo ($Search['LL_Water_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater2" id="LLWater2" readonly tabindex="-1" value="<?php echo ($Search['LL_Water_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater3" id="LLWater3" readonly tabindex="-1" value="<?php echo ($Search['LL_Water_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare1" id="LLTare1" value="<?php echo ($Search['LL_Tare_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare2" id="LLTare2" value="<?php echo ($Search['LL_Tare_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare3" id="LLTare3" value="<?php echo ($Search['LL_Tare_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil1" id="LLWtDrySoil1" readonly tabindex="-1" value="<?php echo ($Search['LL_Wt_Dry_Soil_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil2" id="LLWtDrySoil2" readonly tabindex="-1" value="<?php echo ($Search['LL_Wt_Dry_Soil_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil3" id="LLWtDrySoil3" readonly tabindex="-1" value="<?php echo ($Search['LL_Wt_Dry_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce1" id="LLMCPorce1" readonly tabindex="-1" value="<?php echo ($Search['LL_MC_Porce_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce2" id="LLMCPorce2" readonly tabindex="-1" value="<?php echo ($Search['LL_MC_Porce_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce3" id="LLMCPorce3" readonly tabindex="-1" value="<?php echo ($Search['LL_MC_Porce_3']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Table for Liquid Limit Trials -->
              <h5 class="card-title">Plastic Limit</h5>
              <!-- Table for Plastic Limit Trials -->
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
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer1" id="PLContainer1" value="<?php echo ($Search['PL_Container_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer2" id="PLContainer2" value="<?php echo ($Search['PL_Container_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer3" id="PLContainer3" value="<?php echo ($Search['PL_Container_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil1" id="PLWetSoil1" value="<?php echo ($Search['PL_Wet_Soil_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil2" id="PLWetSoil2" value="<?php echo ($Search['PL_Wet_Soil_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil3" id="PLWetSoil3" value="<?php echo ($Search['PL_Wet_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare1" id="PLDrySoilTare1" value="<?php echo ($Search['PL_Dry_Soil_Tare_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare2" id="PLDrySoilTare2" value="<?php echo ($Search['PL_Dry_Soil_Tare_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare3" id="PLDrySoilTare3" value="<?php echo ($Search['PL_Dry_Soil_Tare_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater1" id="PLWater1" readonly tabindex="-1" value="<?php echo ($Search['PL_Water_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater2" id="PLWater2" readonly tabindex="-1" value="<?php echo ($Search['PL_Water_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater3" id="PLWater3" readonly tabindex="-1" value="<?php echo ($Search['PL_Water_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare1" id="PLTare1" value="<?php echo ($Search['PL_Tare_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare2" id="PLTare2" value="<?php echo ($Search['PL_Tare_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare3" id="PLTare3" value="<?php echo ($Search['PL_Tare_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil1" id="PLWtDrySoil1" readonly tabindex="-1" value="<?php echo ($Search['PL_Wt_Dry_Soil_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil2" id="PLWtDrySoil2" readonly tabindex="-1" value="<?php echo ($Search['PL_Wt_Dry_Soil_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil3" id="PLWtDrySoil3" readonly tabindex="-1" value="<?php echo ($Search['PL_Wt_Dry_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce1" id="PLMCPorce1" readonly tabindex="-1" value="<?php echo ($Search['PL_MC_Porce_1']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce2" id="PLMCPorce2" readonly tabindex="-1" value="<?php echo ($Search['PL_MC_Porce_2']); ?>"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce3" id="PLMCPorce3" readonly tabindex="-1" value="<?php echo ($Search['PL_MC_Porce_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Avg. Moisture Content (%)</th>
                    <td colspan="3"><input type="text" style="border: none;" class="form-control" name="PLAvgMcPorce" id="PLAvgMcPorce" readonly tabindex="-1" value="<?php echo ($Search['PL_Avg_Mc_Porce']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Table for Plastic Limit Trials -->
            </div>
          </div>
        </div>
        <!-- End Liquid Limit and Plastic Limit Trials -->

        <!-- Charts Liquid Limit and Plasticity Index -->
        <div class="col-lg-4">

          <!-- Multi Point Liquid Limit Plot Chart -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>
              <div id="liquid-limit" style="min-height: 400px;" class="echart"></div>
            </div>
          </div>
          <!-- End Multi Point Liquid Limit Plot Chart -->

          <!-- Plasticity Chart -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>
              <div id="PlasticityChart" style="min-height: 400px;" class="echart"></div>
            </div>
          </div>
          <!-- End Plasticity Chart -->

        </div>
        <!-- End Charts Liquid Limit and Plasticity Index -->

        <!-- Summary AL Parameter and Actions Buttons -->
        <div class="col-lg-3">

          <!-- Summary Atteberg Limit Parameter -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Summary Atteberg Limit Parameter</h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row" style="width: 175px;">Liquid Limit (%):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLPorce" id="LLPorce" readonly tabindex="-1" value="<?php echo ($Search['Liquid_Limit_Porce']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Plastic Limit (%):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLPorce" id="PLPorce" readonly tabindex="-1" value="<?php echo ($Search['Plastic_Limit_Porce']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Plasticity Index (%):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLIndexPorce" id="PLIndexPorce" readonly tabindex="-1" value="<?php echo ($Search['Plasticity_Index_Porce']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Liquidity Index (%):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLIndexPorce" id="LLIndexPorce" readonly tabindex="-1" value="<?php echo ($Search['Liquidity_Index_Porce']); ?>"></td>
                    <input hidden style="border: none;" class="form-control" name="Rsquared" id="Rsquared" readonly tabindex="-1" value="<?php echo ($Search['Rsquared']); ?>">
                  </tr>
                </tbody>
              </table>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row" style="width: 160px;">Soil Classification as per Unified Soil Classification System, ASTM designation D2487-06</th>
                    <td><input type="text" style="border: none;" class="form-control" name="classifysoil" id="classifysoil" readonly tabindex="-1" value="<?php echo ($Search['Classification']); ?>"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
          <!-- End Summary Atteberg Limit Parameter -->

          <!-- Actions Buttons -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>

              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-atterberg-limit">UpdateEssay</button>

                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" onclick="enviarImagenAlServidor('AL-Naranjo')">Naranjo</a></li>
                    <li><a class="dropdown-item" onclick="enviarImagenAlServidor('AL-Build')">Contruccion</a></li>
                  </ul>
                </div>
                <button type="submit" class="btn btn-danger" name="delete_al"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat-atterberg-limit">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed-atterberg-limit">Reviewed</button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary" onclick="search()">Search Moisture</button>
              </div>
              <div class="mt-2" id="mensaje-container"></div>
            </div>
          </div>
          <!-- End Actions Buttons -->

        </div>
        <!-- End Summary AL Parameter and Actions Buttons -->

      </form><!-- End Form -->

    </div>
  </section>

</main><!-- End #main -->

<script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
<script src="../js/Atterberg-Limit.js"></script>
<script src="../libs/graph/Liquid-Limit-Plot.js?v02"></script>
<script src="../libs/graph/Plasticity-Chart.js"></script>
<?php include_once('../components/footer.php');  ?>