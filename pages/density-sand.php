<?php
  $page_title = 'Sand Density Calibration';
  $class_form = ' ';
  $form_show = 'show';
  $Density = 'active';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Sand Density Calibration</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Sand Density</li>
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
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod">
              </select>
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
              <h5 class="card-title">Bulk Density of Sand</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Trials</th>
                    <th scope="row">1</th>
                    <th scope="row">2</th>
                    <th scope="row">3</th>
                  </tr>

                  <?php
                  $campos = array(
                    "Weight of Sand + Mold (g)" => "WeigSand",
                    "Mold" => "Mold",
                    "Weight Mold (g)" => "WeigMold",
                    "Weight of Sand in Mold (g) =(A-C)" => "WeigSandMold",
                    "Volume of Mold (cm3)" => "VolMoldCm3",
                    "Bulk Density of Sand (g/cm3) =(D/E))" => "BulkDensity",
                    "Average Bulk Density of Sand (g/cm3) =(1+2+3)/3" => "Average1"
                  );

                  foreach ($campos as $label => $id) {
                    echo '<tr>';
                    echo '<th scope="col">' . $label . '</th>';
                    for ($i = 1; $i <= 3; $i++) { // Asumo 3 columnas por fila, ajusta según tus necesidades
                      echo '<td><input type="text" style="border: none;" class="form-control" id="' . $id . $i . '"></td>';
                    }
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

    </div>


    <div class="col-lg-9">
      
    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Volume of  Funnel</h5>
          <!-- Bordered Table -->
          <table class="table table-bordered">
            <tbody>
              <tr>
                <th scope="row">Trials</th>
                <th scope="row">1</th>
                <th scope="row">2</th>
                <th scope="row">3</th>
              </tr>

              <?php
              $campos = array(
                "Weight of Sand + Container Before Test (g)" => "WeigBefore",
                "Weight of Sand + Container After Test (g)" => "WeigAfter",
                "Weight Sand Used (g) =(A-B)" => "WeigSandUse",
                "Bulk Density of Sand (g/cm3)" => "BDensityGcm3",
                "Volume of Funnel (cm3)  =(C/D)" => "VFcm3CD",
                "Average Volume of Funnel (cm3) =(1+2+3)/3" => "Average2"
              );

              foreach ($campos as $label => $id) {
                echo '<tr>';
                echo '<th scope="col">' . $label . '</th>';
                for ($i = 1; $i <= 3; $i++) { // Asumo 3 columnas por fila, ajusta según tus necesidades
                  echo '<td><input type="text" style="border: none;" class="form-control" id="' . $id . $i . '"></td>';
                }
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