<?php
  $page_title = 'Pinhole Test';
  require_once('../config/load.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Pinhole'])) {
        include('../database/pinhole-test.php');
    } 
  }
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Pinhole Test</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Pinhole Test</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row" oninput="Pinhole()">

  <form class="row" action="pinhole-test.php" method="post">

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
                <option value="ASTM-D4647">ASTM-D4647</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod" name="TestMethod">
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
                <thead>
                  <tr>
                    <th scope="col">Moisture Content  before test MC(%)</th>
                    <th scope="col"><input type="text" style="border: none;" class="form-control" id="mcBefore" name="mcBefore" readonly tabindex="-1"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">Specific Gravity (Estimated or Measured)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="sgEM" name="sgEM"></td>
                  </tr>
                  <tr>
                    <th scope="row">Max Dry Density (g/cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="maxDryDensity" name="maxDryDensity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Optimum Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="optimumMC" name="optimumMC"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="welSoilMold" name="welSoilMold"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wtMold" name="wtMold"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wtWetSoil" name="wtWetSoil" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Logitud Del Specimen (cm)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="longitudSpecimen" name="longitudSpecimen"></td>
                  </tr>
                  <tr>
                    <th scope="row">Vol Specimen (cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="volSpecimen" name="volSpecimen" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wet Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wetDensity" name="wetDensity" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="dryDensityGCM3" name="dryDensityGCM3" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">% Compaction</th>
                    <td><input type="text" style="border: none;" class="form-control" id="porceCompaction" name="porceCompaction" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content after test, %</th>
                    <td><input type="text" style="border: none;" class="form-control" id="mcAfter" name="mcAfter" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wire Punch Diameter (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="wirePuchDiameter" name="wirePuchDiameter"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
    </div>

    <div class="col-lg-7">

    <div class="row">
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">MC Before</h5>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row">Tare Name</th>
              <td><input type="text" style="border: none;" class="form-control" id="nameBefore" name="nameBefore"></td>
            </tr>
            <tr>
              <th scope="row">Oven Temperature (°C)</th>
              <td><input type="text" style="border: none;" class="form-control" id="tempBefore" name="tempBefore" value="110 °C"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Wet Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="wetSoil1" name="wetSoil1"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Dry Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="drySoil1" name="drySoil1"></td>
            </tr>
            <tr>
              <th scope="row">Water, Ww (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="water1" name="water1" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Tare (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="tare1" name="tare1"></td>
            </tr>
            <tr>
              <th scope="row">Dry Soil, Ws (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="drySoilWs1" name="drySoilWs1" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Moisture Content (%)</th>
              <td><input type="text" style="border: none;" class="form-control" id="mc1" name="mc1" readonly tabindex="-1"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">MC After</h5>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row">Tare Name</th>
              <td><input type="text" style="border: none;" class="form-control" id="nameAfter" name="nameAfter"></td>
            </tr>
            <tr>
              <th scope="row">Oven Temperature (°C)</th>
              <td><input type="text" style="border: none;" class="form-control" id="tempAfter" name="tempAfter" value="110 °C"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Wet Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="wetSoil2" name="wetSoil2"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Dry Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="drySoil2" name="drySoil2"></td>
            </tr>
            <tr>
              <th scope="row">Water, Ww (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="water2" name="water2" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Tare (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="tare2" name="tare2"></td>
            </tr>
            <tr>
              <th scope="row">Dry Soil, Ws (g)</th>
              <td><input type="text" style="border: none;" class="form-control" id="drySoilWs2" name="drySoilWs2" readonly tabindex="-1"></td>
            </tr>
            <tr>
              <th scope="row">Moisture Content (%)</th>
              <td><input type="text" style="border: none;" class="form-control" id="mc2" name="mc2" readonly tabindex="-1"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
        
        <!-- Multi Point Liquid Limit Plot Chart -->
        <div id="Pinhole" style="min-height: 430px;" class="echart"></div>
        <!-- End Multi Point Liquid Limit Plot Chart -->
    
      </div>
    </div>

    </div>
    
    <div class="col-lg-10">

    <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <th scope="row" colspan="9">Flow</th>
            <tr>
              <th scope="col">Head</th>
              <th scope="col">mL</th>
              <th scope="col">Seg</th>
              <th scope="col">Flow Rate</th>
              <th scope="col">From Side</th>
              <th scope="col">From Top</th>
              <th scope="col">Observation</th>
              <th scope="col">Hole Size After Test mm</th>
              <th scope="col">Dispersive Classification1</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row" rowspan="12">50</th>
              <td><input type="text" style="border: none;" class="form-control" id="ML_1" name="ML_1"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_1" name="Seg_1"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_1" name="Flow_Rate_1"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_1" name="From_Side_1"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_1" name="From_Top_1"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_1" name="Observation_1"></td>
              <td rowspan="22"><input type="text" style="border: none;" class="form-control" id="Hole_Size_After" name="Hole_Size_After"></th>
              <td rowspan="22"><input type="text" style="border: none;" class="form-control" id="Dispersive_Classification" name="Dispersive_Classification"></th>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_2" name="ML_2"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_2" name="Seg_2"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_2" name="Flow_Rate_2"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_2" name="From_Side_2"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_2" name="From_Top_2"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_2" name="Observation_2"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_3" name="ML_3"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_3" name="Seg_3"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_3" name="Flow_Rate_3"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_3" name="From_Side_3"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_3" name="From_Top_3"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_3" name="Observation_3"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_4" name="ML_4"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_4" name="Seg_4"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_4" name="Flow_Rate_4"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_4" name="From_Side_4"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_4" name="From_Top_4"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_4" name="Observation_4"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_5" name="ML_5"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_5" name="Seg_5"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_5" name="Flow_Rate_5"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_5" name="From_Side_5"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_5" name="From_Top_5"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_5" name="Observation_5"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_6" name="ML_6"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_6" name="Seg_6"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_6" name="Flow_Rate_6"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_6" name="From_Side_6"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_6" name="From_Top_6"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_6" name="Observation_6"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_7" name="ML_7"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_7" name="Seg_7"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_7" name="Flow_Rate_7"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_7" name="From_Side_7"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_7" name="From_Top_7"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_7" name="Observation_7"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_8" name="ML_8"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_8" name="Seg_8"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_8" name="Flow_Rate_8"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_8" name="From_Side_8"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_8" name="From_Top_8"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_8" name="Observation_8"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_9" name="ML_9"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_9" name="Seg_9"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_9" name="Flow_Rate_9"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_9" name="From_Side_9"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_9" name="From_Top_9"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_9" name="Observation_9"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_10" name="ML_10"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_10" name="Seg_10"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_10" name="Flow_Rate_10"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_10" name="From_Side_10"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_10" name="From_Top_10"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_10" name="Observation_10"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_11" name="ML_11"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_11" name="Seg_11"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_11" name="Flow_Rate_11"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_11" name="From_Side_11"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_11" name="From_Top_11"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_11" name="Observation_11"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_12" name="ML_12"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_12" name="Seg_12"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_12" name="Flow_Rate_12"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_12" name="From_Side_12"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_12" name="From_Top_12"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_12" name="Observation_12"></td>
            </tr>
            <tr>
              <th scope="row" rowspan="5">180</th>
              <td><input type="text" style="border: none;" class="form-control" id="ML_13" name="ML_13"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_13" name="Seg_13"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_13" name="Flow_Rate_13"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_13" name="From_Side_13"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_13" name="From_Top_13"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_13" name="Observation_13"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_14" name="ML_14"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_14" name="Seg_14"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_14" name="Flow_Rate_14"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_14" name="From_Side_14"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_14" name="From_Top_14"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_14" name="Observation_14"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_15" name="ML_15"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_15" name="Seg_15"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_15" name="Flow_Rate_15"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_15" name="From_Side_15"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_15" name="From_Top_15"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_15" name="Observation_15"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_16" name="ML_16"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_16" name="Seg_16"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_16" name="Flow_Rate_16"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_16" name="From_Side_16"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_16" name="From_Top_16"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_16" name="Observation_16"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_17" name="ML_17"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_17" name="Seg_17"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_17" name="Flow_Rate_17"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_17" name="From_Side_17"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_17" name="From_Top_17"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_17" name="Observation_17"></td>
            </tr>
            <tr>
              <th scope="row" rowspan="5">380</th>
              <td><input type="text" style="border: none;" class="form-control" id="ML_18" name="ML_18"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_18" name="Seg_18"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_18" name="Flow_Rate_18"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_18" name="From_Side_18"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_18" name="From_Top_18"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_18" name="Observation_18"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_19" name="ML_19"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_19" name="Seg_19"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_19" name="Flow_Rate_19"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_19" name="From_Side_19"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_19" name="From_Top_19"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_19" name="Observation_19"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_20" name="ML_20"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_20" name="Seg_20"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_20" name="Flow_Rate_20"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_20" name="From_Side_20"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_20" name="From_Top_20"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_20" name="Observation_20"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_21" name="ML_21"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_21" name="Seg_21"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_21" name="Flow_Rate_21"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_21" name="From_Side_21"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_21" name="From_Top_21"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_21" name="Observation_21"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control" id="ML_22" name="ML_22"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Seg_22" name="Seg_22"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Flow_Rate_22" name="Flow_Rate_22"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Side_22" name="From_Side_22"></td>
              <td><input type="text" style="border: none;" class="form-control" id="From_Top_22" name="From_Top_22"></td>
              <td><input type="text" style="border: none;" class="form-control" id="Observation_22" name="Observation_22"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="Pinhole">Save Essay</button>
        </div>

      </div>
    </div>
  
  </div>

  </form><!-- End Form -->

  </div>

</section>
</main><!-- End #main -->

<script src="../js/Pinhole-Test.js"></script>
<script src="../libs/graph/Pinhole-Test.js"></script>
<?php include_once('../components/footer.php');  ?>