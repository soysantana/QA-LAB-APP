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

    <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Información de muestra</h5>

          <!-- Multi Columns Form -->
          <div class="row g-3">
            <div class="col-md-3">
              <label for="ProjectName" class="form-label">Nombre del Proyecto</label>
              <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo ($Search['Project_Name']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Client" class="form-label">Cliente</label>
              <input type="text" class="form-control" name="Client" id="Client" value="<?php echo ($Search['Client']); ?>">
            </div>
            <div class="col-md-3">
              <label for="ProjectNumber" class="form-label">Numero del Proyecto</label>
              <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo ($Search['Project_Number']); ?>">
            </div>
            <div class="col-md-3">
              <label for="PackageID" class="form-label">Paquete ID</label>
              <input type="text" class="form-control" name="PackageID" id="PackageID" value="<?php echo ($Search['Package_ID']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Structure" class="form-label">Estructura</label>
              <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo ($Search['Structure']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Area" class="form-label">Area</label>
              <input type="text" class="form-control" name="Area" id="Area" value="<?php echo ($Search['Area']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Source" class="form-label">Fuente</label>
              <input type="text" class="form-control" name="Source" id="Source" value="<?php echo ($Search['Source']); ?>">
            </div>
            <div class="col-md-3">
              <label for="CollectionDate" class="form-label">Fecha de colección</label>
              <input type="text" class="form-control" name="CollectionDate" id="CollectionDate" value="<?php echo ($Search['Sample_Date']); ?>">
            </div>
            <div class="col-md-3">
              <label for="SampleName" class="form-label">Nombre de la muestra</label>
              <input type="text" class="form-control" name="SampleName" id="SampleName" value="<?php echo ($Search['Sample_ID']); ?>">
            </div>
            <div class="col-md-3">
              <label for="SampleNumber" class="form-label">Numero de la muestra</label>
              <input type="text" class="form-control" name="SampleNumber" id="SampleNumber" value="<?php echo ($Search['Sample_Number']); ?>">
            </div>
            <div class="col-md-3">
              <label for="DepthFrom" class="form-label">Profundidad desde</label>
              <input type="text" class="form-control" name="DepthFrom" id="DepthFrom" value="<?php echo ($Search['Depth_From']); ?>">
            </div>
            <div class="col-md-3">
              <label for="DepthTo" class="form-label">Profundidad hasta</label>
              <input type="text" class="form-control" name="DepthTo" id="DepthTo" value="<?php echo ($Search['Depth_To']); ?>">
            </div>
            <div class="col-md-3">
              <label for="MType" class="form-label">Tipo de material</label>
              <select id="MType" class="form-select" name="MType">
                <option selected>Elegir...</option>
                <option <?php if ($Search['Material_Type'] == 'Soil') echo 'selected'; ?>>Soil</option>
                <option <?php if ($Search['Material_Type'] == 'Rock') echo 'selected'; ?>>Rock</option>
                <option <?php if ($Search['Material_Type'] == 'Crudo') echo 'selected'; ?>>Crudo</option>
                <option <?php if ($Search['Material_Type'] == 'RF') echo 'selected'; ?>>RF</option>
                <option <?php if ($Search['Material_Type'] == 'IRF') echo 'selected'; ?>>IRF</option>
                <option <?php if ($Search['Material_Type'] == 'FRF') echo 'selected'; ?>>FRF</option>
                <option <?php if ($Search['Material_Type'] == 'UTF') echo 'selected'; ?>>UTF</option>
                <option <?php if ($Search['Material_Type'] == 'TRF') echo 'selected'; ?>>TRF</option>
                <option <?php if ($Search['Material_Type'] == 'FF') echo 'selected'; ?>>FF</option>
                <option <?php if ($Search['Material_Type'] == 'CF') echo 'selected'; ?>>CF</option>
                <option <?php if ($Search['Material_Type'] == 'LPF') echo 'selected'; ?>>LPF</option>
                <option <?php if ($Search['Material_Type'] == 'RS') echo 'selected'; ?>>RS</option>
                <option <?php if ($Search['Material_Type'] == 'EMF') echo 'selected'; ?>>EMF</option>
                <option <?php if ($Search['Material_Type'] == 'GF') echo 'selected'; ?>>GF</option>
                <option <?php if ($Search['Material_Type'] == 'UFF') echo 'selected'; ?>>UFF</option>
                <option <?php if ($Search['Material_Type'] == 'EF') echo 'selected'; ?>>EF</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="SType" class="form-label">Tipo de muestra</label>
              <select id="SType" class="form-select" name="SType">
                <option selected>Elegir...</option>
                <option <?php if ($Search['Sample_Type'] == 'Grab') echo 'selected'; ?>>Grab</option>
                <option <?php if ($Search['Sample_Type'] == 'Bulk') echo 'selected'; ?>>Bulk</option>
                <option <?php if ($Search['Sample_Type'] == 'Truck') echo 'selected'; ?>>Truck</option>
                <option <?php if ($Search['Sample_Type'] == 'Shelby') echo 'selected'; ?>>Shelby</option>
                <option <?php if ($Search['Sample_Type'] == 'Lexan') echo 'selected'; ?>>Lexan</option>
                <option <?php if ($Search['Sample_Type'] == 'Ring') echo 'selected'; ?>>Ring</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="North" class="form-label">Norte</label>
              <input type="text" class="form-control" name="North" id="North" value="<?php echo ($Search['North']); ?>">
            </div>
            <div class="col-md-3">
              <label for="East" class="form-label">Este</label>
              <input type="text" class="form-control" name="East" id="East" value="<?php echo ($Search['East']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Elev" class="form-label">Elevación</label>
              <input type="text" class="form-control" name="Elev" id="Elev" value="<?php echo ($Search['Elev']); ?>">
            </div>
            <div class="col-md-3">
              <label for="Cviaje" class="form-label">Cantidad de Viajes</label>
              <input type="text" class="form-control" name="Cviaje" id="Cviaje" value="<?php echo ($Search['Truck_Count']); ?>">
            </div>
            <div class="col-md-3">
              <label for="SampleBy" class="form-label">Muestreado por</label>
              <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo ($Search['Sample_By']); ?>">
            </div>

          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Notas o comentarios</h5>
          
          <!-- Notes or Comments -->
          <div class="row g-3">
            <div class="col-12">
              <label for="Comments" class="form-label">Comentarios</label>
              <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comment']); ?></textarea>
            </div>

          </div>
        
        </div>
      </div>

    </div>

      <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Seleccionar pruebas de laboratorio</h5>

          <!-- Multi Columns Form -->
          <div class="row g-3">
            <div class="col-md-7">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType1" id="MC" value="MC" <?php if ($Search['Test_Type1'] == 'MC') echo 'checked'; ?>>
                <label class="form-check-label" for="MC">
                  Contenido de Humedad (MC)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType2" id="AL" value="AL" <?php if ($Search['Test_Type2'] == 'AL') echo 'checked'; ?>>
                <label class="form-check-label" for="AL">
                  Limite De Atterberg (AL)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType3" id="GS" value="GS" <?php if ($Search['Test_Type3'] == 'GS') echo 'checked'; ?>>
                <label class="form-check-label" for="GS">
                  Granulometria por Tamizado (GS)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType4" id="SP" value="SP" <?php if ($Search['Test_Type4'] == 'SP') echo 'checked'; ?>>
                <label class="form-check-label" for="SP">
                  Proctor Estandar (SP)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType5" id="SG" value="SG" <?php if ($Search['Test_Type5'] == 'SG') echo 'checked'; ?>>
                <label class="form-check-label" for="SG">
                  Gravedad Espesifica (SG)
                </label>
              </div>
            </div>

            <div class="col-md-5">
  
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType6" id="AR" value="AR" <?php if ($Search['Test_Type6'] == 'AR') echo 'checked'; ?>>
                <label class="form-check-label" for="AR">
                  Reactividad Acida (AR)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType7" id="SCT" value="SCT" <?php if ($Search['Test_Type7'] == 'SCT') echo 'checked'; ?>>
                <label class="form-check-label" for="SCT">
                  Castillo de Arena (SCT)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType8" id="LAA" value="LAA" <?php if ($Search['Test_Type8'] == 'LAA') echo 'checked'; ?>>
                <label class="form-check-label" for="LAA">
                  Abrasion de Los Angeles (LAA)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType9" id="SND" value="SND" <?php if ($Search['Test_Type9'] == 'SND') echo 'checked'; ?>>
                <label class="form-check-label" for="SND">
                  Sanidad (SND)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType10" id="Consolidation" value="Consolidation" <?php if ($Search['Test_Type10'] == 'Consolidation') echo 'checked'; ?>>
                <label class="form-check-label" for="Consolidation">
                  Consolidacion
                </label>
              </div>

            </div> <!-- END -->

            <div class="col-md-7">

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType11" id="UCS" value="UCS" <?php if ($Search['Test_Type11'] == 'UCS') echo 'checked'; ?>>
                <label class="form-check-label" for="UCS">
                  Compresion Simple (UCS)
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType12" id="PLT" value="PLT" <?php if ($Search['Test_Type12'] == 'PLT') echo 'checked'; ?>>
                <label class="form-check-label" for="PLT">
                  Carga Puntual (PLT)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType13" id="BTS" value="BTS" <?php if ($Search['Test_Type13'] == 'BTS') echo 'checked'; ?>>
                <label class="form-check-label" for="BTS">
                  Traccion Simple (BTS)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType14" id="HY" value="HY" <?php if ($Search['Test_Type14'] == 'HY') echo 'checked'; ?>>
                <label class="form-check-label" for="HY">
                  Hidrometro (HY)
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType15" id="DHY" value="DHY" <?php if ($Search['Test_Type15'] == 'DHY') echo 'checked'; ?>>
                <label class="form-check-label" for="DHY">
                  Doble Hidrometro (DHY)
                </label>
              </div>

            </div> <!-- END -->

            <div class="col-md-5">

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType16" id="PH" value="PH" <?php if ($Search['Test_Type16'] == 'PH') echo 'checked'; ?>>
                <label class="form-check-label" for="PH">
                  Pinhole (PH)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType17" id="Permeability" value="Permeability" <?php if ($Search['Test_Type17'] == 'Permeability') echo 'checked'; ?>>
                <label class="form-check-label" for="Permeability">
                  Permeabilidad
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType18" id="SHAPE" value="SHAPE" <?php if ($Search['Test_Type18'] == 'SHAPE') echo 'checked'; ?>>
                <label class="form-check-label" for="SHAPE">
                  Formas de Particulas (SHAPE)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType19" id="DENSITY" value="DENSITY" <?php if ($Search['Test_Type19'] == 'DENSITY') echo 'checked'; ?>>
                <label class="form-check-label" for="DENSITY">
                  Densidad
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType20" id="CRUMBS" value="CRUMBS" <?php if ($Search['Test_Type20'] == 'CRUMBS') echo 'checked'; ?>>
                <label class="form-check-label" for="CRUMBS">
                  Crumbs
                </label>
              </div>

            </div>

          </div>

        </div>
      </div>

      <div class="col-lg-6"><!-- Actions --> 
        
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
    
    </div><!-- End Actions -->

    </div>

  </form>

  </div>
</section>

</main><!-- End #main -->


<?php include_once('../components/footer.php');  ?>