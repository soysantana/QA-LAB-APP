<?php
$page_title = 'Editar Reporte Diario';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

// Obtener la fecha desde el GET
if (!isset($_GET['fecha'])) {
  $session->msg("d", "Fecha no especificada.");
  redirect('lista_reportes.php');
}

$fecha = $_GET['fecha'];
$db->db_connect();

// Obtener los registros del reporte
$ensayos = find_by_sql("SELECT * FROM ensayos_reporte WHERE Report_Date = '{$fecha}'");

?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Editar Reporte Diario - <?= date('d-m-Y', strtotime($fecha)) ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="lista_reportes.php">Lista de Reportes</a></li>
        <li class="breadcrumb-item active">Editar Reporte</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <?php echo display_msg($msg); ?>

    <form method="POST" class="row g-3 needs-validation" novalidate>
      <?php foreach ($ensayos as $i => $ensayo): ?>
      <input type="hidden" name="id[]" value="<?= $ensayo['id'] ?>">
      <div class="card mb-4">
        <div class="card-body row g-3">
          <h5 class="card-title">Muestra <?= $i + 1 ?></h5>
          <div class="col-md-6">
            <label class="form-label">Sample Name</label>
            <input type="text" class="form-control" name="Sample_Name[]" value="<?= $ensayo['Sample_Name'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Sample Number</label>
            <input type="text" class="form-control" name="Sample_Number[]" value="<?= $ensayo['Sample_Number'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Structure</label>
            <input type="text" class="form-control" name="Structure[]" value="<?= $ensayo['Structure'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Material Type</label>
            <input type="text" class="form-control" name="Material_Type[]" value="<?= $ensayo['Material_Type'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Test Type</label>
            <input type="text" class="form-control" name="Test_Type[]" value="<?= $ensayo['Test_Type'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Test Condition</label>
            <input type="text" class="form-control" name="Test_Condition[]" value="<?= $ensayo['Test_Condition'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="Date[]" value="<?= $ensayo['Report_Date'] ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Comments</label>
            <textarea class="form-control" name="Comments[]" rows="3"><?= $ensayo['Comments'] ?></textarea>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary" name="update">Actualizar Reporte</button>
        <a href="../pages/reporte_diario_lab.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $db->db_connect();

  $ids             = $_POST['id'];
  $sample_names    = $_POST['Sample_Name'];
  $sample_numbers  = $_POST['Sample_Number'];
  $structures      = $_POST['Structure'];
  $material_types  = $_POST['Material_Type'];
  $test_types      = $_POST['Test_Type'];
  $test_conditions = $_POST['Test_Condition'];
  $report_dates    = $_POST['Date'];
  $comments_list   = $_POST['Comments'];

  for ($i = 0; $i < count($ids); $i++) {
    $id             = $db->escape($ids[$i]);
    $sample_name    = $db->escape($sample_names[$i]);
    $sample_number  = $db->escape($sample_numbers[$i]);
    $structure      = $db->escape($structures[$i]);
    $material_type  = $db->escape($material_types[$i]);
    $test_type      = $db->escape($test_types[$i]);
    $test_condition = $db->escape($test_conditions[$i]);
    $date           = $db->escape($report_dates[$i]);
    $comments       = $db->escape($comments_list[$i]);

    if (!empty($date)) {
      $sql = "UPDATE ensayos_reporte SET
                Sample_Name = '{$sample_name}',
                Sample_Number = '{$sample_number}',
                Structure = '{$structure}',
                Material_Type = '{$material_type}',
                Test_Type = '{$test_type}',
                Test_Condition = '{$test_condition}',
                Comments = '{$comments}',
                Report_Date = '{$date}'
              WHERE id = '{$id}'";
      $db->query($sql);
    }
  }

  $session->msg("s", "Reporte actualizado correctamente.");
  redirect("../pages/editar_reporteDiario.php?fecha={$fecha}", false);
}
?>
