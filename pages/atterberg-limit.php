<?php
  $page_title = 'Atterberg Limit';
  $class_form = ' ';
  $form_show = 'show';
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
  <div class="row">

  <div id="product_info"></div>

    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Trial Information</h5>

          <!-- Multi Columns Form -->
          <form class="row g-3">
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select">
                <option selected>Choose...</option>
                <option>ASTM</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="PMethods" class="form-label">Preparation Methods</label>
              <select id="PMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Oven Dried</option>
                <option>Air Dried</option>
                <option>Microwave Dried</option>
                <option>Wet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="SMethods" class="form-label">Split Methods</label>
              <select id="SMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Manual</option>
                <option>Mechanical</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="NatMc" class="form-label">Natural Mc %</label>
              <input type="text" class="form-control" id="NatMc" oninput="LLyPL()">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" style="height: 100px;"></textarea>
            </div>
          </form><!-- End Multi Columns Form -->

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
                    <td><input type="text" style="border: none;" class="form-control" id="Blows1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="Blows2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="Blows3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" id="LLContainer1"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLContainer2"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLContainer3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="LLWetSoil1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLWetSoil2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLWetSoil3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="LLDrySoilTare1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLDrySoilTare2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLDrySoilTare3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><p style="border: none;" class="form-control" id="LLWater1"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLWater2"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLWater3"></p></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="LLTare1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLTare2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="LLTare3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><p style="border: none;" class="form-control" id="LLWtDrySoil1"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLWtDrySoil2"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLWtDrySoil3"></p></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><p style="border: none;" class="form-control" id="LLMCPorce1"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLMCPorce2"></p></td>
                    <td><p style="border: none;" class="form-control" id="LLMCPorce3"></p></td>
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
                    <td><input type="text" style="border: none;" class="form-control" id="PLContainer1"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLContainer2"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLContainer3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="PLWetSoil1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLWetSoil2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLWetSoil3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="PLDrySoilTare1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLDrySoilTare2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLDrySoilTare3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Water (gr)</th>
                    <td><p style="border: none;" class="form-control" id="PLWater1"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLWater2"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLWater3"></p></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="PLTare1" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLTare2" oninput="LLyPL()"></td>
                    <td><input type="text" style="border: none;" class="form-control" id="PLTare3" oninput="LLyPL()"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><p style="border: none;" class="form-control" id="PLWtDrySoil1"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLWtDrySoil2"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLWtDrySoil3"></p></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><p style="border: none;" class="form-control" id="PLMCPorce1"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLMCPorce2"></p></td>
                    <td><p style="border: none;" class="form-control" id="PLMCPorce3"></p></td>
                  </tr>
                  <tr>
                    <th scope="row">Avg. Moisture Content (%)</th>
                    <td colspan="3"><p style="border: none;" class="form-control" id="PLAvgMcPorce"></p></td>
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
              <td><p style="border: none;" class="form-control" id="LLPorce"></p></td>
            </tr>
            <tr>
              <th scope="row">Plastic Limit (%):</th>
              <td><p style="border: none;" class="form-control" id="PLPorce"></p></td>
            </tr>
            <tr>
              <th scope="row">Plasticity Index (%):</th>
              <td><p style="border: none;" class="form-control" id="PLIndexPorce"></p></td>
            </tr>
            <tr>
              <th scope="row">Liquidity Index (%):</th>
              <td><p style="border: none;" class="form-control" id="LLIndexPorce"></p></td>
            </tr>
          </tbody>
        </table>
        <!-- End Default Table Example -->
        <!-- Summary Atteberg Limit Parameter -->
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row" style="width: 160px;">Soil Classification as per Unified Soil Classification System, ASTM designation D2487-06</th>
              <td ><p style="border: none;" class="form-control" id="classifysoil"></p></td>
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
          <button type="button" class="btn btn-success">Save Essay</button>
          <button type="button" class="btn btn-primary disabled">Search Moisture</button>
        </div>

      </div>
    </div>
  
  </div>

  </div>
</section>

</main><!-- End #main -->


<script>
  $("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Liquid-Limit-Plot.js",
      type: "POST",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
    $.ajax({
      url: "../libs/graph/Plasticity-Chart.js",
      type: "POST",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }
</script>



<script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
<script src="../js/Atterberg-Limit.js"></script>
<script src="../libs/graph/Liquid-Limit-Plot.js"></script>
<script src="../libs/graph/Plasticity-Chart.js"></script>

<?php include_once('../components/footer.php');  ?>