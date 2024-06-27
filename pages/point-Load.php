<?php
  $page_title = 'Point Load';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Point Load</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Point Load</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section">
    <div class="row" oninput="PLT()">

    <form class="row" action="../database/point-load.php" method="post" enctype="multipart/form-data">

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
                  <option value="ASTM-D5731">ASTM-D5731</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="MethodTest" class="form-label">Test Method</label>
                <select id="MethodTest" class="form-select" name="TestMethod">
                  <option selected>Choose...</option>
                  <option value="Diametral">Diametral</option>
                  <option value="Axial">Axial</option>
                  <option value="Block">Block</option>
                  <option value="Irregular Lump">Irregular Lump</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="ExEquip" class="form-label">Extraction Equipment:</label>
                <input type="text" class="form-control" id="ExEquip" name="ExEquip" />
              </div>
              <div class="col-md-6">
                <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
                <input type="text" class="form-control" id="CutterEquip" name="CuttEquip" />
              </div>
              <div class="col-md-6">
                <label for="Technician" class="form-label">Technician</label>
                <input type="text" class="form-control" id="Technician" name="Technician" />
              </div>
              <div class="col-md-6">
                <label for="DateTesting" class="form-label">Date of Testing</label>
                <input type="date" class="form-control" id="DateTesting" name="DateTesting" />
              </div>
              <div class="col-12">
                <label for="Comments" class="form-label">Comments</label>
                <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"></textarea>
              </div>
            </div>
            <!-- End Multi Columns Form -->
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Point Load Test Device Values</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Effective Area of Jack Piston (m²)</th>
                  <td><input type="text" style="border: none;" class="form-control" id="JackPiston" value="0.0014354" name="JackPiston" /></td>
                </tr>
                <tr>
                  <th scope="row">k₁ value (assumed value to correlate Is50 to UCS):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="K1assumed" value="15" name="K1assumed" /></td>
                </tr>
                <tr>
                  <th scope="row">k₂ value (assumed):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="K2assumed" value="21" name="K2assumed" /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Testing Information</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row" colspan="3">Test Type (A, B, C, D):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="TypeABCD" name="TypeABCD" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">Dimension L (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionL" name="DimensionL" /></td>
                  <th scope="row">Dimension D or W (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionD" name="DimensionD" /></td>
                </tr>
                <tr>
                  <th scope="row">Plattens Separation (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="PlattensSeparation" name="PlattensSeparation" /></td>
                  <th scope="row">Load Direction:</th>
                  <td>
                    <select id="LoadDirection" class="form-select" name="LoadDirection">
                      <option selected>Choose...</option>
                      <option value="Perpendicular">⊥</option>
                      <option value="Parallel">//</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Gauge Reading (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="GaugeReading" name="GaugeReading" /></td>
                  <th scope="row">Failure Load (MN):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="FailureLoad" name="FailureLoad" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">De (mm):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Demm" name="Demm" readonly tabindex="-1" /></td>
                  <th scope="row">Is (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="IsMpa" name="IsMpa" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">F:</th>
                  <td><input type="text" style="border: none;" class="form-control" id="F" name="F" readonly tabindex="-1" /></td>
                  <th scope="row">Is 50:</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Is50" name="Is50" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row">UCS From k1 (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSK1Mpa" name="UCSK1Mpa" readonly tabindex="-1" /></td>
                  <th scope="row">UCS From k2 (Mpa):</th>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSK2Mpa" name="UCSK2Mpa" readonly tabindex="-1" /></td>
                </tr>
                <tr>
                  <th scope="row" colspan="3">Strenght Classification :</th>
                  <td><input type="text" style="border: none;" class="form-control" id="Classification" name="Classification" readonly tabindex="-1" /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Actions</h5>
            <!-- Actions Buttons -->
            <div class="d-grid gap-2 mt-3">
              <button type="submit" name="point-load" class="btn btn-success">Save Essay</button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen Before Test</h5>
            <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
            <div id="imageContainerBefore" class="image-container mt-3">
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen After Test</h5>
            <input class="form-control" type="file" name="SpecimenAfter" onchange="showImage(this, 'after')" />
            <div id="imageContainerAfter" class="image-container mt-3"></div>
          </div>
        </div>
      </div>

      </form>

    </div>
  </section>

</main>
<!-- End #main -->

<script src="../js/Point-Load.js"></script>
<?php include_once('../components/footer.php');  ?>