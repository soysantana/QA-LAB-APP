<?php
$page_title = 'Edicion de Muestra Registrada';
$requisition_form = 'show';
require_once('../config/load.php');
$Search = find_by_id('lab_test_requisition_form', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-requisition'])) {
    include('../database/requisition-form.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edicion de Muestra Registrada</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Paginas</li>
        <li class="breadcrumb-item active">Formulario de requisicion</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">

      <form class="row" action="requisition-form-edit.php?id=<?php echo $Search['id']; ?>" method="post">

        <!-- Sample Information Requisition -->
        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Información de muestra</h5>

              <div class="row g-3">
                <div class="col-md-3">
                  <label for="ProjectName" class="form-label">Nombre del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo ($Search['Project_Name']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="Client" class="form-label">Cliente</label>
                  <select id="Client" class="form-select" name="Client">
                    <option selected>Elegir...</option>
                    <option <?php if ($Search['Client'] == 'TSF LLagal') echo 'selected'; ?>>TSF LLagal</option>
                    <option <?php if ($Search['Client'] == 'TSF Naranjo') echo 'selected'; ?>>TSF Naranjo</option>
                    <option <?php if ($Search['Client'] == 'Capital Project') echo 'selected'; ?>>Capital Project</option>
                    <option <?php if ($Search['Client'] == 'MRM') echo 'selected'; ?>>MRM</option>
                    <option <?php if ($Search['Client'] == 'Geotecnia') echo 'selected'; ?>>Geotecnia</option>
                    <option <?php if ($Search['Client'] == 'Hidrogeologia') echo 'selected'; ?>>Hidrogeologia</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="ProjectNumber" class="form-label">Numero del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo ($Search['Project_Number']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="PackageID" class="form-label">Paquete ID</label>
                  <input type="text" class="form-control" name="PackageID" id="PackageID" value="<?php echo ($Search['Package_ID']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Structure" class="form-label">Estructura</label>
                  <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo ($Search['Structure']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Area" class="form-label">Area</label>
                  <input type="text" class="form-control" name="Area" id="Area" value="<?php echo ($Search['Area']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Source" class="form-label">Fuente</label>
                  <input type="text" class="form-control" name="Source" id="Source" value="<?php echo ($Search['Source']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="CollectionDate" class="form-label">Fecha de colección</label>
                  <input type="date" class="form-control" name="CollectionDate" id="CollectionDate" value="<?php echo ($Search['Sample_Date']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="Cviaje" class="form-label">Cantidad de Viajes</label>
                  <input type="text" class="form-control" name="Cviaje" id="Cviaje" value="<?php echo ($Search['Truck_Count']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="SampleBy" class="form-label">Muestreado por</label>
                  <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo ($Search['Sample_By']); ?>">
                </div>

              </div>
            </div>
          </div>

        </div>
        <!-- End Sample Information Requisition -->

        <!-- Information the multiple samples -->
        <div class="col-md-6" id="samples-container">
          <div class="card sample-card">
            <div class="card-body">
              <h5 class="card-title">Muestra</h5>

              <div class="row g-3">
                <div class="col-md-3">
                  <label for="SampleName_1" class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="SampleName_1" id="SampleName_1" value="<?php echo ($Search['Sample_ID']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="SampleNumber_1" class="form-label">Numero</label>
                  <input type="text" class="form-control" name="SampleNumber_1" id="SampleNumber_1" value="<?php echo ($Search['Sample_Number']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="DepthFrom_1" class="form-label">Profundidad desde</label>
                  <input type="text" class="form-control" name="DepthFrom_1" id="DepthFrom_1" value="<?php echo ($Search['Depth_From']); ?>">
                </div>
                <div class="col-md-3">
                  <label for="DepthTo_1" class="form-label">Profundidad hasta</label>
                  <input type="text" class="form-control" name="DepthTo_1" id="DepthTo_1" value="<?php echo ($Search['Depth_To']); ?>">
                </div>
                <div class="col-md-2">
                  <label for="MType_1" class="form-label">Material</label>
                  <select id="MType_1" class="form-select" name="MType_1">
                    <option value="" selected>Elegir...</option>
                    <option value="Soil" <?php if ($Search['Material_Type'] == 'Soil') echo 'selected'; ?>>Suelo</option>
                    <option value="Rock" <?php if ($Search['Material_Type'] == 'Rock') echo 'selected'; ?>>Roca</option>
                    <option value="Crudo" <?php if ($Search['Material_Type'] == 'Crudo') echo 'selected'; ?>>Crudo</option>
                    <option value="RF" <?php if ($Search['Material_Type'] == 'RF') echo 'selected'; ?>>RF</option>
                    <option value="IRF" <?php if ($Search['Material_Type'] == 'IRF') echo 'selected'; ?>>IRF</option>
                    <option value="FRF" <?php if ($Search['Material_Type'] == 'FRF') echo 'selected'; ?>>FRF</option>
                    <option value="UTF" <?php if ($Search['Material_Type'] == 'UTF') echo 'selected'; ?>>UTF</option>
                    <option value="TRF" <?php if ($Search['Material_Type'] == 'TRF') echo 'selected'; ?>>TRF</option>
                    <option value="FF" <?php if ($Search['Material_Type'] == 'FF') echo 'selected'; ?>>FF</option>
                    <option value="CF" <?php if ($Search['Material_Type'] == 'CF') echo 'selected'; ?>>CF</option>
                    <option value="LPF" <?php if ($Search['Material_Type'] == 'LPF') echo 'selected'; ?>>LPF</option>
                    <option value="RS" <?php if ($Search['Material_Type'] == 'RS') echo 'selected'; ?>>RS</option>
                    <option value="EMF" <?php if ($Search['Material_Type'] == 'EMF') echo 'selected'; ?>>EMF</option>
                    <option value="GF" <?php if ($Search['Material_Type'] == 'GF') echo 'selected'; ?>>GF</option>
                    <option value="UFF" <?php if ($Search['Material_Type'] == 'UFF') echo 'selected'; ?>>UFF</option>
                    <option value="EF" <?php if ($Search['Material_Type'] == 'EF') echo 'selected'; ?>>EF</option>
                    <option value="Common" <?php if ($Search['Material_Type'] == 'Common') echo 'selected'; ?>>Common</option>
                    <option value="Concreto" <?php if ($Search['Material_Type'] == 'Concreto') echo 'selected'; ?>>Concreto</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="SType_1" class="form-label">Tipo de muestra</label>
                  <select id="SType_1" class="form-select" name="SType_1">
                    <option value="" selected>Elegir...</option>
                    <option value="Grab" <?php if ($Search['Sample_Type'] == 'Grab') echo 'selected'; ?>>Grab</option>
                    <option value="Bulk" <?php if ($Search['Sample_Type'] == 'Bulk') echo 'selected'; ?>>Bulk</option>
                    <option value="Truck" <?php if ($Search['Sample_Type'] == 'Truck') echo 'selected'; ?>>Truck</option>
                    <option value="Rock" <?php if ($Search['Sample_Type'] == 'Rock') echo 'selected'; ?>>Rock</option>
                    <option value="Shelby" <?php if ($Search['Sample_Type'] == 'Shelby') echo 'selected'; ?>>Shelby</option>
                    <option value="Lexan" <?php if ($Search['Sample_Type'] == 'Lexan') echo 'selected'; ?>>Lexan</option>
                    <option value="Mazier" <?php if ($Search['Sample_Type'] == 'Mazier') echo 'selected'; ?>>Mazier</option>
                    <option value="Ring" <?php if ($Search['Sample_Type'] == 'Ring') echo 'selected'; ?>>Ring</option>
                    <option value="Cilindro" <?php if ($Search['Sample_Type'] == 'Cilindro') echo 'selected'; ?>>Cilindro</option>
                    <option value="Cubo" <?php if ($Search['Sample_Type'] == 'Cubo') echo 'selected'; ?>>Cubo</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="North_1" class="form-label">Norte</label>
                  <input type="text" class="form-control" name="North_1" id="North_1" value="<?php echo ($Search['North']); ?>">
                </div>
                <div class="col-md-2">
                  <label for="East_1" class="form-label">Este</label>
                  <input type="text" class="form-control" name="East_1" id="East_1" value="<?php echo ($Search['East']); ?>">
                </div>
                <div class="col-md-2">
                  <label for="Elev_1" class="form-label">Elevación</label>
                  <input type="text" class="form-control" name="Elev_1" id="Elev_1" value="<?php echo ($Search['Elev']); ?>">
                </div>
                <div class="col-md-12">
                  <label for="Comments_1" class="form-label">Comentarios</label>
                  <textarea class="form-control" name="Comments_1" id="Comments_1" style="height: 100px;"><?php echo ($Search['Comment']); ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Information the multiple samples -->

        <!-- Select Laboratory Tests -->
        <div class="col-md-6" id="lab-tests-container">

          <div class="card lab-tests-block">
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
                  "Densidad-Vibratorio" => "Densidad-Vibratorio",
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
                $selectedTests = explode(',', $Search['Test_Type'] ?? '');
                foreach ($testTypes as $id => $label) {
                  // Cada 5 checkboxes abrimos una nueva columna
                  if ($i % 5 === 0) {
                    if ($i > 0) echo '</div>'; // cerrar columna anterior
                    echo '<div class="col-md-5">';
                  }
                ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="TestType_1[]" id="<?= $id ?>_1" value="<?= $id ?>" <?= in_array($id, $selectedTests) ? 'checked' : '' ?>>
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
        <!-- End Select Laboratory Tests -->

        <!-- Actions Buttons -->
        <div class="col-md-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Acciones</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-requisition">Actualizar formulario de solicitud</button>
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


<?php include_once('../components/footer.php');  ?>