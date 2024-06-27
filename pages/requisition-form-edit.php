<?php
  $page_title = 'Formulario de solicitud';
  $requisition_form = 'show';
  require_once('../config/load.php');
  $Search = find_by_id('lab_test_requisition_form', (int)$_GET['id']);
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

  <form class="row" action="../database/requisition-form.php?id=<?php echo $Search['id']; ?>" method="post">

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
                <option <?php if ($Search['Material_Type'] == 'CF') echo 'selected'; ?>>CF</option>
                <option <?php if ($Search['Material_Type'] == 'FF') echo 'selected'; ?>>FF</option>
                <option <?php if ($Search['Material_Type'] == 'FRF') echo 'selected'; ?>>FRF</option>
                <option <?php if ($Search['Material_Type'] == 'IRF') echo 'selected'; ?>>IRF</option>
                <option <?php if ($Search['Material_Type'] == 'LPF') echo 'selected'; ?>>LPF</option>
                <option <?php if ($Search['Material_Type'] == 'UTF') echo 'selected'; ?>>UTF</option>
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
            <div class="col-md-3">
              <label for="mcOven" class="form-label">Suelo</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType1" id="mcOven" value="MC" <?php if ($Search['Test_Type1'] == 'MC') echo 'checked'; ?>>
                <label class="form-check-label" for="mcOven">
                  MC (Oven)
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType2" id="mcStove" value="MC" <?php if ($Search['Test_Type2'] == 'MC') echo 'checked'; ?>>
                <label class="form-check-label" for="mcStove">
                  MC (Stove)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType3" id="mcScale" value="MC" <?php if ($Search['Test_Type3'] == 'MC') echo 'checked'; ?>>
                <label class="form-check-label" for="mcScale">
                  MC (Scale)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType4" id="Atterberg" value="AL" <?php if ($Search['Test_Type4'] == 'AL') echo 'checked'; ?>>
                <label class="form-check-label" for="Atterberg">
                  Atterberg Limit
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType5" id="GrainSize" value="GS" <?php if ($Search['Test_Type5'] == 'GS') echo 'checked'; ?>>
                <label class="form-check-label" for="GrainSize">
                  Grain Size
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType6" id="SP" value="SP" <?php if ($Search['Test_Type6'] == 'SP') echo 'checked'; ?>>
                <label class="form-check-label" for="SP">
                  Standard Proctor
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType7" id="SG" value="SG" <?php if ($Search['Test_Type7'] == 'SG') echo 'checked'; ?>>
                <label class="form-check-label" for="SG">
                  Specific Gravity
                </label>
              </div>
            </div>

            <div class="col-md-3">
              <label for="mcOvenAgg" class="form-label">Agregados</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType1" id="mcOvenAgg" value="MC">
                <label class="form-check-label" for="mcOvenAgg">
                  MC (Oven)
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType8" id="AR" value="AR" <?php if ($Search['Test_Type8'] == 'AR') echo 'checked'; ?>>
                <label class="form-check-label" for="AR">
                  Acid Reactivity
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType5" id="GrainSizeAgg" value="GS">
                <label class="form-check-label" for="GrainSizeAgg">
                  Grain Size
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType6" id="SpAgg" value="SP">
                <label class="form-check-label" for="SpAgg">
                  Standard Proctor
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType9" id="SCT" value="SCT" <?php if ($Search['Test_Type9'] == 'SCT') echo 'checked'; ?>>
                <label class="form-check-label" for="SCT">
                  Sand Castle
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType7" id="SgAgg" value="SG">
                <label class="form-check-label" for="SgAgg">
                  Specific Gravity
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType10" id="LAA" value="LAA" <?php if ($Search['Test_Type10'] == 'LAA') echo 'checked'; ?>>
                <label class="form-check-label" for="LAA">
                  Los Angeles Abrasion
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType11" id="SND" value="SND" <?php if ($Search['Test_Type11'] == 'SND') echo 'checked'; ?>>
                <label class="form-check-label" for="SND">
                  Soundness
                </label>
              </div>
            </div>

            <div class="col-md-3">
              <label for="UCS" class="form-label">Roca</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType12" id="UCS" value="UCS" <?php if ($Search['Test_Type12'] == 'UCS') echo 'checked'; ?>>
                <label class="form-check-label" for="UCS">
                  UCS
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType13" id="PLT" value="PLT" <?php if ($Search['Test_Type13'] == 'PLT') echo 'checked'; ?>>
                <label class="form-check-label" for="PLT">
                  PLT
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType14" id="BTT" value="BTT" <?php if ($Search['Test_Type14'] == 'BTT') echo 'checked'; ?>>
                <label class="form-check-label" for="BTT">
                  BTT
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType7" id="SgRock" value="SG">
                <label class="form-check-label" for="SgRock">
                  Specific Gravity
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType10" id="LAaRock" value="LAA">
                <label class="form-check-label" for="LAaRock">
                  Los Angeles Abrasion
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType15" id="Consolidation" value="Consolidation" <?php if ($Search['Test_Type15'] == 'Consolidation') echo 'checked'; ?>>
                <label class="form-check-label" for="Consolidation">
                  Consolidation
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType6" id="SpRock" value="SP">
                <label class="form-check-label" for="SpRock">
                  Standard Proctor
                </label>
              </div>

            </div>

            <div class="col-md-3">
              <label for="HY" class="form-label">Prueba especial</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType16" id="HY" value="HY" <?php if ($Search['Test_Type16'] == 'HY') echo 'checked'; ?>>
                <label class="form-check-label" for="HY">
                  Hydrometer
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType17" id="DHY" value="DHY" <?php if ($Search['Test_Type17'] == 'DHY') echo 'checked'; ?>>
                <label class="form-check-label" for="DHY">
                  Doble Hydrometer
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType18" id="PH" value="PH" <?php if ($Search['Test_Type18'] == 'PH') echo 'checked'; ?>>
                <label class="form-check-label" for="PH">
                  Pinhole
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType19" id="PGS" value="Permeability" <?php if ($Search['Test_Type19'] == 'Permeability') echo 'checked'; ?>>
                <label class="form-check-label" for="PGS">
                  Permeability Granular Soil
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
            <a href="requisition-form-view.php" class="btn btn-primary">Formulario de solicitud de búsqueda</a>
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