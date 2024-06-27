<?php
  $page_title = 'Density Unit Weight';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
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
              <label for="PMethods" class="form-label">Preparation Methods</label>
              <select id="PMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Oven Dried</option>
                <option>Air Dried</option>
                <option>Microwave Dried</option>
                <option>Wet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="SMethods" class="form-label">Split Methods</label>
              <select id="SMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Manual</option>
                <option>Mechanical</option>
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


    <div class="col-lg-7">
      
      <div class="card">
              <div class="card-body">
                <h5 class="card-title">Specimen Properties</h5>
                <!-- Bordered Table -->
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col"></th>
                      <th scope="col">Specimen 1</th>
                      <th scope="col">Specimen 2</th>
                      <th scope="col">Specimen 3</th>
                      <th scope="col">Specimen 4</th>
                      <th scope="col">Specimen 5</th>
                      <th scope="col">Specimen 6</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    <?php
                    $datos = array(
                      array("Shape type (Cylindrical or Cubical)", "ShapeType1", "ShapeType2", "ShapeType3", "ShapeType4", "ShapeType5", "ShapeType6"),
                      array("Mass of moist specimen, M0", "MassMoist1", "MassMoist2", "MassMoist3", "MassMoist4", "MassMoist5", "MassMoist6"),
                      array("Diameter or Width, mm", "Width1", "Width2", "Width3", "Width4", "Width5", "Width6"),
                      array("height , mm", "Height1", "Height2", "Height3", "Height4", "Height5", "Height6"),
                      array("Length, mm", "Length1", "Length2", "Length3", "Length4", "Length5", "Length6"),
                      array("Volumes of Moist soil, V0 (cm3)", "Vol1", "Vol2", "Vol3", "Vol4", "Vol5", "Vol6"),
                      // Puedes agregar más filas según sea necesario
                    );

                    foreach ($datos as $fila) {
                      echo '<tr>';
                      foreach ($fila as $index => $valor) {
                          if ($index < 1) {
                              echo '<th scope="row">' . $valor . '</th>';
                          } else {
                              $readonly = (strpos($valor, "Vol") !== false) ? 'readonly tabindex="-1"' : '';
                              echo '<td><input type="text" style="border: none;" class="form-control" id="' . $valor . '" ' . $readonly . '></td>';
                          }
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


      <div class="col-lg-5">
      
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Calculation</h5>
          <!-- Bordered Table -->
          <table class="table table-bordered">
            <thead>
              <tr>
              </tr>
            </thead>
            <tbody>
              
              <?php
              $datos = array(
                array("Total Volumes of Moist specimen, V (cm3)", "TotVolMoist"),
                array("Mass of moist/total  of specimens, Mt (gr)", "MassMoistTot"),
                array("Water Content of specimen, w (%)", "WaterContSpecimen"),
                array("Density of total moist specimen, ᵨm(gr/cm3)", "DensityTotMoist"),
                array("Moist Unit Weight of Specimen,ᵨd(gr/cm3)", "UnitWeightGrCm3"),
                array("Moist Unit Weight of Specimen,ϒm (KN/m3)", "UnitWeightKnM3"),
                array("Dry Unit Weight of Specimen,ϒd (KN/m3)", "DrtUnitweight"),
                // Puedes agregar más filas según sea necesario
              );

              foreach ($datos as $fila) {
                echo '<tr>';
                foreach ($fila as $index => $valor) {
                    if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                    } else {
                        echo '<td><input type="text" style="border: none;" class="form-control" id="' . $valor . '" readonly tabindex="-1"></td>';
                    }
                }
                echo '</tr>';
              }
              ?>

            </tbody>
          </table>
          <!-- End Bordered Table -->

          <table class="table table-bordered">
            <thead>
              <tr>
              </tr>
            </thead>
            <tbody>
              
              <?php
              $datos = array(
                array("Moist Unit Weight of Specimen,ϒm (Kg/m3) ", "MUWSkgm3"),
                array("Dry Unit Weight of Specimen,ϒd (Kg/m3) ", "DUWOSkgm3"),
                // Puedes agregar más filas según sea necesario
              );

              foreach ($datos as $fila) {
                echo '<tr>';
                foreach ($fila as $index => $valor) {
                    if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                    } else {
                        $readonly = (in_array($valor, ["MUWSkgm3", "DUWOSkgm3"])) ? 'readonly tabindex="-1"' : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" id="' . $valor . '" ' . $readonly . '></td>';
                    }
                }
                echo '</tr>';
              }
              ?>
              
            </tbody>
          </table>

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
                    <a href="#" onclick="loadContent('grain-size-fine-agg.php')"> <!-- Modificado -->
                        <span>Grain Size Fine Aggregate</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="loadContent('grain-size-coarse-agg.php')"> <!-- Modificado -->
                        <span>Grain Size Coarse Aggregate</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="loadContent('grain-size-coarsethan-agg.php')"> <!-- Modificado -->
                        <span>Grain Size Coarse Than Aggregate</span>
                    </a>
                </li>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadContent(page) {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Reemplazar solo el contenido dentro de la etiqueta main
      document.getElementById("main").innerHTML = xhr.responseText;

      // Cerrar y volver a abrir el modal
      $('#disablebackdrop').modal('hide');
      $('#disablebackdrop').modal('show');
    }
  };

  xhr.open("GET", page, true);
  xhr.send();
}

</script>

<script src="../js/Grain-Size.js"></script>



<?php include_once('../components/footer.php');  ?>