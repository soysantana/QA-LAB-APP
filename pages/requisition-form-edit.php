<?php
$page_title = 'Edición de Paquete de Muestras';
$requisition_form = 'show';
require_once('../config/load.php');

// Recibir parámetros
$packageId = $_POST['package_id'] ?? '';

if (empty($packageId)) {
  $session->msg("d", "No se especificó un paquete.");
  redirect('requisition-form-view.php');
}

$twoMonthsAgo = date('Y-m-d', strtotime('-24 months'));

$SearchRows = find_by_sql("
    SELECT * 
    FROM lab_test_requisition_form 
    WHERE Package_ID = '" . $db->escape($packageId) . "'
      AND Registed_Date >= '{$twoMonthsAgo}'
    ORDER BY Registed_Date DESC
");

// Datos generales (usamos la primera fila del paquete)
$Search = $SearchRows[0];

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-requisition'])) {
    include('../database/requisition-form/update.php');
  }
}

page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edición de Paquete de Muestras</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Páginas</li>
        <li class="breadcrumb-item active">Formulario de requisición</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">

      <form class="row" action="requisition-form-edit.php" method="post">
        <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($packageId); ?>">

        <!-- Información general del paquete -->
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Información general del paquete</h5>
              <div class="row g-3">
                <div class="col-md-3">
                  <label for="ProjectName" class="form-label">Nombre del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo $Search['Project_Name']; ?>">
                </div>
                <div class="col-md-3">
                  <label for="Client" class="form-label">Cliente</label>
                  <select id="Client" class="form-select" name="Client">
                    <option value="" selected>Elegir...</option>
                    <option <?php if ($Search['Client'] == 'TSF LLagal') echo 'selected'; ?>>TSF LLagal</option>
                    <option <?php if ($Search['Client'] == 'TSF Naranjo') echo 'selected'; ?>>TSF Naranjo</option>
                    <option <?php if ($Search['Client'] == 'PV Project') echo 'selected'; ?>>PV Project</option>
                    <option <?php if ($Search['Client'] == 'Capital Project') echo 'selected'; ?>>Capital Project</option>
                    <option <?php if ($Search['Client'] == 'MRM') echo 'selected'; ?>>MRM</option>
                    <option <?php if ($Search['Client'] == 'Geotecnia') echo 'selected'; ?>>Geotecnia</option>
                    <option <?php if ($Search['Client'] == 'Hidrogeologia') echo 'selected'; ?>>Hidrogeologia</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="ProjectNumber" class="form-label">Numero del Proyecto</label>
                  <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo $Search['Project_Number']; ?>">
                </div>
                <div class="col-md-3">
                  <label for="PackageID" class="form-label">Paquete ID</label>
                  <input
                    type="text"
                    class="form-control"
                    name="PackageID"
                    id="PackageID"
                    value="<?php echo $Search['Package_ID']; ?>"
                    <?php if (!user_can_access(2)) echo 'readonly'; ?>>
                  <input type="hidden" name="OldPackageID" value="<?php echo $Search['Package_ID']; ?>" />
                </div>
                <div class="col-md-3">
                  <label for="Structure" class="form-label">Estructura</label>
                  <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo $Search['Structure']; ?>">
                </div>
                <div class="col-md-3">
                  <label for="CollectionDate" class="form-label">Fecha de colección</label>
                  <input type="date" class="form-control" name="CollectionDate" id="CollectionDate" value="<?php echo $Search['Sample_Date']; ?>" required>
                </div>
                <div class="col-md-3">
                  <label for="Cviaje" class="form-label">Cantidad de Viajes</label>
                  <input type="text" class="form-control" name="Cviaje" id="Cviaje" value="<?php echo $Search['Truck_Count']; ?>">
                </div>
                <div class="col-md-3">
                  <label for="SampleBy" class="form-label">Muestreado por</label>
                  <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo $Search['Sample_By']; ?>" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Información general del paquete -->

        <?php foreach ($SearchRows as $index => $row): ?>
          <div class="row mb-4">

            <!-- Columna izquierda: información de la muestra -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">Muestra <?php echo $row['Sample_Number']; ?></h5>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label for="SampleName_<?php echo $index; ?>" class="form-label">Nombre</label>
                      <input type="text" class="form-control"
                        name="SampleName_<?php echo $index; ?>"
                        id="SampleName_<?php echo $index; ?>"
                        value="<?php echo $row['Sample_ID']; ?>">
                      <input
                        type="hidden"
                        name="OldSampleName_<?php echo $index; ?>"
                        value="<?php echo $row['Sample_ID']; ?>" />
                    </div>
                    <div class="col-md-2">
                      <label for="SampleNumber_<?php echo $index; ?>" class="form-label">Número</label>
                      <input type="text" class="form-control"
                        name="SampleNumber_<?php echo $index; ?>"
                        id="SampleNumber_<?php echo $index; ?>"
                        value="<?php echo $row['Sample_Number']; ?>">
                      <input
                        type="hidden"
                        name="OldSampleNumber_<?php echo $index; ?>"
                        value="<?php echo $row['Sample_Number']; ?>" />
                    </div>
                    <div class="col-md-3">
                      <label for="Area_<?php echo $index; ?>" class="form-label">Area</label>
                      <input type="text" class="form-control" name="Area_<?php echo $index; ?>" id="Area_<?php echo $index; ?>" value="<?php echo $row['Area']; ?>">
                    </div>
                    <div class="col-md-3">
                      <label for="Source_<?php echo $index; ?>" class="form-label">Fuente</label>
                      <input type="text" class="form-control" name="Source_<?php echo $index; ?>" id="Source_<?php echo $index; ?>" value="<?php echo $row['Source']; ?>">
                    </div>
                    <div class="col-md-3">
                      <label for="MType_<?php echo $index; ?>" class="form-label">Material</label>
                      <select id="MType_<?php echo $index; ?>" class="form-select" name="MType_<?php echo $index; ?>">
                        <option value="" selected>Elegir...</option>
                        <option value="Soil" <?php if ($row['Material_Type'] == 'Soil') echo 'selected'; ?>>Suelo</option>
                        <option value="Rock" <?php if ($row['Material_Type'] == 'Rock') echo 'selected'; ?>>Roca</option>
                        <option value="Crudo" <?php if ($row['Material_Type'] == 'Crudo') echo 'selected'; ?>>Crudo</option>
                        <option value="RF" <?php if ($row['Material_Type'] == 'RF') echo 'selected'; ?>>RF</option>
                        <option value="IRF" <?php if ($row['Material_Type'] == 'IRF') echo 'selected'; ?>>IRF</option>
                        <option value="FRF" <?php if ($row['Material_Type'] == 'FRF') echo 'selected'; ?>>FRF</option>
                        <option value="UTF" <?php if ($row['Material_Type'] == 'UTF') echo 'selected'; ?>>UTF</option>
                        <option value="TRF" <?php if ($row['Material_Type'] == 'TRF') echo 'selected'; ?>>TRF</option>
                        <option value="FF" <?php if ($row['Material_Type'] == 'FF') echo 'selected'; ?>>FF</option>
                        <option value="CF" <?php if ($row['Material_Type'] == 'CF') echo 'selected'; ?>>CF</option>
                        <option value="LPF" <?php if ($row['Material_Type'] == 'LPF') echo 'selected'; ?>>LPF</option>
                        <option value="RS" <?php if ($row['Material_Type'] == 'RS') echo 'selected'; ?>>RS</option>
                        <option value="EMF" <?php if ($row['Material_Type'] == 'EMF') echo 'selected'; ?>>EMF</option>
                        <option value="GF" <?php if ($row['Material_Type'] == 'GF') echo 'selected'; ?>>GF</option>
                        <option value="UFF" <?php if ($row['Material_Type'] == 'UFF') echo 'selected'; ?>>UFF</option>
                        <option value="EF" <?php if ($row['Material_Type'] == 'EF') echo 'selected'; ?>>EF</option>
                        <option value="PQ" <?php if ($row['Material_Type'] == 'PQ') echo 'selected'; ?>>PQ</option>
                        <option value="Common" <?php if ($row['Material_Type'] == 'Common') echo 'selected'; ?>>Common</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="SType_<?php echo $index; ?>" class="form-label">Tipo de muestra</label>
                      <select id="SType_<?php echo $index; ?>" class="form-select" name="SType_<?php echo $index; ?>">
                        <option value="" selected>Elegir...</option>
                        <option value="Grab" <?php if ($row['Sample_Type'] == 'Grab') echo 'selected'; ?>>Grab</option>
                        <option value="Bulk" <?php if ($row['Sample_Type'] == 'Bulk') echo 'selected'; ?>>Bulk</option>
                        <option value="Bag" <?php if ($row['Sample_Type'] == 'Bag') echo 'selected'; ?>>Bag</option>
                        <option value="Sacks" <?php if ($row['Sample_Type'] == 'Sacks') echo 'selected'; ?>>Sacks</option>
                        <option value="Truck" <?php if ($row['Sample_Type'] == 'Truck') echo 'selected'; ?>>Truck</option>
                        <option value="Rock" <?php if ($row['Sample_Type'] == 'Rock') echo 'selected'; ?>>Rock</option>
                        <option value="Shelby" <?php if ($row['Sample_Type'] == 'Shelby') echo 'selected'; ?>>Shelby</option>
                        <option value="Lexan" <?php if ($row['Sample_Type'] == 'Lexan') echo 'selected'; ?>>Lexan</option>
                        <option value="Mazier" <?php if ($row['Sample_Type'] == 'Mazier') echo 'selected'; ?>>Mazier</option>
                        <option value="Ring" <?php if ($row['Sample_Type'] == 'Ring') echo 'selected'; ?>>Ring</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="DepthFrom_<?php echo $index; ?>" class="form-label">Profundidad desde</label>
                      <input type="text" class="form-control"
                        name="DepthFrom_<?php echo $index; ?>"
                        id="DepthFrom_<?php echo $index; ?>"
                        value="<?php echo $row['Depth_From']; ?>">
                    </div>
                    <div class="col-md-3">
                      <label for="DepthTo_<?php echo $index; ?>" class="form-label">Profundidad hasta</label>
                      <input type="text" class="form-control"
                        name="DepthTo_<?php echo $index; ?>"
                        id="DepthTo_<?php echo $index; ?>"
                        value="<?php echo $row['Depth_To']; ?>">
                    </div>
                    <div class="col-md-4">
                      <label for="North_<?php echo $index; ?>" class="form-label">Norte</label>
                      <input type="text" class="form-control" name="North_<?php echo $index; ?>" id="North_<?php echo $index; ?>" value="<?php echo ($row['North']); ?>">
                    </div>
                    <div class="col-md-4">
                      <label for="East_<?php echo $index; ?>" class="form-label">Este</label>
                      <input type="text" class="form-control" name="East_<?php echo $index; ?>" id="East_<?php echo $index; ?>" value="<?php echo ($row['East']); ?>">
                    </div>
                    <div class="col-md-4">
                      <label for="Elev_<?php echo $index; ?>" class="form-label">Elevación</label>
                      <input type="text" class="form-control" name="Elev_<?php echo $index; ?>" id="Elev_<?php echo $index; ?>" value="<?php echo ($row['Elev']); ?>">
                    </div>
                    <div class="col-md-12">
                      <label for="Comments_<?php echo $index; ?>" class="form-label">Comentarios</label>
                      <textarea class="form-control"
                        name="Comments_<?php echo $index; ?>"
                        id="Comments_<?php echo $index; ?>"
                        style="height: 100px;"><?php echo $row['Comment']; ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Columna derecha: ensayos -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">Ensayos de Muestra <?php echo $row['Sample_Number']; ?></h5>
                  <div class="row">
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
                      "DENSIDAD-VIBRATORIO" => "Densidad Vibratorio",
                      "DENSITY" => "Densidad",
                      "CRUMBS" => "Crumbs",
                      "Actividad" => "Actividad",
                      "Envio" => "Envio",
                    ];
                    $selectedTests = explode(',', $row['Test_Type'] ?? '');
                    foreach ($testTypes as $id => $label): ?>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox"
                            name="TestType_<?php echo $index; ?>[]"
                            id="<?php echo $id . '_' . $index; ?>"
                            value="<?php echo $id; ?>"
                            <?php echo in_array($id, $selectedTests) ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="<?php echo $id . '_' . $index; ?>">
                            <?php echo $label; ?>
                          </label>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- row -->
        <?php endforeach; ?>


        <!-- Botones de acción -->
        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Acciones</h5>
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update-requisition">
                  Actualizar paquete
                </button>
                <a href="requisition-form-view.php" class="btn btn-primary">
                  Volver a Muestras Registradas
                </a>
              </div>
            </div>
          </div>
        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php'); ?>