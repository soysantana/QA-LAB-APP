<?php
  $page_title = 'Formulario de solicitud';
  $requisition_form = 'show';
  require_once('../config/load.php');
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

  <form class="row" action="../database/requisition-form.php" method="post">

    <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Información de muestra</h5>

          <!-- Multi Columns Form -->
          <div class="row g-3">
            <div class="col-md-3">
              <label for="ProjectName" class="form-label">Nombre del Proyecto</label>
              <input type="text" class="form-control" name="ProjectName" id="ProjectName">
            </div>
            <div class="col-md-3">
              <label for="Client" class="form-label">Cliente</label>
              <input type="text" class="form-control" name="Client" id="Client">
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
              <input type="text" class="form-control" name="Structure" id="Structure">
            </div>
            <div class="col-md-3">
              <label for="Area" class="form-label">Area</label>
              <input type="text" class="form-control" name="Area" id="Area">
            </div>
            <div class="col-md-3">
              <label for="Source" class="form-label">Fuente</label>
              <input type="text" class="form-control" name="Source" id="Source">
            </div>
            <div class="col-md-3">
              <label for="CollectionDate" class="form-label">Fecha de colección</label>
              <input type="date" class="form-control" name="CollectionDate" id="CollectionDate">
            </div>
            <div class="col-md-3">
              <label for="SampleName" class="form-label">Nombre de la muestra</label>
              <input type="text" class="form-control" name="SampleName" id="SampleName">
            </div>
            <div class="col-md-3">
              <label for="SampleNumber" class="form-label">Numero de la muestra</label>
              <input type="text" class="form-control" name="SampleNumber" id="SampleNumber">
            </div>
            <div class="col-md-3">
              <label for="DepthFrom" class="form-label">Profundidad desde</label>
              <input type="text" class="form-control" name="DepthFrom" id="DepthFrom">
            </div>
            <div class="col-md-3">
              <label for="DepthTo" class="form-label">Profundidad hasta</label>
              <input type="text" class="form-control" name="DepthTo" id="DepthTo">
            </div>
            <div class="col-md-3">
              <label for="MType" class="form-label">Tipo de material</label>
              <select id="MType" class="form-select" name="MType">
                <option selected>Elegir...</option>
                <option value="Soil">Soil</option>
                <option value="Rock">Rock</option>
                <option value="CF">CF</option>
                <option value="FF">FF</option>
                <option value="FRF">FRF</option>
                <option value="IRF">IRF</option>
                <option value="LPF">LPF</option>
                <option value="UTF">UTF</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="SType" class="form-label">Tipo de muestra</label>
              <select id="SType" class="form-select" name="SType">
                <option selected>Elegir...</option>
                <option value="Grab">Grab</option>
                <option value="Bulk">Bulk</option>
                <option value="Truck">Truck</option>
                <option value="Shelby">Shelby</option>
                <option value="Lexan">Lexan</option>
                <option value="Ring">Ring</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="North" class="form-label">Norte</label>
              <input type="text" class="form-control" name="North" id="North">
            </div>
            <div class="col-md-3">
              <label for="East" class="form-label">Este</label>
              <input type="text" class="form-control" name="East" id="East">
            </div>
            <div class="col-md-3">
              <label for="Elev" class="form-label">Elevación</label>
              <input type="text" class="form-control" name="Elev" id="Elev">
            </div>
            <div class="col-md-3">
              <label for="SampleBy" class="form-label">Muestreado por</label>
              <input type="text" class="form-control" name="SampleBy" id="SampleBy">
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
              <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
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
                <input class="form-check-input" type="checkbox" name="TestType1" id="mcOven" value="MC">
                <label class="form-check-label" for="mcOven">
                  MC (Oven)
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType2" id="mcStove" value="MC">
                <label class="form-check-label" for="mcStove">
                  MC (Stove)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType3" id="mcScale" value="MC">
                <label class="form-check-label" for="mcScale">
                  MC (Scale)
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType4" id="Atterberg" value="AL">
                <label class="form-check-label" for="Atterberg">
                  Atterberg Limit
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType5" id="GrainSize" value="GS">
                <label class="form-check-label" for="GrainSize">
                  Grain Size
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType6" id="SP" value="SP">
                <label class="form-check-label" for="SP">
                  Standard Proctor
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType7" id="SG" value="SG">
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
                <input class="form-check-input" type="checkbox" name="TestType8" id="AR" value="AR">
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
                <input class="form-check-input" type="checkbox" name="TestType9" id="SCT" value="SCT">
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
                <input class="form-check-input" type="checkbox" name="TestType10" id="LAA" value="LAA">
                <label class="form-check-label" for="LAA">
                  Los Angeles Abrasion
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType11" id="SND" value="SND">
                <label class="form-check-label" for="SND">
                  Soundness
                </label>
              </div>
            </div>

            <div class="col-md-3">
              <label for="UCS" class="form-label">Roca</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType12" id="UCS" value="UCS">
                <label class="form-check-label" for="UCS">
                  UCS
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType13" id="PLT" value="PLT">
                <label class="form-check-label" for="PLT">
                  PLT
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType14" id="BTT" value="BTT">
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
                <input class="form-check-input" type="checkbox" name="TestType15" id="Consolidation" value="Consolidation">
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
                <input class="form-check-input" type="checkbox" name="TestType16" id="HY" value="HY">
                <label class="form-check-label" for="HY">
                  Hydrometer
                </label>
              </div>
              
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType17" id="DHY" value="DHY">
                <label class="form-check-label" for="DHY">
                  Doble Hydrometer
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType18" id="PH" value="PH">
                <label class="form-check-label" for="PH">
                  Pinhole
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="TestType19" id="PGS" value="Permeability">
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
            <button type="submit" class="btn btn-success" name="requisition-form">Guardar formulario de solicitud</button>
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