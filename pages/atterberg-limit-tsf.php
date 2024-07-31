<?php
  $page_title = 'Atterberg Limit';
  $formPresa = ' ';
  $formPresaShow = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
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

  <form class="row" action="../database/atterberg-limit.php" method="post">

  <div id="product_info"></div>

  <div class="col-md-4">
  <?php echo display_msg($msg); ?>
  </div>
  
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Trial Information</h5>

          <!-- Multi Columns Form -->
          <div class="row g-3">
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select" name="Standard">
                <option value="ASTM-D4318">ASTM-D4318</option>
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
              <label for="NatMc" class="form-label">Natural Mc %</label>
              <input type="text" class="form-control" name="NatMc" id="NatMc">
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
              <textarea hidden class="form-control" name="PlotLimit" id="PlotLimit" style="height: 100px;"></textarea>
              <textarea hidden class="form-control" name="PlotPlasticity" id="PlotPlasticity" style="height: 100px;"></textarea>
            </div>
          </div><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-5">
    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Liquid Limit</h5>
              <!-- Bordered Table -->
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
                    <td><input type="text" style="border: none;" class="form-control" name="Blows1" id="Blows1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="Blows2" id="Blows2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="Blows3" id="Blows3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer1" id="LLContainer1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer2" id="LLContainer2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLContainer3" id="LLContainer3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil1" id="LLWetSoil1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil2" id="LLWetSoil2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWetSoil3" id="LLWetSoil3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare1" id="LLDrySoilTare1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare2" id="LLDrySoilTare2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLDrySoilTare3" id="LLDrySoilTare3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater1" id="LLWater1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater2" id="LLWater2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWater3" id="LLWater3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare1" id="LLTare1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare2" id="LLTare2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLTare3" id="LLTare3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil1" id="LLWtDrySoil1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil2" id="LLWtDrySoil2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLWtDrySoil3" id="LLWtDrySoil3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce1" id="LLMCPorce1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce2" id="LLMCPorce2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="LLMCPorce3" id="LLMCPorce3" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
              <h5 class="card-title">Plastic Limit</h5>
              <!-- Bordered Table -->
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
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer1" id="PLContainer1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer2" id="PLContainer2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLContainer3" id="PLContainer3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil1" id="PLWetSoil1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil2" id="PLWetSoil2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWetSoil3" id="PLWetSoil3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare1" id="PLDrySoilTare1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare2" id="PLDrySoilTare2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLDrySoilTare3" id="PLDrySoilTare3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater1" id="PLWater1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater2" id="PLWater2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWater3" id="PLWater3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare1" id="PLTare1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare2" id="PLTare2"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLTare3" id="PLTare3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil1" id="PLWtDrySoil1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil2" id="PLWtDrySoil2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLWtDrySoil3" id="PLWtDrySoil3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce1" id="PLMCPorce1" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce2" id="PLMCPorce2" readonly tabindex="-1"></td>
                    <td><input type="text" style="border: none;" class="form-control" name="PLMCPorce3" id="PLMCPorce3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Avg. Moisture Content (%)</th>
                    <td colspan="3"><input type="text" style="border: none;" class="form-control" name="PLAvgMcPorce" id="PLAvgMcPorce" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
    </div>

    <div class="col-lg-4">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
        
        <!-- Multi Point Liquid Limit Plot Chart -->
        <div id="liquid-limit" style="min-height: 400px;" class="echart"></div>
        <!-- End Multi Point Liquid Limit Plot Chart -->
    
      </div>
    </div>
          
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
              
        <!-- Plasticity Chart -->
        <div id="PlasticityChart" style="min-height: 400px;" class="echart"></div>
        <!-- End Plasticity Chart -->
            
      </div>
    </div>

    </div>
    
    <div class="col-lg-3">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Summary Atteberg Limit Parameter</h5>
        <!-- Summary Atteberg Limit Parameter -->
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row" style="width: 175px;">Liquid Limit (%):</th>
              <td><input type="text" style="border: none;" class="form-control" name="LLPorce" id="LLPorce" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Plastic Limit (%):</th>
              <td><input type="text" style="border: none;" class="form-control" name="PLPorce" id="PLPorce" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Plasticity Index (%):</th>
              <td><input type="text" style="border: none;" class="form-control" name="PLIndexPorce" id="PLIndexPorce" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Liquidity Index (%):</th>
              <td><input type="text" style="border: none;" class="form-control" name="LLIndexPorce" id="LLIndexPorce" readonly tabindex="-1"></td>
              <input hidden style="border: none;" class="form-control" name="Rsquared" id="Rsquared" readonly tabindex="-1">
            </tr>
          </tbody>
        </table>
        <!-- End Default Table Example -->
        <!-- Summary Atteberg Limit Parameter -->
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row" style="width: 160px;">Soil Classification as per Unified Soil Classification System, ASTM designation D2487-06</th>
              <td><input type="text" style="border: none;" class="form-control" name="classifysoil" id="classifysoil" readonly tabindex="-1"></td>
            </tr>
          </tbody>
        </table>
        <!-- End Default Table Example -->
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="atterberg-limit">Save Essay</button>
          <button type="button" class="btn btn-primary" onclick="search()">Search Moisture</button>
          <div id="mensaje-container"></div>
        </div>

      </div>
    </div>
  
  </div>

  </form><!-- End Form -->

  </div>
</section>

</main><!-- End #main -->

<script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
<script src="../js/Atterberg-Limit.js"></script>
<script src="../libs/graph/Liquid-Limit-Plot.js"></script>
<script src="../libs/graph/Plasticity-Chart.js"></script>
<?php include_once('../components/footer.php');  ?>