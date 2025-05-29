<?php
$page_title = 'Muestra repetida';
$tracking_show = 'show';
$class_tracking = ' ';
$repeat = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['send-delivery'])) {
    include('../database/sample-tracking/repeat/entregar_muestra.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php'); ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Muestra repetida</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Formularios</li>
      <li class="breadcrumb-item active">Muestra repetida</li>
    </ol>
  </nav>
</div>

<div class="col-md-4"><?php echo display_msg($msg); ?></div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">LISTA DE MUESTRAS EN REPETICIÓN</h5>
          <?php
          $reviewed_check = "(SELECT 1 FROM test_reviewed trw WHERE trw.Sample_Name = p.Sample_Name AND trw.Sample_Number = p.Sample_Number AND trw.Test_Type = p.Test_Type AND trw.Signed = 1)";
          $Search = find_by_sql("SELECT * FROM test_repeat p WHERE NOT EXISTS $reviewed_check ORDER BY Start_Date DESC");
          ?>
          <table class="table table-bordered datatable">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre de la muestra</th>
                <th>Número de muestra</th>
                <th>Tipo de prueba</th>
                <th>Registrado por</th>
                <th>Fecha de inicio</th>
                <th>Comentario</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($Search as $index => $item): ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($item['Sample_Name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($item['Sample_Number'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($item['Test_Type'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($item['Register_By'] ?? ''); ?></td>
                  <td><?php echo date('Y-m-d', strtotime($item['Start_Date'])); ?></td>
                  <td><?php echo htmlspecialchars($item['Comment'] ?? ''); ?></td>
                  <td>
                    <form method="POST" action="test-repeat.php">
                      <input type="hidden" name="Sample_Name" value="<?php echo htmlspecialchars($item['Sample_Name']); ?>">
                      <input type="hidden" name="Sample_Number" value="<?php echo htmlspecialchars($item['Sample_Number']); ?>">
                      <input type="hidden" name="Test_Type" value="<?php echo htmlspecialchars($item['Test_Type']); ?>">
                      <input type="hidden" name="Register_By" value="<?php echo htmlspecialchars($item['Register_By']); ?>">
                      <input type="hidden" name="Start_Date" value="<?php echo htmlspecialchars($item['Start_Date']); ?>">
                      <input type="hidden" name="Comment" value="<?php echo htmlspecialchars($item['Comment']); ?>">
                     <button type="submit" class="btn btn-success" name="send-delivery"><i class="bi bi-send me-1"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

</main>

<?php include_once('../components/footer.php'); ?>
