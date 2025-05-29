<?php
$page_title = 'Brazilian';
require_once('../config/load.php');
$Search = find_by_id('brazilian', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['Update_Brazilian'])) {
    include('../database/brazilian.php');
  } elseif (isset($_POST['Repeat_Brazilian'])) {
    include('../database/brazilian.php');
  } elseif (isset($_POST['Reviewed_Brazilian'])) {
    include('../database/brazilian.php');
  } elseif (isset($_POST['delete_bts'])) {
    include('../database/brazilian.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Brazilian</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Brazilian</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="brazilian.php?id=<?php echo $Search['id']; ?>" method="post" enctype="multipart/form-data">

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
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'ASTM-D3967') echo 'selected'; ?>>ASTM-D3967</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" id="TestMethod" name="TestMethod" value="<?php echo ($Search['Methods']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="ExEquip" class="form-label">Extraction Equipment:</label>
                  <input type="text" class="form-control" id="ExEquip" name="ExEquip" value="<?php echo ($Search['Extraction_Equipment']); ?>">
                </div>
                <div class="col-md-6">
                  <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
                  <input type="text" class="form-control" id="CutterEquip" name="CutterEquip" value="<?php echo ($Search['Cutter_Equipment']); ?>">
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
                  <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-10">

          <div class="card" oninput="BTT()">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col" colspan="9">Splittling Tensile Strenggth of Rock Core Specimen - Brazilian</th>
                  </tr>
                  <tr>
                    <th scope="col">Samples</th>
                    <th scope="col">Diameter D(cm)</th>
                    <th scope="col">Thicness t(cm)</th>
                    <th scope="col">Relation t/d</th>
                    <th scope="col">Loading rate (KN/s)</th>
                    <th scope="col">Time to Failure (s)</th>
                    <th scope="col">Max. Load (kN)</th>
                    <th scope="col">Tensile Strength (Mpa)</th>
                    <th scope="col">Failure type</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $datos = array(
                    array("Sample 1", "DcmNo1", "TcmNo1", "ReltdNo1", "LoandNo1", "TimeFaiNo1", "MaxKnNo1", "TensStrNo1", "FailureNo1"),
                    array("Sample 2", "DcmNo2", "TcmNo2", "ReltdNo2", "LoandNo2", "TimeFaiNo2", "MaxKnNo2", "TensStrNo2", "FailureNo2"),
                    array("Sample 3", "DcmNo3", "TcmNo3", "ReltdNo3", "LoandNo3", "TimeFaiNo3", "MaxKnNo3", "TensStrNo3", "FailureNo3"),
                    array("Sample 4", "DcmNo4", "TcmNo4", "ReltdNo4", "LoandNo4", "TimeFaiNo4", "MaxKnNo4", "TensStrNo4", "FailureNo4"),
                    array("Sample 5", "DcmNo5", "TcmNo5", "ReltdNo5", "LoandNo5", "TimeFaiNo5", "MaxKnNo5", "TensStrNo5", "FailureNo5"),
                    array("Sample 6", "DcmNo6", "TcmNo6", "ReltdNo6", "LoandNo6", "TimeFaiNo6", "MaxKnNo6", "TensStrNo6", "FailureNo6"),
                    array("Sample 7", "DcmNo7", "TcmNo7", "ReltdNo7", "LoandNo7", "TimeFaiNo7", "MaxKnNo7", "TensStrNo7", "FailureNo7"),
                    array("Sample 8", "DcmNo8", "TcmNo8", "ReltdNo8", "LoandNo8", "TimeFaiNo8", "MaxKnNo8", "TensStrNo8", "FailureNo8"),
                    array("Sample 9", "DcmNo9", "TcmNo9", "ReltdNo9", "LoandNo9", "TimeFaiNo9", "MaxKnNo9", "TensStrNo9", "FailureNo9"),
                    array("Sample 10", "DcmNo10", "TcmNo10", "ReltdNo10", "LoandNo10", "TimeFaiNo10", "MaxKnNo10", "TensStrNo10", "FailureNo10"),
                    array("Average", "DcmNoAvge", "TcmNoAvge", "ReltdNoAvge", "LoandNoAvge", "TimeFaiNoAvge", "MaxKnNoAvge", "TensStrNoAvge", "FailureNoAvge"),
                    // Puedes agregar más filas según sea necesario
                  );

                  foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ($fila as $index => $valor) {
                      if ($index < 1) {
                        echo '<th scope="row">' . $valor . '</th>';
                      } elseif (strpos($valor, "FailureNo") !== false) {
                        // Si el valor contiene "FailureNo", imprimir un campo select
                        echo '<td><select class="form-select" name="' . $valor . '">';
                        echo '<option select>Choose...</option>';
                        echo '<option ' . ($Search[$valor] == 'Central' ? 'selected' : '') . '>Central</option>';
                        echo '<option ' . ($Search[$valor] == 'Central+Layer activation' ? 'selected' : '') . '>Central+Layer activation</option>';
                        echo '<option ' . ($Search[$valor] == 'Non-central' ? 'selected' : '') . '>Non-central</option>';
                        echo '<option ' . ($Search[$valor] == 'Central multiple' ? 'selected' : '') . '>Central multiple</option>';
                        echo '</select></td>';
                      } else {
                        $readonly = (strpos($valor, "ReltdNo") !== false || strpos($valor, "LoandNo") !== false || strpos($valor, "TensStrNo") !== false ||
                          strpos($valor, "DcmNoAvge") !== false || strpos($valor, "TcmNoAvge") !== false || strpos($valor, "ReltdNoAvge") !== false ||
                          strpos($valor, "LoandNoAvge") !== false || strpos($valor, "TimeFaiNoAvge") !== false || strpos($valor, "MaxKnNoAvge") !== false ||
                          strpos($valor, "TensStrNoAvge") !== false || strpos($valor, "FailureNoAvge") !== false) ? 'readonly tabindex="-1"' : '';
                        echo '<td><input type="text" style="border: none;" class="form-control" id="' . $valor . '" name="' . $valor . '" value="' . $Search[$valor] . '" ' . $readonly . '></td>';
                      }
                    }
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->
            </div>
          </div>

        </div>


        <div class="col-lg-2">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="Update_Brazilian">Update Essay</button>
                <a href="../pdf/BTS-Naranjo.php?id=<?php echo $Search['id']; ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
                <button type="submit" class="btn btn-danger" name="delete_bts"><i class="bi bi-trash"></i></button>
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="Repeat_Brazilian">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="Reviewed_Brazilian">Reviewed</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Specimen Before Test</h5>
              <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
              <div id="imageContainerBefore" class="image-container mt-3">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['SpecimenBefore']); ?>" alt="SpecimenBefore" class="img-fluid" width="300px">
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Specimen After Test</h5>
              <input class="form-control" type="file" name="SpecimenAfter" onchange="showImage(this, 'after')" />
              <div id="imageContainerAfter" class="image-container mt-3">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($Search['SpecimenAfter']); ?>" alt="SpecimenAfter" class="img-fluid" width="300px">
              </div>
            </div>
          </div>
        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/Brazilian.js"></script>
<?php include_once('../components/footer.php');  ?>