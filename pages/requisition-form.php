<?php
$page_title = 'Formulario de solicitud';
$requisition_form = 'show';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['requisition-form'])) {
    include('../database/requisition-form/save.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Formulario de solicitud</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Paginas</li>
        <li class="breadcrumb-item active">Formulario de solicitud</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">

      <form class="row" action="requisition-form.php" method="post">

        <!-- Sample Information Requisition -->
        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Información de muestra</h5>

              <div class="row g-3">
                <div class="col-md-3">
                  <label for="ProjectName" class="form-label">Nombre del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectName" id="ProjectName">
                </div>
                <div class="col-md-3">
                  <label for="Client" class="form-label">Cliente</label>
                  <select id="Client" class="form-select" name="Client" required>
                    <option value="" selected>Elegir...</option>
                    <option value="TSF LLagal">TSF LLagal</option>
                    <option value="TSF Naranjo">TSF Naranjo</option>
                    <option value="Capital Project">Capital Project</option>
                    <option value="MRM">MRM</option>
                    <option value="Geotecnia">Geotecnia</option>
                    <option value="Hidrogeologia">Hidrogeologia</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="ProjectNumber" class="form-label">Numero del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber">
                </div>
                <div class="col-md-3">
                  <label for="PackageID" class="form-label">Paquete ID</label>
                  <input type="text" class="form-control" name="PackageID" id="PackageID">
                </div>
                <div class="col-md-3">
                  <label for="Structure" class="form-label">Estructura</label>
                  <input type="text" class="form-control" name="Structure" id="Structure" required>
                </div>
                <div class="col-md-3">
                  <label for="CollectionDate" class="form-label">Fecha de colección</label>
                  <input type="date" class="form-control" name="CollectionDate" id="CollectionDate" required>
                </div>
                <div class="col-md-3">
                  <label for="Cviaje" class="form-label">Cantidad de Viajes</label>
                  <input type="text" class="form-control" name="Cviaje" id="Cviaje">
                </div>
                <div class="col-md-3">
                  <label for="SampleBy" class="form-label">Muestreado por</label>
                  <input type="text" class="form-control" name="SampleBy" id="SampleBy" required>
                </div>

              </div>
            </div>
          </div>

        </div>
        <!-- End Sample Information Requisition -->

        <!-- Information the multiple samples -->
        <div id="samples-container">
          <div class="row mb-4 sample-card">
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">Muestra</h5>

                  <div class="row g-3">
                    <div class="col-md-4">
                      <label for="SampleName_1" class="form-label">Nombre</label>
                      <input type="text" class="form-control" name="SampleName_1" id="SampleName_1" required>
                    </div>
                    <div class="col-md-2">
                      <label for="SampleNumber_1" class="form-label">Numero</label>
                      <input type="text" class="form-control" name="SampleNumber_1" id="SampleNumber_1" required>
                    </div>
                    <div class="col-md-3">
                      <label for="Area_1" class="form-label">Area</label>
                      <input type="text" class="form-control" name="Area_1" id="Area_1">
                    </div>
                    <div class="col-md-3">
                      <label for="Source_1" class="form-label">Fuente</label>
                      <input type="text" class="form-control" name="Source_1" id="Source_1">
                    </div>
                    <div class="col-md-3">
                      <label for="MType_1" class="form-label">Tipo de material</label>
                      <select id="MType_1" class="form-select" name="MType_1" required>
                        <option selected>Elegir...</option>
                        <option value="Soil">Suelo</option>
                        <option value="Rock">Roca</option>
                        <option value="Crudo">Crudo</option>
                        <option value="RF">RF</option>
                        <option value="IRF">IRF</option>
                        <option value="FRF">FRF</option>
                        <option value="UTF">UTF</option>
                        <option value="TRF">TRF</option>
                        <option value="FF">FF</option>
                        <option value="CF">CF</option>
                        <option value="LPF">LPF</option>
                        <option value="RS">RS</option>
                        <option value="EMF">EMF</option>
                        <option value="GF">GF</option>
                        <option value="UFF">UFF</option>
                        <option value="EF">EF</option>
                        <option value="PQ">PQ</option>
                        <option value="Common">Common</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="SType_1" class="form-label">Tipo de muestra</label>
                      <select id="SType_1" class="form-select" name="SType_1" required>
                        <option selected>Elegir...</option>
                        <option value="Grab">Grab</option>
                        <option value="Bag">Bag</option>
                        <option value="Sacks">Sacks</option>
                        <option value="Bulk">Bulk</option>
                        <option value="Truck">Truck</option>
                        <option value="Rock">Rock</option>
                        <option value="Shelby">Shelby</option>
                        <option value="Lexan">Lexan</option>
                        <option value="Mazier">Mazier</option>
                        <option value="Ring">Ring</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="DepthFrom_1" class="form-label">Profundidad desde</label>
                      <input type="text" class="form-control" name="DepthFrom_1" id="DepthFrom_1">
                    </div>
                    <div class="col-md-3">
                      <label for="DepthTo_1" class="form-label">Profundidad hasta</label>
                      <input type="text" class="form-control" name="DepthTo_1" id="DepthTo_1">
                    </div>
                    <div class="col-md-4">
                      <label for="North_1" class="form-label">Norte</label>
                      <input type="text" class="form-control" name="North_1" id="North_1">
                    </div>
                    <div class="col-md-4">
                      <label for="East_1" class="form-label">Este</label>
                      <input type="text" class="form-control" name="East_1" id="East_1">
                    </div>
                    <div class="col-md-4">
                      <label for="Elev_1" class="form-label">Elevación</label>
                      <input type="text" class="form-control" name="Elev_1" id="Elev_1">
                    </div>
                    <div class="col-md-12">
                      <label for="Comments_1" class="form-label">Comentarios</label>
                      <textarea class="form-control" name="Comments_1" id="Comments_1" style="height: 100px;"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Information the multiple samples -->

            <!-- Select Laboratory Tests -->
            <div class="col-md-6">

              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">Seleccionar pruebas de laboratorio</h5>

                  <div class="row g-3">
                    <?php
                    $testTypes = [
                      "MC" => "Contenido de Humedad (MC)",
                      "AL" => "Limite De Atterberg (AL)",
                      "GS" => "Granulometria por Tamizado (GS)",
                      "SP" => "Proctor Estandar (SP)",
                      "SG" => "Gravedad Espesifica (SG)",
                      "AR" => "Reactividad Acida (AR)",
                      "SCT" => "Castillo de Arena (SCT)",
                      "LAA" => "Abrasion de Los Angeles (LAA)",
                      "SND" => "Sanidad (SND)",
                      "Consolidation" => "Consolidacion",
                      "UCS" => "Compresion Simple (UCS)",
                      "PLT" => "Carga Puntual (PLT)",
                      "BTS" => "Traccion Simple (BTS)",
                      "HY" => "Hidrometro (HY)",
                      "DHY" => "Doble Hidrometro (DHY)",
                      "PH" => "Pinhole (PH)",
                      "Permeability" => "Permeabilidad",
                      "SHAPE" => "Formas de Particulas (SHAPE)",
                      "DENSITY" => "Densidad",
                      "CRUMBS" => "Crumbs",
                      "Actividad" => "Actividad",
                      "Envio" => "Envio",
                    ];
                    ?>

                    <?php
                    $i = 0;
                    foreach ($testTypes as $id => $label) {
                      // Cada 5 checkboxes abrimos una nueva columna
                      if ($i % 5 === 0) {
                        if ($i > 0) echo '</div>'; // cerrar columna anterior
                        echo '<div class="col-md-5">';
                      }
                    ?>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="TestType_1[]" id="<?= $id ?>_1" value="<?= $id ?>">
                        <label class="form-check-label" for="<?= $id ?>_1"><?= $label ?></label>
                      </div>
                    <?php
                      $i++;
                    }
                    echo '</div>'; // cerrar última columna
                    ?>

                  </div>

                </div>

              </div>
            </div>
          </div>
        </div>
        <!-- End Select Laboratory Tests -->

        <!-- Actions Buttons -->
        <div class="col-md-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Acciones</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="button" class="btn btn-secondary" onclick="duplicateSample()"><i class="bi bi-plus-square me-1"></i></button>
                <button type="submit" class="btn btn-success" name="requisition-form">Guardar formulario de solicitud</button>
                <a href="requisition-form-view.php" class="btn btn-primary">Muestras Registradas</a>
              </div>

            </div>
          </div>

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<!-- Custom Script to Duplicate Samples and Lab Tests -->
<script>
  let sampleCount = 1;

  function duplicateSample() {
    sampleCount++;
    const container = document.getElementById('samples-container');
    const original = document.querySelector('.sample-card');
    const clone = original.cloneNode(true);

    // Actualizar todos los ID y NAME
    const elements = clone.querySelectorAll('[id],[name],[for]');
    elements.forEach(el => {
      if (el.id) {
        el.id = el.id.replace(/_\d+$/, "_" + sampleCount);
      }
      if (el.name) {
        el.name = el.name.replace(/_\d+(\[\])?$/, "_" + sampleCount + (el.name.endsWith("[]") ? "[]" : ""));
      }
      if (el.htmlFor) {
        el.htmlFor = el.htmlFor.replace(/_\d+$/, "_" + sampleCount);
      }
      if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
        el.value = ""; // limpiar valores
        if (el.type === "checkbox" || el.type === "radio") el.checked = false;
      }
      if (el.tagName === "SELECT") {
        el.selectedIndex = 0; // resetear select
      }
    });

    container.appendChild(clone);
  }
</script>
<!-- End Custom Script to Duplicate Samples and Lab Tests -->

<?php include_once('../components/footer.php');  ?>