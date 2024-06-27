<?php
  $page_title = 'Moisture Constant Mass';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('moisture_constant_mass', (int)$_GET['id']);
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Moisture Constant Mass</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Moisture Constant Mass</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <form class="row" action="../database/moisture-content.php?id=<?php echo $Search['id']; ?>" method="post">

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
                <option <?php if ($Search['Standard'] == 'ASTM-D2216') echo 'selected'; ?>>ASTM-D2216</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Method']); ?>">
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
          </div><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-8">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="MoistureConstantMass()">
                <tbody>
                  <tr>
                    <th scope="row">Tare Name</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TareName" id="TareName" value="<?php echo ($Search['Tare_Name']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Oven Temperature (°C)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="OvenTemp" id="OvenTemp" value="<?php echo ($Search['Temperature']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil1" id="WetSoil" value="<?php echo ($Search['Tare_Plus_Wet_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 1</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil2" id="WetSoil1" value="<?php echo ($Search['Tare_Plus_Wet_Soil_1']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 2</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil3" id="WetSoil2" value="<?php echo ($Search['Tare_Plus_Wet_Soil_2']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 3</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil4" id="WetSoil3" value="<?php echo ($Search['Tare_Plus_Wet_Soil_3']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 4</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil5" id="WetSoil4" value="<?php echo ($Search['Tare_Plus_Wet_Soil_4']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare Plus Wet Soil (g) 5</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WetSoil6" id="WetSoil5" value="<?php echo ($Search['Tare_Plus_Wet_Soil_5']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Water, Ww (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Water" id="Water" readonly tabindex="-1" value="<?php echo ($Search['Water_Ww']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Tare (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Tare" id="Tare" value="<?php echo ($Search['Tare']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Dry Soil, Ws (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs" id="DrySoilWs" readonly tabindex="-1" value="<?php echo ($Search['Dry_Soil']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Moisture" id="Moisture" readonly tabindex="-1" value="<?php echo ($Search['Moisture_Content_Porce']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
    </div>

    
    <div class="col-lg-3">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="update-mc-constant-mass">Update Essay</button>
          <a href="../pdf/mc-constant-mass.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
        </div>

        <div class="btn-group mt-2" role="group">
          <button type="submit" class="btn btn-primary" name="repeat-mc-constant-mass">Repeat</button>
          <button type="submit" class="btn btn-primary" name="reviewed-mc-constant-mass">Reviewed</button>
        </div>

      </div>
    </div>
  
  </div>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Moisture-Content.js"></script>
<?php include_once('../components/footer.php');  ?>