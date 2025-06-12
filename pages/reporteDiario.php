<?php
$page_title = 'Registro de Reporte Diario';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Reporte Diario Laboratorio</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item active">Reporte Diario</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <?php echo display_msg($msg); ?>

    <form method="POST" class="row g-3 needs-validation" novalidate>
      <div id="samples-container">
        <div class="card sample-card mb-4">
          <div class="card-body row g-3">
            <h5 class="card-title">Test Status</h5>
            <div class="col-md-6">
              <label class="form-label">Sample Name</label>
              <input type="text" class="form-control" name="Sample_Name[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Sample Number</label>
              <input type="text" class="form-control" name="Sample_Number[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Structure</label>
              <input type="text" class="form-control" name="Structure[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Material Type</label>
              <input type="text" class="form-control" name="Material_Type[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Test Type</label>
              <input type="text" class="form-control" name="Test_Type[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Test Condition</label>
              <input type="text" class="form-control" name="Test_Condition[]" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="Date[]">
            </div>
            <div class="col-12">
              <label class="form-label">Comments</label>
              <textarea class="form-control" name="Comments[]" rows="3"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary" onclick="duplicateSample()">
          <i class="bi bi-plus-square"></i> Añadir otra muestra
        </button>
        <button type="submit" class="btn btn-success" name="requisition-form">Guardar Reporte</button>
      </div>
    </form>
  </section>
</main>

<script>
function duplicateSample() {
  const container = document.getElementById('samples-container');
  const original = document.querySelector('.sample-card');
  const clone = original.cloneNode(true);
  const inputs = clone.querySelectorAll('input, textarea');
  inputs.forEach(input => input.value = '');
  container.appendChild(clone);
}
</script>

<?php include_once('../components/footer.php'); ?>

<?php
// Guardar en la base de datos después del cierre del HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  global $db;
  $db->db_connect(); // Asegura conexión activa

  $sample_names     = $_POST['Sample_Name'] ?? [];
  $sample_numbers   = $_POST['Sample_Number'] ?? [];
  $structures       = $_POST['Structure'] ?? [];
  $material_types   = $_POST['Material_Type'] ?? [];
  $test_types       = $_POST['Test_Type'] ?? [];
  $test_conditions  = $_POST['Test_Condition'] ?? [];
  $comments_list    = $_POST['Comments'] ?? [];

  $report_date = $_POST['Date'] ?? [];

  for ($i = 0; $i < count($sample_names); $i++) {
    $sample_name     = $db->escape($sample_names[$i]);
    $sample_number   = $db->escape($sample_numbers[$i]);
    $structure       = $db->escape($structures[$i]);
    $material_type   = $db->escape($material_types[$i]);
    $test_type       = $db->escape($test_types[$i]);
    $test_condition  = $db->escape($test_conditions[$i]);
    $comments        = $db->escape($comments_list[$i]);

    $sql = "INSERT INTO ensayos_reporte (
              Sample_Number, Sample_Name, Structure, Material_Type,
              Test_Type, Test_Condition, Comments, Report_Date
            ) VALUES (
              '{$sample_number}', '{$sample_name}', '{$structure}', '{$material_type}',
              '{$test_type}', '{$test_condition}', '{$comments}', '{$report_date}'
            )";

    $db->query($sql);
  }

  $session->msg('s', 'Todas las muestras fueron guardadas exitosamente.');
  redirect('../pages/reporteDiario.php', false);
}
?>
