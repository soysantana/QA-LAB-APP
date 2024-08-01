<?php
  $page_title = 'Soundness Test';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Soundness Test</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Soundness Test</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->
  <section class="section">
    <div class="row">
      <form class="row" action="../database/atterberg-limit.php" method="post">
        <div id="product_info"></div>

        <div class="col-md-4"><?php echo display_msg($msg); ?></div>

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
                    <option value="ASTM-D4318">ASTM-D4318</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" />
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" />
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px"></textarea>
                </div>
              </div>
              <!-- End Multi Columns Form -->
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grain Size Distribution</h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>Wt Dry Soil, g</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                  </tr>
                  <tr>
                    <th>Wt Washed, g</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                  </tr>
                </tbody>
              </table>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Screen</th>
                    <th scope="col">(mm)</th>
                    <th scope="col">Wt. Ret</th>
                    <th scope="col">% Ret</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">4"</th>
                    <th>100</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3 1/2"</th>
                    <th>90</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLContainer2" id="LLContainer2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLContainer3" id="LLContainer3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3"</th>
                    <th>75</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">2 1/2"</th>
                    <th>63</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">2"</th>
                    <th>50</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1 1/2"</th>
                    <th>38</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1"</th>
                    <th>25</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3/4"</th>
                    <th>19</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1/2"</th>
                    <th>12.5</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3/8"</th>
                    <th>9.5</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 4</th>
                    <th>4.75</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 8</th>
                    <th>2.36</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 16</th>
                    <th>1.18</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 30</th>
                    <th>0.6</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 50</th>
                    <th>0.3</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 100</th>
                    <th>0.15</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Pan</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Total Pan</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Total</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
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
              <h5 class="card-title">Grain Size Distribution for Soundness</h5>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Screen</th>
                    <th scope="col">(mm)</th>
                    <th scope="col">Wt. Ret</th>
                    <th scope="col">% Ret</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="4">Coarse Aggregate</td>
                  </tr>
                  <tr>
                    <th scope="row">4"</th>
                    <th>100</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3 1/2"</th>
                    <th>90</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLContainer2" id="LLContainer2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLContainer3" id="LLContainer3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3"</th>
                    <th>75</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">2 1/2"</th>
                    <th>63</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">2"</th>
                    <th>50</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1 1/2"</th>
                    <th>38</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1"</th>
                    <th>25</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3/4"</th>
                    <th>19</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">1/2"</th>
                    <th>12.5</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">3/8"</th>
                    <th>9.5</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 4</th>
                    <th>4.75</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Total</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                </tbody>
              </table>


              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td colspan="4">Fine Aggregate</td>
                  </tr>
                  <tr>
                    <th scope="row">No. 4</th>
                    <th>4.75</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 8</th>
                    <th>2.36</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 16</th>
                    <th>1.18</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 30</th>
                    <th>0.6</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 50</th>
                    <th>0.3</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row">No. 100</th>
                    <th>0.15</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                  <tr>
                    <th scope="row" colspan="2">Total</th>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil2" id="LLWetSoil2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="LLWetSoil3" id="LLWetSoil3" /></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Information</h5>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Cycle</th>
                    <th>Start Date</th>
                    <th>Room Temperature (°C)</th>
                    <th>Solution Temperature (°C)</th>
                    <th>Specific Gravity of the Solution</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>1</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>2</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>3</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>4</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>5</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th colspan="2">Solution Used</th>
                    <th colspan="3">Sodium Sulfate</th>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Qualitative Examination of Coarse Sizes</h5>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th rowspan="3">Sieve Size</th>
                    <th colspan="8">Particles Exhibiting Distress</th>
                    <th rowspan="3">Total No. of Particles Before Test</th>
                  </tr>
                  <tr>
                    <th colspan="2">Splitting</th>
                    <th colspan="2">Crumbling</th>
                    <th colspan="2">Cracking</th>
                    <th colspan="2">Flaking</th>
                  </tr>
                  <tr>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                    <th>No.</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>37.5mm(11⁄2 in) to 19.0 mm (3/4 in)</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>63 mm (2 1⁄2 in) to 37.5 mm (1 1⁄2 in)</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>75 mm (3 in) to 63 mm (2 1⁄2 in)</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>91 mm (3 1⁄2 in) to 75 mm (3 in)</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                  <tr>
                    <th>100 mm (4 in) to 90 mm (3 1⁄2 in)</th>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows2" id="Blows2" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                    <td><input type="text" style="border: none" class="form-control" name="Blows3" id="Blows3" /></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Results</h5>
              <!-- Soundness Test of Fine Aggregate -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td>Screen</td>
                    <td>Weight of Sample for each size (required)</td>
                    <td>Star Weight Ret</td>
                    <td>Combined Fractions</td>
                    <td>Designated Sieve After Test</td>
                    <td>Final Weight Ret</td>
                    <td>Percentage Passing Designated Sieve After Test</td>
                    <td>Weigthed Percentage of Mass Loss</td>
                  </tr>
                  <tr>
                    <th colspan="8">Soundness Test of Fine Aggregate</th>
                  </tr>
                  <tr>
                    <td>Minus 150 µm (No. 100)</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>300 µm (No. 50) to No. 100</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 100</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>600 µm (No. 30) to No. 50</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 50</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>1.18 mm (No. 16) to No. 30</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 30</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>2.36 mm (No. 8) to No. 16</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 16</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>4.75 mm (No. 4) to No. 8</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 8</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>9.5mm(3⁄8 in.) to No. 4</td>
                    <td>100g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>No. 4</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td colspan="2">Totals</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>---</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                </tbody>
              </table>
              <!-- Soundness Test of Fine Aggregate -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th colspan="8">Soundness Test of Coarse Aggregate</th>
                  </tr>
                  <tr>
                    <td>9.5mm(3⁄8 in.) to 4.75 mm (No. 4)</td>
                    <td>(300+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>4.0 mm (No. 5)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>12.5 mm (1⁄2 in) to 9.5 mm (3⁄8 in)</td>
                    <td>(330+-5)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td rowspan="2">19.0mm(3⁄4 in) to 9.5mm(3⁄8 in)</td>
                    <td rowspan="2">8.0 mm (5⁄16 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>19.0mm(3⁄4 in) to 12.5 mm (1⁄2 in)</td>
                    <td>(670+-10)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>25 mm (1 in) to 19.0 mm (3⁄4 in)</td>
                    <td>(500+-30)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td rowspan="2">37.5mm(1 1⁄2 in) to 19.0mm(3⁄4 in)</td>
                    <td rowspan="2">16.0 mm (5⁄8 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>37.5mm(11⁄2 in) to 25.0 mm (1 in.)</td>
                    <td>(1000+-50)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>50 mm (2 in) to 37.5 mm (1 1⁄2 in)</td>
                    <td>(2000+-200)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td rowspan="2">63mm(2 1⁄2 in) to 37.5mm(1 1⁄2 in)</td>
                    <td rowspan="2">31.5 mm (1 1⁄4 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>63 mm (2 1⁄2 in) to 50 mm (2 in)</td>
                    <td>(3000+-300)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>75 mm (3 in) to 63 mm (2 1⁄2 in)</td>
                    <td>(7000+-100)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td rowspan="3"> 100mm(4 in.) to  90mm(2 1⁄2 in.)</td>
                    <td>50 mm (2 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>90 mm (3 1⁄2 in) to 75 mm (3 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>63 mm (2 1⁄2 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td>100 mm (4 in) to 90 mm (3 1⁄2 in)</td>
                    <td>(7000+-1000)g</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>75 mm (3 in)</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                  <tr>
                    <td colspan="2">Totals</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td>---</td>
                    <td>---</td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                    <td></td>
                    <td><input type="text" style="border: none" class="form-control" name="PLPorce" id="PLPorce"/></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Default Table Example -->
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="atterberg-limit">Save Essay</button>
                <button type="button" class="btn btn-primary" onclick="search()"></button>
                <div id="mensaje-container"></div>
              </div>

            </div>
          </div>
        </div>

      </form>
      <!-- End Form -->
    </div>
  </section>
</main>
<!-- End #main -->


<?php include_once('../components/footer.php');  ?>