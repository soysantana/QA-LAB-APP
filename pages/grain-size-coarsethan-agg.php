<?php
  $page_title = 'Grain Size Coarse Than';
  $class_form = ' ';
  $form_show = 'show';
  $GrainSize = 'active';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Grain Size Coarse Than Aggregate</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Grain Size Coarse Than Aggregate</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row" oninput="CoarsethanAgg()">

  <form class="row" action="../database/grain-size-general.php" method="post">

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
                <option selected>Choose...</option>
                <option value="ASTM-C136">ASTM-C136</option>
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

    <div class="col-lg-5">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Testing Information</h5>
        <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Container</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Container" id="Container"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil" id="WetSoil"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil + Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoilTare" id="DrySoilTare"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Tare" id="Tare"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Dry Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoil" id="DrySoil" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Washed (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Washed" id="Washed"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wash Pan (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WashPan" id="WashPan" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
        <!-- End Bordered Table -->
        
        <h5 class="card-title">Reactivity Test Method FM13-007</h5>
        <!-- Bordered Table -->
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row">Total Sample Weight (g):</th>
              <td><input type="text" style="border: none;" class="form-control" name="TotalWeight" id="TotalWeight"></td>
            </tr>
            <tr>
              <th scope="row">Weight used for the Test (g):</th>
              <td><input type="text" style="border: none;" class="form-control" name="WeigtTest" id="WeigtTest"></td>
            </tr>
            <tr>
              <th scope="row">A Particles Reactive #:</th>
              <td><input type="text" style="border: none;" class="form-control" name="Particles1" id="Particles1"></td>
            </tr>
            <tr>
              <th scope="row">B Particles Reactive #:</th>
              <td><input type="text" style="border: none;" class="form-control" name="Particles2" id="Particles2"></td>
            </tr>
            <tr>
              <th scope="row">C Particles Reactive #:</th>
              <td><input type="text" style="border: none;" class="form-control" name="Particles3" id="Particles3"></td>
            </tr>
            <tr>
              <th scope="row">Weight Mat. Ret. No. 4 (If Applicable)</th>
              <td><input type="text" style="border: none;" class="form-control" name="WeightNo4" id="WeightNo4"></td>
            </tr>
            <tr>
              <th scope="row">Wt Reactive Part. Ret. No.4 (If Applicable)</th>
              <td><input type="text" style="border: none;" class="form-control" name="WeightReactiveNo4" id="WeightReactiveNo4"></td>
            </tr>
            <tr>
              <th scope="row">Percent Reactive Particles (If Applicable)</th>
              <td><input type="text" style="border: none;" class="form-control" name="PercentReactive" id="PercentReactive"></td>
            </tr>
            <tr>
              <th scope="row">Average Particles Reactive:</th>
              <td><input type="text" style="border: none;" class="form-control" name="AvgParticles" id="AvgParticles" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Reaction Strength Result:</th>
              <td><input type="text" style="border: none;" class="form-control" name="ReactionResult" id="ReactionResult" readonly tabindex="-1"></td>
            </tr>
          </tbody>
        </table>
        <!-- End Bordered Table -->

        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row">Acid Reactivity Test Result</th>
              <td><input type="text" style="border: none;" class="form-control" name="AcidResult" id="AcidResult" readonly tabindex="-1"></td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  </div>

    <div class="col-lg-7">
      
      <div class="card">
              <div class="card-body">
                <h5 class="card-title">Grain Size Distribution</h5>
                <!-- Bordered Table -->
                <table class="table table-bordered" oninput="CoarsethanAgg()">
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
                      array("3/8\"", "9.5", "WtRet10", "Ret10", "CumRet10", "Pass10", "Specs10"),
                      array("No. 4", "4.75", "WtRet11", "Ret11", "CumRet11", "Pass11", "Specs11"),
                      array("No. 10", "2", "WtRet12", "Ret12", "CumRet12", "Pass12", "Specs12"),
                      array("No. 200", "0.075", "WtRet13", "Ret13", "CumRet13", "Pass13", "Specs13"),
                      // Puedes agregar más filas según sea necesario
                    );

                    foreach ($datos as $fila) {
                      echo '<tr>';
                      foreach ($fila as $index => $valor) {
                          if ($index < 2) {
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
                      <th scope="row" colspan="2">Pan</th>
                      <td><input type="text" style="border: none;" class="form-control" name="PanWtRen" id="PanWtRen"></td>
                      <td><input type="text" style="border: none;" class="form-control" name="PanRet" id="PanRet" readonly tabindex="-1"></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <th scope="row" colspan="2">Total Pan</th>
                      <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" readonly tabindex="-1"></td>
                      <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" readonly tabindex="-1"></td>
                      <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" readonly tabindex="-1"></td>
                      <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" readonly tabindex="-1"></td>
                      <td></td>
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
        <h5 class="card-title"></h5>
        
        <!-- Grain Size Coarse Than Aggregate -->
        <div id="GrainSizeCoarseThanAggregate" style="min-height: 400px;" class="echart"></div>
        <!-- End Grain Size Coarse Than Aggregate -->
    
      </div>
    </div>
          
    </div>

    <div class="col-lg-4">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Summary Grain Size Distribution Parameter</h5>
        <!-- Bordered Table -->
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
        <!-- End Bordered Table -->
      </div>
    </div>
  </div>
    
    <div class="col-lg-2">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="grain-size-coarsethan">Save Essay</button>
          <button type="button" class="btn btn-primary disabled">Search Moisture</button>
        </div>

      </div>
    </div>
  
  </div>

  </form>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Grain-Size.js"></script>
<script src="../libs/graph/Grain-Size-Coarsethan.js"></script>
<?php include_once('../components/footer.php');  ?>