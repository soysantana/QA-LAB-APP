<?php
$page_title = 'Muestra en realizacion';
$tracking_show = 'show';
$class_tracking = ' ';
$realization = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete_realization'])) {
    include('../database/sample-tracking/realization/delete.php');
  } elseif (isset($_POST['send_delivery'])) {
    include('../database/sample-tracking/delivery/save.php');
  } elseif (isset($_POST['update_multiple'])) {
    include('../database/sample-tracking/realization/update.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestra en realizacion</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Formularios</li>
        <li class="breadcrumb-item active">Muestra en realizacion</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">

      <!-- tabla de muestra en realizacion para el envio a entrega -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">LISTA DE MUESTRAS EN REALIZACION</h5>

            <?php $week = date('Y-m-d', strtotime('-300 days')); ?>
            <?php $realization = "(SELECT 1 FROM test_delivery WHERE sample_name = p.sample_name AND sample_number = p.sample_number AND test_type = p.test_type)"; ?>
            <?php $Seach = find_by_sql("SELECT id, Sample_Name, Sample_Number, Test_Type, Technician, Start_Date FROM test_realization p WHERE Start_Date >= '{$week}' AND NOT EXISTS $realization ORDER BY Register_Date DESC"); ?>

            <form id="multiple-send-form" method="post" action="test-realization.php">
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

              <button type="submit" class="btn btn-success mt-2" name="send_delivery">
                <i class="bi bi-send-check me-1"></i>
              </button>
              <button type="submit" class="btn btn-primary mt-2" name="update_multiple">
                <i class="bi bi-pencil"></i>
              </button>
              <button type="submit" class="btn btn-danger mt-2" name="delete_realization">
                <i class="bi bi-trash"></i>
              </button>
            </form>

          </div>
        </div>
      </div>
      <!-- END tabla de muestra en realizacion para el envio a entrega -->

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>