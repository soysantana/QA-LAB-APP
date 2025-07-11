<?php
$page_title = 'Editar Reporte Diario';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
if (!$fecha) {
  $session->msg('d', 'Fecha no especificada.');
  redirect('reporte_diario_lab.php');
}

$registros = find_by_sql("SELECT * FROM ensayos_reporte WHERE DATE(Report_Date) = '{$fecha}'");
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Editar Reporte Diario - <?= date('d-m-Y', strtotime($fecha)) ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item active">Editar Reporte Diario</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <?php echo display_msg($msg); ?>

    <div class="alert alert-info">
      Fecha buscada: <?= $fecha ?> | Total registros encontrados: <?= count($registros) ?>
    </div>

    <?php if (count($registros) > 0): ?>
    <form method="POST" class="row g-3 needs-validation" novalidate>
      <input type="hidden" name="fecha" value="<?= $fecha ?>">
      <div id="samples-container">
        <?php foreach ($registros as $index => $row): ?>
          <div class="card sample-card mb-4">
            <div class="card-body row g-3">
              <h5 class="card-title d-flex justify-content-between align-items-center">
                Muestra <?= $index + 1 ?>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarMuestra(<?= $row['id'] ?>)">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </h5>

              <input type="hidden" name="ids[]" value="<?= $row['id'] ?>">

              <div class="col-md-6">
                <label class="form-label">Sample Name</label>
                <input type="text" class="form-control" name="Sample_ID[]" value="<?= $row['Sample_ID'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Sample Number</label>
                <input type="text" class="form-control" name="Sample_Number[]" value="<?= $row['Sample_Number'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Structure</label>
                <input type="text" class="form-control" name="Structure[]" value="<?= $row['Structure'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Material Type</label>
                <input type="text" class="form-control" name="Material_Type[]" value="<?= $row['Material_Type'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Test Type</label>
                <input type="text" class="form-control" name="Test_Type[]" value="<?= $row['Test_Type'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Test Condition</label>
                <input type="text" class="form-control" name="Test_Condition[]" value="<?= $row['Test_Condition'] ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="Date[]" value="<?= date('Y-m-d', strtotime($row['Report_Date'])) ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Comments</label>
                <textarea class="form-control" name="Comments[]" rows="2"><?= $row['Comments'] ?></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Observación / No Conformidad</label>
                <textarea class="form-control" name="noconformidad[]" rows="2"><?= $row['Noconformidad'] ?></textarea>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary" name="update-form">
          <i class="bi bi-save"></i> Guardar Cambios
        </button>
        <a href="reporte_diario_lab.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
    <?php else: ?>
      <div class="alert alert-warning">
        No se encontraron registros para la fecha seleccionada.
      </div>
    <?php endif; ?>

    <!-- Formulario oculto para eliminar -->
    <form id="deleteForm" method="POST" style="display: none;">
      <input type="hidden" name="delete_id" id="delete_id">
      <input type="hidden" name="fecha" value="<?= $fecha ?>">
      <input type="hidden" name="delete-sample" value="1">
    </form>
  </section>
</main>

<script>
function eliminarMuestra(id) {
  if (confirm("¿Estás seguro de eliminar esta muestra?")) {
    document.getElementById("delete_id").value = id;
    document.getElementById("deleteForm").submit();
  }
}
</script>

<?php include_once('../components/footer.php'); ?>

<?php
// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-form'])) {
  global $db;
  $db->db_connect();

  $ids             = $_POST['ids'] ?? [];
  $sample_names    = $_POST['Sample_ID'] ?? [];
  $sample_numbers  = $_POST['Sample_Number'] ?? [];
  $structures      = $_POST['Structure'] ?? [];
  $material_types  = $_POST['Material_Type'] ?? [];
  $test_types      = $_POST['Test_Type'] ?? [];
  $test_conditions = $_POST['Test_Condition'] ?? [];
  $dates           = $_POST['Date'] ?? [];
  $comments        = $_POST['Comments'] ?? [];
  $noconformidad   = $_POST['noconformidad'] ?? [];

  for ($i = 0; $i < count($ids); $i++) {
    $id             = (int)$ids[$i];
    $sample_name    = $db->escape($sample_names[$i]);
    $sample_number  = $db->escape($sample_numbers[$i]);
    $structure      = $db->escape($structures[$i]);
    $material_type  = $db->escape($material_types[$i]);
    $test_type      = $db->escape($test_types[$i]);
    $test_condition = $db->escape($test_conditions[$i]);
    $date           = $db->escape($dates[$i]);
    $comment        = $db->escape($comments[$i]);
    $noconf         = $db->escape($noconformidad[$i]);

    $sql = "UPDATE ensayos_reporte SET
              Sample_ID = '{$sample_name}',
              Sample_Number = '{$sample_number}',
              Structure = '{$structure}',
              Material_Type = '{$material_type}',
              Test_Type = '{$test_type}',
              Test_Condition = '{$test_condition}',
              Comments = '{$comment}',
              Noconformidad = '{$noconf}',
              Report_Date = '{$date}'
            WHERE id = {$id}";

    $db->query($sql);
  }

  $session->msg('s', 'Reporte diario actualizado correctamente.');
 redirect("../pages/reporte_diario_lab.php", false);

}

// Eliminar muestra individual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-sample'])) {
  global $db;
  $db->db_connect();

  $id = (int)$_POST['delete_id'];
  $fecha = $_POST['fecha'] ?? date('Y-m-d');

  $sql = "DELETE FROM ensayos_reporte WHERE id = {$id}";

  if ($db->query($sql)) {
    $session->msg('s', 'Muestra eliminada correctamente.');
  } else {
    $session->msg('d', 'Error al eliminar la muestra.');
  }

  redirect("../pages/reporte_diario_lab.php", false);
}
?>
