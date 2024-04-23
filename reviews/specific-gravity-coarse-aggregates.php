<?php
  $page_title = 'Specific Gravity Coarse';
  $review = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('specific_gravity_coarse', (int)$_GET['id']);
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Specific Gravity Coarse Aggregate</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Specific Gravity</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <form class="row" action="../database/specific-gravity.php?id=<?php echo $Search['id']; ?>" method="post">

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
                <option <?php if ($Search['Standard'] == 'ASTM-C127') echo 'selected'; ?>>ASTM-C127</option>
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
          </div><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-7">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="SGCOARSE()">
                <thead>
                  <tr>
                    <th scope="col" colspan="2">Oven Dry (A)</th>
                    <th scope="col" colspan="2">Saturated Surface Dry (B)</th>
                    <th scope="col" colspan="2">Sample immersed (C)</th>
                  </tr>
                  <tr>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                    <th scope="row">Size (inch)</th>
                    <th scope="row">Wt (gr)</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                $datos = array("3.5", "3", "2.5", "2", "1.5", "1", "3/4", "1/2", "3/8", "No. 4", "Total");

                for ($i = 1; $i < count($datos); $i++) {
                  echo "<tr>";
                  
                  echo "<th scope='row'>$datos[$i]</th>";

                  echo "<td><input type='text' style='border: none;' class='form-control' name='OvenDry{$i}' id='OvenDry{$i}' value='" . $Search["Oven_Dry_{$i}"] . "'></td>";

                  echo "<th scope='row'>$datos[$i]</th>";

                  echo "<td><input type='text' style='border: none;' class='form-control' name='SurfaceDry{$i}' id='SurfaceDry{$i}' value='" . $Search["Surface_Dry_{$i}"] . "'></td>";

                  echo "<th scope='row'>$datos[$i]</th>";

                  echo "<td><input type='text' style='border: none;' class='form-control' name='SampImmers{$i}' id='SampImmers{$i}' value='" . $Search["Samp_Immers_{$i}"] . "'></td>";
                  
                  echo "</tr>";
                }
                ?>


                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
    </div>

    
    <div class="col-lg-5">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Results</h5>
        <!-- Bordered Table -->
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th scope="col">Specific Gravity (OD) (A/(B-C))=</th>
              <td><input type="text" style="border: none;" class="form-control" name="SpecificGravityOD" id="SpecificGravityOD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_OD']); ?>"></td>
              <th scope="col">Specific Gravity (SSD) (B/(B-C))=</th>
              <td><input type="text" style="border: none;" class="form-control" name="SpecificGravitySSD" id="SpecificGravitySSD" readonly tabindex="-1" value="<?php echo ($Search['Specific_Gravity_SSD']); ?>"></td>
            </tr>
            <tr>
              <th scope="col">Apparent Specific Gravity (A/(A-C))=</th>
              <td><input type="text" style="border: none;" class="form-control" name="ApparentSpecificGravity" id="ApparentSpecificGravity" readonly tabindex="-1" value="<?php echo ($Search['Apparent_Specific_Gravity']); ?>"></td>
              <th scope="col">Percent of Absortion ((B-A)/A)*100=</th>
              <td><input type="text" style="border: none;" class="form-control" name="PercentAbsortion" id="PercentAbsortion" readonly tabindex="-1" value="<?php echo ($Search['Percent_Absortion']); ?>"></td>
            </tr>
          </tbody>
        </table>
        <!-- End Bordered Table -->
      </div>
    </div>

    <div class="col-lg-6">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="update-sg-coarse">Update Essay</button>
          <a href="../pdf/sg-coarse.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
        </div>

        <div class="btn-group mt-2" role="group">
          <button type="submit" class="btn btn-primary" name="repeat-sg-coarse">Repeat</button>
          <button type="submit" class="btn btn-primary" name="reviewed-sg-coarse">Reviewed</button>
        </div>
      
      </div>
    </div>
  
  </div>

  </div>

  </form>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Specific-Gravity.js"></script>
<?php include_once('../components/footer.php');  ?>