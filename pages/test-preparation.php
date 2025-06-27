<?php
$page_title = 'Muestra en preparación';
$tracking_show = 'show';
$class_tracking = ' ';
$preparation = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['test-preparation'])) {
    include('../database/sample-tracking/preparation/save.php');
  } elseif (isset($_POST['delete_multiple'])) {
    include('../database/sample-tracking/preparation/delete.php');
  } elseif (isset($_POST['SendMultipleRealization'])) {
    include('../database/sample-tracking/realization/send-multiple.php');
  } elseif (isset($_POST['update_multiple'])) {
    include('../database/sample-tracking/preparation/update.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestra en preparación</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Formularios</li>
        <li class="breadcrumb-item active">Muestra en preparación</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">

      <!-- formulario de añadir muestra a la preparacion -->
      <div class="col-lg-5">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">AÑADIR MUESTRA A LA PREPARACIÓN</h5>

            <form class="row g-3" method="post" action="test-preparation.php">
              <div class="col-md-12">
                <label for="Sname" class="form-label">Nombre de la muestra</label>
                <input type="text" class="form-control" name="Sname" id="Sname" autocomplete="off">
              </div>
              <div class="col-md-12">
                <label for="Snumber" class="form-label">Numero de muestra</label>
                <input type="text" class="form-control" name="Snumber" id="Snumber" autocomplete="off">
              </div>
              <div class="col-md-12">
                <label for="Ttype" class="form-label">Tipo de prueba</label>
                <select id="Ttype" class="form-select" size="20" name="Ttype[]" multiple>
                  <option value="MC">MC</option>
                  <option value="AL">AL</option>
                  <option value="GS">GS</option>
                  <option value="SP">SP</option>
                  <option value="SG">SG</option>
                  <option value="UCS">UCS</option>
                  <option value="BTS">BTS</option>
                  <option value="PLT">PLT</option>
                  <option value="HY">HY</option>
                  <option value="DHY">DHY</option>
                  <option value="AR">AR</option>
                  <option value="SCT">SCT</option>
                  <option value="LAA">LAA</option>
                  <option value="SND">SND</option>
                  <option value="Consolidation">Consolidación</option>
                  <option value="PH">PH</option>
                  <option value="Permeability">Permeabilidad</option>
                  <option value="SHAPE">Formas de Partículas</option>
                  <option value="DENSITY">Densidad</option>
                  <option value="CRUMBS">CRUMBS</option>
                </select>
                <small class="text-muted">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones.</small>
              </div>
              <div class="col-md-12">
                <label for="Technician" class="form-label">Técnico/a</label>
                <input type="text" class="form-control" name="Technician" id="Technician" autocomplete="off">
              </div>
              <div>
                <button type="submit" class="btn btn-success" name="test-preparation"><i class="bi bi-save me-1"></i> Enviar a Preparación</button>
              </div>
            </form>

          </div>
        </div>
      </div>
      <!-- END formulario de añadir muestra a la preparacion -->

      <!-- tabla de muestra en preparacion para el envio a realizacion -->
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">LISTA DE MUESTRAS EN PREPARACIÓN</h5>

            <?php $week = date('Y-m-d', strtotime('-14 days')); ?>
            <?php $realization = "(SELECT 1 FROM test_realization WHERE sample_name = p.sample_name AND sample_number = p.sample_number AND test_type = p.test_type)"; ?>
            <?php $Seach = find_by_sql("SELECT id, Sample_Name, Sample_Number, Test_Type, Technician, Start_Date FROM test_preparation p WHERE Start_Date >= '{$week}' AND NOT EXISTS $realization ORDER BY Register_Date DESC"); ?>

            <form id="multiple-send-form" method="post" action="test-preparation.php">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Select</th>
                    <th scope="col">Nombre de la muestra</th>
                    <th scope="col">Numero de muestra</th>
                    <th scope="col">Tipo de prueba</th>
                    <th scope="col">Técnico/a</th>
                    <th scope="col">Fecha de inicio</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($Seach as $Seach): ?>
                    <tr>
                      <td>
                        <?php echo count_id(); ?>
                      </td>
                      <td>
                        <input type="checkbox" name="selected_samples[]" value="<?php echo $Seach['id']; ?>">
                      </td>
                      <td>
                        <?php echo $Seach['Sample_Name']; ?>
                      </td>
                      <td>
                        <?php echo $Seach['Sample_Number']; ?>
                      </td>
                      <td>
                        <?php echo $Seach['Test_Type']; ?>
                      </td>
                      <td>
                        <input type="text" class="form-control" name="Technician[<?php echo $Seach['id']; ?>]" autocomplete="off" value="<?php echo $Seach['Technician']; ?>">
                      </td>
                      <td>
                        <?php echo $Seach['Start_Date']; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table -->

              <button type="submit" class="btn btn-success mt-2" name="SendMultipleRealization">
                <i class="bi bi-send-check me-1"></i>
              </button>
              <button type="submit" class="btn btn-primary mt-2" name="update_multiple">
                <i class="bi bi-pencil"></i>
              </button>
              <button type="submit" class="btn btn-danger mt-2" name="delete_multiple">
                <i class="bi bi-trash"></i>
              </button>
            </form>

          </div>
        </div>
      </div>
      <!-- END tabla de muestra en preparacion para el envio a realizacion -->

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>