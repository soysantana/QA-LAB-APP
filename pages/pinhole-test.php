<?php
  $page_title = 'Pinhole Test';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
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
                <option value="ASTM-D4647">ASTM-D4647</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod">
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
                    <th scope="col">Moisture Content  before test MC(%)</th>
                    <th scope="col"><input type="text" style="border: none;" class="form-control" id=""></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">Specific Gravity (Estimated or Measured)</th>
                    <td><input type="text" style="border: none;" class="form-control" id=""></td>
                  </tr>
                  <tr>
                    <th scope="row">Max Dry Density (g/cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id=""></td>
                  </tr>
                  <tr>
                    <th scope="row">Optimum Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id=""></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil + Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Mold (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id=""></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Wet Soil (gr)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Vol Specimen (cm3)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wet Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Density (g/cm3 )</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">% Compaction</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content after test, %</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wire Punch Diameter (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="" readonly tabindex="-1"></td>
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
        <h5 class="card-title">Trial No. 1</h5>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="row">Tare Name</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Oven Temperature (°C)</th>
              <td><input type="text" style="border: none;" class="form-control" value="110 °C"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Wet Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Dry Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Water, Ww (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Tare (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Dry Soil, Ws (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Moisture Content (%)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Trial No. 2</h5>
        <table class="table table-bordered">
          <tbody>
            <tr>
            <tr>
              <th scope="row">Tare Name</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Oven Temperature (°C)</th>
              <td><input type="text" style="border: none;" class="form-control" value="110 °C"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Wet Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Tare Plus Dry Soil (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Water, Ww (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Tare (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Dry Soil, Ws (g)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row">Moisture Content (%)</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
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
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td rowspan="22"><input type="text" style="border: none;" class="form-control"></th>
              <td rowspan="22"><input type="text" style="border: none;" class="form-control"></th>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row" rowspan="5">180</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <th scope="row" rowspan="5">380</th>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
            </tr>
            <tr>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
              <td><input type="text" style="border: none;" class="form-control"></td>
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
          <div id="mensaje-container"></div>
        </div>

      </div>
    </div>
  
  </div>

  </form><!-- End Form -->

  </div>
</section>

</main><!-- End #main -->

<script src="../libs/graph/Pinhole-Test.js"></script>
<?php include_once('../components/footer.php');  ?>