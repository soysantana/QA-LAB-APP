<?php
  $page_title = 'Brazilian';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
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

  <form class="row" action="../database/brazilian.php" method="post" enctype="multipart/form-data">
  
  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <div id="product_info"></div>

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
                <option value="ASTM-D3967">ASTM-D3967</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod" name="TestMethod">
            </div>
            <div class="col-md-6">
              <label for="ExEquip" class="form-label">Extraction Equipment:</label>
              <input type="text" class="form-control" id="ExEquip" name="ExEquip">
            </div>
            <div class="col-md-6">
              <label for="CutterEquip" class="form-label">Cutter Equipment:</label>
              <input type="text" class="form-control" id="CutterEquip" name="CutterEquip">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician" name="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting" name="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" style="height: 100px;" name="Comments"></textarea>
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
              array("Average", "DcmNoAvge", "TcmNoAvge", "ReltdNoAvge", "LoandNoAvge", "TimeFaiNoAvge", "MaxKnNoAvge", "TensStrNoAvge", "FailureAvge"),
              // Puedes agregar más filas según sea necesario
            );
            
            foreach ($datos as $fila) {
              echo '<tr>';
              foreach ($fila as $index => $valor) {
                if ($index < 1) {
                  echo '<th scope="row">' . $valor . '</th>';
                } elseif (strpos($valor, "FailureNo") !== false) {
                  // Si el valor contiene "FailureNo", imprimir un campo select
                  echo '<td><select class="form-select" name="'.$valor.'">';
                  echo '<option select>Choose...</option>';
                  echo '<option value="Central">Central</option>';
                  echo '<option value="Central+Layer activation">Central+Layer activation</option>';
                  echo '<option value="Non-central">Non-central</option>';
                  echo '<option value="Central multiple">Central multiple</option>';
                  echo '</select></td>';
                } else {
                  $readonly = (strpos($valor, "ReltdNo") !== false || strpos($valor, "LoandNo") !== false || strpos($valor, "TensStrNo") !== false ||
                  strpos($valor, "DcmNoAvge") !== false || strpos($valor, "TcmNoAvge") !== false || strpos($valor, "ReltdNoAvge") !== false ||
                  strpos($valor, "LoandNoAvge") !== false || strpos($valor, "TimeFaiNoAvge") !== false || strpos($valor, "MaxKnNoAvge") !== false ||
                  strpos($valor, "TensStrNoAvge") !== false || strpos($valor, "FailureAvge") !== false) ? 'readonly tabindex="-1"' : '';
                  echo '<td><input type="text" style="border: none;" class="form-control" id="' . $valor . '" name="' . $valor . '" ' . $readonly . '></td>';
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
        <button type="submit" name="brazilian" class="btn btn-success">Save Essay</button>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-5">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Specimen Before Test</h5>
      <input class="form-control" type="file" name="SpecimenBefore" onchange="showImage(this, 'before')" />
      <div id="imageContainerBefore" class="image-container mt-3"></div>
    </div>
  </div>
</div>

<div class="col-lg-5">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Specimen After Test</h5>
      <input class="form-control" type="file" name="SpecimenAfter" onchange="showImage(this, 'after')" />
      <div id="imageContainerAfter" class="image-container mt-3"></div>
    </div>
  </div>
</div>

  </form>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Brazilian.js"></script>
<?php include_once('../components/footer.php');  ?>