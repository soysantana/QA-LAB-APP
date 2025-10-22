<?php
require_once('../config/load.php');
$page_title = 'Moisture Content Scale';
$Search = find_by_id('moisture_scale', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_mc_scale'])) {
    include('../database/moisture-content/mc-scale/update.php');
  } elseif (isset($_POST['repeat_mc_scale'])) {
    include('../database/moisture-content/mc-scale/repeat.php');
  } elseif (isset($_POST['reviewed_mc_scale'])) {
    include('../database/moisture-content/mc-scale/reviewed.php');
  } elseif (isset($_POST['delete_mc_scale'])) {
    include('../database/moisture-content/mc-scale/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Moisture Content Scale</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Moisture Content</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="moisture-scale.php?id=<?php echo $Search['id']; ?>" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <?php include_once('../includes/sample-info-form.php'); ?>
        <!-- End Sample Information -->

        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <!-- Multi Columns Form -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" id="Technician" name="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" id="DateTesting" name="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" id="Comments" name="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Tare Name</th>
                    <td><input type="text" style="border: none;" class="form-control" id="TareName" name="TareName" value="<?php echo ($Search['Tare_Name']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Moisture Content (%)</th>
                    <td><input type="text" style="border: none;" class="form-control" id="Moisture" name="Moisture" value="<?php echo ($Search['Moisture_Content_Porce']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>
        </div>


        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="update_mc_scale">Update Essay</button>
                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
              <ul class="dropdown-menu">
  <li><button class="dropdown-item" type="button" onclick="guardarPDF('MC-Scale-Naranjo','<?= $Search['id'] ?>')">Naranjo</button></li>
  <li><button class="dropdown-item" type="button" onclick="guardarPDF('MC-Scale-Build','<?= $Search['id'] ?>')">Construcción</button></li>
</ul>

                </div>
                <button type="submit" class="btn btn-danger" name="delete_mc_scale"><i class="bi bi-trash"></i></button>
              </div>

              <div class="btn-group mt-2" role="group">
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="repeat_mc_scale">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="reviewed_mc_scale">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->
<script>
async function guardarPDF(template, id) {
  try {
    const res = await fetch(`../pdf/${template}.php?id=${encodeURIComponent(id)}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({}) // si no envías imágenes, deja {}
    });
    const data = await res.json();
    if (!res.ok || !data?.ok) throw new Error(data?.error || 'No se pudo guardar el PDF.');
    const msg = document.getElementById('mensaje-container');
    if (msg) msg.innerHTML = `<div class="alert alert-success">PDF guardado en el servidor.<br><small>${data.filename}</small></div>`;
    else alert('PDF guardado: ' + data.filename);
  } catch (err) {
    const msg = document.getElementById('mensaje-container');
    if (msg) msg.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
    else alert('Error: ' + err.message);
  }
}
</script>

<?php include_once('../components/footer.php');  ?>