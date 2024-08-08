<?php
  $page_title = 'Los Angeles Abrasion';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Los Angeles Abrasion For Small Size Coarse</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Los Angeles Abrasion</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <form action="../database/los-angeles-abrasion-coarse-filter.php" method="post" class="row">

  <div class="col-md-7">
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
                <option value="ASTM-C535">ASTM-C535</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod" name="TestMethod">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician" name="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting" name="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" name="Comments" style="height: 100px;"></textarea>
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
                    <th scope="row">Selected Grading</th>
                    <td>
                        <select class="form-control" id="SelectGrading" name="SelectGrading">
                            <option selected>Choose...</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Weight of the Spheres (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigSpheres" name="WeigSpheres"></td>
                  </tr>
                  <tr>
                    <th scope="row">Revolutions</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Revolutions" name="Revolutions"></td>
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
              <h5 class="card-title">Results</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered" oninput="laaLarge()">
                <tbody>
                  <tr>
                    <th scope="row">Initial Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="InitWeig" name="InitWeig"></td>
                    <th scope="row">Final Weight (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="FinalWeig" name="FinalWeig"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight Loss (g)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLoss" name="WeigLoss" readonly tabindex="-1"></td>
                    <th scope="row">Weight Loss (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="WeigLossPorce" name="WeigLossPorce" readonly tabindex="-1"></td>
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
          <button type="submit" name="LAA_Coarse_Filter" class="btn btn-success">Save Essay</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true">Launch</button>
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
                    <a href="LAA-Large.php">
                        <span>Los Angeles Abrasion For Large Size Coarse</span>
                    </a>
                </li>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/Los-Angeles-Abrasion.js"></script>
<?php include_once('../components/footer.php');  ?>