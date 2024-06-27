<?php
  $page_title = 'Unixial Compresive Strenght';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Unixial Compresive Strenght</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">UCS</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section">
    <div class="row">

    <form class="row" action="../database/unixial-compressive.php" method="post" enctype="multipart/form-data">

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
                  <option value="ASTM-D7012">ASTM-D7012</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="TestDevice" class="form-label">Test Devices</label>
                <input type="text" class="form-control" id="TestDevice" name="TestDevice" />
              </div>
              <div class="col-md-6">
                <label for="ExEquip" class="form-label">Extraction Equipment:</label>
                <input type="text" class="form-control" id="ExEquip" name="ExEquip" />
              </div>
              <div class="col-md-6">
                <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
                <input type="text" class="form-control" id="CutterEquip" name="CutterEquip" />
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

      <div class="col-lg-8" oninput="UCS()">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Testing Information</h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row" colspan="5">Compressive Strenght of Intact Rock Core Specimens</th>
                </tr>
                <tr>
                  <th scope="row">Dimension D (cm)</th>
                  <th scope="row">Dimension H (cm)</th>
                  <th scope="row">Relation h/D</th>
                  <th scope="row">Area (m²)</th>
                  <th scope="row">Volume (m³)</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionD" name="DimensionD" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="DimensionH" name="DimensionH" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="RelationHD" name="RelationHD" readonly tabindex="-1" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="AreaM2" name="AreaM2" readonly tabindex="-1"  /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="VolM3" name="VolM3" readonly tabindex="-1"  /></td>
                </tr>
              </tbody>
            </table>
            <!-- End Bordered Table -->

            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Weight of the Core (Kg)</th>
                  <th scope="row">Unit Weight of the Core (Kg/m³)</th>
                  <th scope="row">Failure Load (KN)</th>
                  <th scope="row">Test Timing (s)</th>
                  <th scope="row">Load Proportion (Mpa/s)</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="WeightKg" name="WeightKg" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="UnitWeigKgm3" name="UnitWeigKgm3" readonly tabindex="-1" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="FailLoadKn" name="FailLoadKn" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="TestTimingS" name="TestTimingS" /></td>
                  <td><input type="text" style="border: none;" class="form-control" id="LoadpMpas" name="LoadpMpas" /></td>
                </tr>
              </tbody>
            </table>

            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th scope="row">Uniaxial Compressive Strenght (Mpa)</th>
                  <th scope="row">Failure Type</th>
                </tr>
                <tr>
                  <td><input type="text" style="border: none;" class="form-control" id="UCSMpa" name="UCSMpa" /></td>
                  <td>
                    <select id="FailureType" class="form-select" name="FailureType">
                      <option selected>Choose...</option>
                      <option value="Simple extension">Simple extension</option>
                      <option value="Simple shear">Simple shear</option>
                      <option value="Multiple extension">Multiple extension</option>
                      <option value="Multiple fracturing">Multiple fracturing</option>
                      <option value="Multiple shear">Multiple shear</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
      <div class="card">
          <div class="card-body">
            <h5 class="card-title">Actions</h5>
            <!-- Actions Buttons -->
            <div class="d-grid gap-2 mt-3">
              <button type="submit" name="unixial-compressive" class="btn btn-success">Save Essay</button>
            </div>
          </div>
        </div>
      </div>

        <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Graphic Failure Load versus Time</h5>
            <input class="form-control" type="file" name="Graphic" onchange="showImage(this, 'graphic')" />
            <div id="imageContainerGraphic" class="image-container mt-3"></div>
          </div>
        </div>
        </div>

        <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen Before Test</h5>
            <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
            <div id="imageContainerBefore" class="image-container mt-3"></div>
          </div>
        </div>
        </div>

        <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Specimen After Test</h5>
            <input class="form-control" type="file" name="SpecimenAfter" onchange="showImage(this, 'after')" />
            <div id="imageContainerAfter" class="image-container mt-3"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

</main>
<!-- End #main -->

<script src="../js/unixial-compressive.js"></script>
<?php include_once('../components/footer.php');  ?>