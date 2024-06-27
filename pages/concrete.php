<?php
  $page_title = 'Concrete Specimens';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Concrete Specimens</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Concrete</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <div id="product_info"></div>

    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Trial Information</h5>

          <!-- Multi Columns Form -->
          <form class="row g-3">
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select">
                <option selected>Choose...</option>
                <option>ASTM</option>
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
          </form><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-9">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row" rowspan="2">No.</th>
                    <th scope="row" colspan="2">Dimension (mm)</th>
                    <th scope="row" rowspan="2">Area (m²)</th>
                    <th scope="row" rowspan="2">Volumen (m³)</th>
                    <th scope="row" rowspan="2">Weight of the Cylinder (Kg)</th>
                    <th scope="row" rowspan="2">Age (days)</th>
                    <th scope="row" rowspan="2">Unit Weight (Kg/m³)</th>
                    <th scope="row" rowspan="2">Failure Load (KN)</th>
                    <th scope="row" rowspan="2">Strenght (Mpa)</th>
                    <th scope="row" rowspan="2">Average Strenght (Mpa)</th>
                    <th scope="row" rowspan="2">Type of Fracture</th>
                    <th scope="row" rowspan="2">Observations</th>
                  </tr>
                  <tr>
                    <th scope="row">Diameter</th>
                    <th scope="row">High</th>
                  </tr>
                  <?php
                  $numFilas = 5; // Número de filas que deseas generar
                  
                  for ($i = 1; $i <= $numFilas; $i++) {
                    echo '<tr>';
                    echo '<th scope="row">' . $i . '</th>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="DiameterNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="HighNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AreaNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="VolNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="WeigCyNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AgeDaysNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="UnitWeigNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="FalLoadNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="StrenghtNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="AverageNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="TypeFractureNo' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="ObservationNo' . $i . '"></td>';
                    echo '</tr>';
                  }
                  ?>

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
          <button type="button" class="btn btn-success">Save Essay</button>
        </div>
  
      </div>
    </div>
          
  </div>


  </div>

  </div>
</section>

</main><!-- End #main -->


<?php include_once('../components/footer.php');  ?>