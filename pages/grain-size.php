<?php
  $page_title = 'Grain Size General';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['grain-size-general'])) {
        include('../database/grain-size-general.php');
    } 
  }
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Grain Size</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Grain Size</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row" oninput="GrainSize()">

  <form class="row" action="grain-size.php" method="post">

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
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select" name="Standard">
                <option selected>Choose...</option>
                <option value="ASTM-D6913">ASTM-D6913</option>
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

    <div class="col-lg-7">
      
      <div class="card">
              <div class="card-body">
                <h5 class="card-title">Grain Size Distribution</h5>
                <!-- Bordered Table -->
                <table class="table table-bordered">
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
                      array("8\"", "200", "WtRet1", "Ret1", "CumRet1", "Pass1", "Specs8"),
                      array("6\"", "152.4", "WtRet2", "Ret2", "CumRet2", "Pass2", "Specs6"),
                      array("5\"", "127", "WtRet3", "Ret3", "CumRet3", "Pass3", "Specs5"),
                      array("4\"", "101.6", "WtRet4", "Ret4", "CumRet4", "Pass4", "Specs4"),
                      array("3.5\"", "88.9", "WtRet5", "Ret5", "CumRet5", "Pass5", "Specs3p5"),
                      array("3\"", "76.2", "WtRet6", "Ret6", "CumRet6", "Pass6", "Specs3"),
                      array("2.5\"", "63.5", "WtRet7", "Ret7", "CumRet7", "Pass7", "Specs2p5"),
                      array("2\"", "50.8", "WtRet8", "Ret8", "CumRet8", "Pass8", "Specs2"),
                      array("1.5\"", "38.1", "WtRet9", "Ret9", "CumRet9", "Pass9", "Specs1p5"),
                      array("1\"", "25", "WtRet10", "Ret10", "CumRet10", "Pass10", "Specs1"),
                      array("3/4\"", "19", "WtRet11", "Ret11", "CumRet11", "Pass11", "Specs3p4"),
                      array("1/2\"", "12.5", "WtRet12", "Ret12", "CumRet12", "Pass12", "Specs1p2"),
                      array("3/8\"", "9.5", "WtRet13", "Ret13", "CumRet13", "Pass13", "Specs3p8"),
                      array("No. 4", "4.75", "WtRet14", "Ret14", "CumRet14", "Pass14", "SpecsNo4"),
                      array("No. 10", "2", "WtRet15", "Ret15", "CumRet15", "Pass15", "SpecsNo10"),
                      array("No. 16", "1.18", "WtRet16", "Ret16", "CumRet16", "Pass16", "SpecsNo16"),
                      array("No. 20", "0.85", "WtRet17", "Ret17", "CumRet17", "Pass17", "SpecsNo20"),
                      array("No. 50", "0.3", "WtRet18", "Ret18", "CumRet18", "Pass18", "SpecsNo50"),
                      array("No. 60", "0.25", "WtRet19", "Ret19", "CumRet19", "Pass19", "SpecsNo60"),
                      array("No. 100", "0.15", "WtRet20", "Ret20", "CumRet20", "Pass20", "SpecsNo100"),
                      array("No. 140", "0.106", "WtRet21", "Ret21", "CumRet21", "Pass21", "SpecsNo140"),
                      array("No. 200", "0.075", "WtRet22", "Ret22", "CumRet22", "Pass22", "SpecsNo200"),
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

    <div class="col-lg-9">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
        
        <!-- Multi Point Liquid Limit Plot Chart -->
        <div id="GrainSizeGeneral" style="min-height: 400px;" class="echart"></div>
        <!-- End Multi Point Liquid Limit Plot Chart -->
    
      </div>
    </div>
          
    </div>
    
    <div class="col-lg-3">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="grain-size-general">Save Essay</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true">GS Options</button>
        </div>

      </div>
    </div>
  
  </div>

  </form>

  </div>
</section>

</main><!-- End #main -->


<div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hey! select an option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <li>
                    <a href="grain-size-fine-agg.php">
                        <span>Grain Size Fine Aggregate</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-coarse-agg.php">
                        <span>Grain Size Coarse Aggregate</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-coarsethan-agg.php">
                        <span>Grain Size Coarse Than Aggregate</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-coarse-filter.php">
                        <span>Grain Size Coarse Filter</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-fine-filter.php">
                        <span>Grain Size Fine Filter</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-lpf.php">
                        <span>Grain Size Low Permeability Fill</span>
                    </a>
                </li>
                <li>
                    <a href="grain-size-upstream-transition-fill.php">
                        <span>Grain Size Upstream Transition Fill</span>
                    </a>
                </li>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/Grain-Size.js"></script>
<script src="../libs/graph/Grain-Size-General.js"></script>
<?php include_once('../components/footer.php');  ?>