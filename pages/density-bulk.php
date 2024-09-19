<?php
  $page_title = 'Density Unit Weight';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Density Unit Weight</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Unit Weight</li>
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


    <div class="col-lg-5">
      
      <div class="card">
              <div class="card-body">
                <h5 class="card-title">Loose Bulk Density</h5>
                <!-- Bordered Table -->
                <table class="table table-bordered">
                  <tbody>

                    <?php
                    $campos = array(
                      "Weight of tare (g)" => "WeigTare",
                      "Weight of tare + Soil (g)" => "WeigTareSoil",
                      "Volume of the Mold (m³)" => "VolMold",
                      "Weight Loose Material (g)" => "WeigLoose",
                      "Absorption %" => "Absorption",
                      "Specific Gravity (OD)" => "SgOD",
                      "Density of Water (Kg/m³)" => "DenWaterKgm3",
                      "Loose Bulk Denisty (Kg/m³)" => "LooseBulkKgm3",
                      "Percent Voids in loose Aggregate" => "PercentVoids"
                    );

                    foreach ($campos as $label => $id) {
                      echo '<tr>';
                      echo '<th scope="col">' . $label . '</th>';
                      echo '<td><input type="text" style="border: none;" class="form-control" id="' . $id . '"></td>';
                      echo '</tr>';
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
          <h5 class="card-title">Compacted Bulk Density</h5>
          <!-- Bordered Table -->
          <table class="table table-bordered">
            <thead>
              <tr>
              </tr>
            </thead>
            <tbody>
              
            <?php
            $campos = array(
              "Weight of tare (g)" => "WeigTareCBD",
              "Weight of tare + Soil (g)" => "WeigTareSoilCBD",
              "Volume of the Mold (m³)" => "VolMoldCBD",
              "Weight Loose Material (g)" => "WeigLooseCBD",
              "Absorption %" => "AbsorptionCBD",
              "Specific Gravity (OD)" => "SgODCBD",
              "Density of Water (Kg/m³)" => "DenWaterKgm3CBD",
              "Compacted Bulk Denisty (Kg/m³)" => "CompaBulkCBD",
              "Percent Voids in loose Aggregate" => "PercentVoidsCBD"
            );

            foreach ($campos as $label => $id) {
              echo '<tr>';
              echo '<th scope="col">' . $label . '</th>';
              echo '<td><input type="text" style="border: none;" class="form-control" id="' . $id . '"></td>';
              echo '</tr>';
            }
            ?>

            </tbody>
          </table>
          <!-- End Bordered Table -->
          
        </div>
      </div>
    
    </div>

    
    <div class="col-lg-2">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="button" class="btn btn-success">Save Essay</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true">Launch</button>
        </div>

      </div>
    </div>
  
  </div>

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
                    <a href="count-nuclear.php">
                        <span>Count Nuclear</span>
                    </a>
                </li>
                <li>
                    <a href="density-sand.php">
                        <span>Density Sand</span>
                    </a>
                </li>
                <li>
                    <a href="density-weight.php">
                        <span>Density Weight</span>
                    </a>
                </li>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/Grain-Size.js"></script>
<?php include_once('../components/footer.php');  ?>