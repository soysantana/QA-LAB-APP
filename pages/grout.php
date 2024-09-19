<?php
  $page_title = 'Grout Specimens';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['grout'])) {
        include('../database/grout.php');
    } 
  }
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Grout Specimens</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Grout</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row" oninput="GROUT()">

  <form class="row" action="grout.php" method="post" enctype="multipart/form-data">

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
              <select id="Standard" class="form-select">
                <option selected>Choose...</option>
                <option value="ASTM-C39">ASTM-C39</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestDevice" class="form-label">Test Devices</label>
              <input type="text" class="form-control" id="TestDevice">
              </select>
            </div>
            <div class="col-md-6">
              <label for="ExEquip" class="form-label">Extraction Equipment:</label>
              <input type="text" class="form-control" id="ExEquip">
            </div>
            <div class="col-md-6">
              <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
              <input type="text" class="form-control" id="CutterEquip">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" style="height: 100px;"></textarea>
            </div>
          </div><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-12">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="row">No.</th>
                    <th scope="row">Diameter</th>
                    <th scope="row">High</th>
                    <th scope="row">Length</th>
                    <th scope="row">Area (m²)</th>
                    <th scope="row">Volumen (m³)</th>
                    <th scope="row">Weight of the Cylinder (Kg)</th>
                    <th scope="row">Age (days)</th>
                    <th scope="row">Unit Weight (Kg/m³)</th>
                    <th scope="row">Failure Load (KN)</th>
                    <th scope="row">Strenght (Mpa)</th>
                    <th scope="row">Average Strenght (Mpa)</th>
                    <th scope="row">Type of Fracture</th>
                    <th scope="row">Observations</th>
                  </tr>
                </thead>
                <tbody class="table">
                  <?php
                  $numFilas = 5; // Número de filas que deseas generar
                  
                  for ($i = 1; $i <= $numFilas; $i++) {
                    echo '<tr>';
                    echo '<th scope="row">' . $i . '</th>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="DiameterNo' . $i . '" name="DiameterNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="HighNo' . $i . '" name="HighNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="LengthNo' . $i . '" name="LengthNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AreaNo' . $i . '" name="AreaNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="VolNo' . $i . '" name="VolNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="WeigCyNo' . $i . '" name="WeigCyNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AgeDaysNo' . $i . '" name="AgeDaysNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="UnitWeigNo' . $i . '" name="UnitWeigNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="FalLoadNo' . $i . '" name="FalLoadNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="StrenghtNo' . $i . '" name="StrenghtNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AverageNo' . $i . '" name="AverageNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="TypeFractureNo' . $i . '" name="TypeFractureNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="ObservationNo' . $i . '" name="ObservationNo' . $i . '"></td>';
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

    </div>
    
    
    <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" name="grout" class="btn btn-success">Save Essay</button>
        </div>
  
      </div>
    </div>
  </div>

  <div></div>

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

  </form>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Grout.js"></script>
<?php include_once('../components/footer.php');  ?>