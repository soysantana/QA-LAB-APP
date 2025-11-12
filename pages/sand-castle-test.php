<?php
$page_title = 'Sand Castle Test';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['save'])) {
    include('../database/sand-castle/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Sand Castle Test (SCT)</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Forms</a></li>
        <li class="breadcrumb-item active">Sand Castle</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <form class="row" action="sand-castle-test.php" method="post">

        <div class="col-md-4">
          <?php echo display_msg($msg); ?>
        </div>

        <!-- Sample Information -->
        <div id="product_info"></div>
        <!-- End Sample Information -->

        <!-- Test Information -->
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Trial Information</h5>

              <div class="row g-3">
                <div class="col-md-3">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option value="PV-15-10">PV-15-10</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried">Oven Dried</option>
                    <option value="Air Dried">Air Dried</option>
                    <option value="Microwave Dried">Microwave Dried</option>
                    <option value="Wet">Wet</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual">Manual</option>
                    <option value="Mechanical">Mechanical</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="TestMethod" class="form-label">Test Method</label>
                  <input type="text" class="form-control" name="TestMethod" id="TestMethod">
                </div>
                <div class="col-md-3">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" required>
                </div>
                <div class="col-md-3">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" required>
                </div>
                <div class="col-md-3">
                  <label for="natMc" class="form-label">Natural MC (%)</label>
                  <input type="text" class="form-control" name="natMc" id="natMc">
                </div>
                <div class="col-md-3">
                  <label for="optimunMc" class="form-label">Optimun MC (%)</label>
                  <input type="text" class="form-control" name="optimunMc" id="optimunMc">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
                </div>
              </div>

            </div>
          </div>

        </div>
        <!-- End Test Information -->

        <!-- Collapsed -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Test Results</h5>

              <table class="table table-bordered" id="collapsedTable">
                <tbody>
                  <tr>
                    <th scope="row">Times of:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Time" id="Time"></td>
                  </tr>
                  <tr>
                    <th scope="row">1st Collapsed</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Collapsed[]" id="Collapsed_1"></td>
                  </tr>
                  <tr id="timeSetRow">
                    <th scope="row">Time of Set (Min):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TimeSet" id="TimeSet" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>

              <button type="button" class="btn btn-secondary" id="addCollapsed">+ Agregar Collapsed</button>
            </div>
          </div>
        </div>
        <!-- End Collapsed -->

        <!-- Approximate & Final Height -->
        <div class="col-lg-4">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Approximate Initial Height (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="initialHeight" id="initialHeight"></td>
                  </tr>
                  <tr>
                    <th scope="row">(A) Final Height (mm)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="FinalHeight" id="FinalHeight"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>

          <!-- Spread Redius Measures & Slope Angle Calculation  -->
          <div class="">

            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Spread Radius Measures</h5>

                <table class="table table-bordered">
                  <thead>
                    <th class="text-center">Measure No.</th>
                    <th class="text-center">mm</th>
                  </thead>
                  <tbody>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                      echo '<tr>';
                      echo '<th class="text-center" scope="row">' . $i . '</th>';
                      echo '<td><input type="text" style="border: none;" class="text-center form-control" name="Radius' . $i . '" id="Radius' . $i . '"></td>';
                      echo '</tr>';
                    }

                    ?>
                  </tbody>
                </table>

                <h5 class="card-title">Slope Angle Calculation </h5>

                <table class="table table-bordered">
                  <thead>
                    <th class="text-center">Angle No.</th>
                    <th class="text-center">Degrees</th>
                  </thead>
                  <tbody>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                      echo '<tr>';
                      echo '<th class="text-center" scope="row">' . $i . '</th>';
                      echo '<td><input type="text" style="border: none;" class="text-center form-control" name="Angle' . $i . '" id="Angle' . $i . '" readonly tabindex="-1"></td>';
                      echo '</tr>';
                    }

                    ?>
                    <th class="text-center" scope="row">Average</th>
                    <td><input type="text" style="border: none;" class="text-center form-control" name="Average" id="Average" readonly tabindex="-1"></td>
                  </tbody>
                </table>


              </div>
            </div>

          </div>
          <!-- End Spread Redius Measures & Slope Angle Calculation -->

        </div>
        <!-- End Approximate & Final Height -->


        <!-- Actions Buttons -->
        <div class="col-lg-3">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="save">Guardar Sand Castle</button>
                <button type="button" class="btn btn-primary" name="search">Buscar Humedad</button>
                <div id="mensaje-container"></div>
              </div>

              <h5 class="card-title"></h5>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Test Result</th>
                    <td><input type="text" style="border: none;" class="form-control" name="testResult" id="testResult"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>

        </div>
        <!-- End Actions Buttons -->

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script type="module" src="../js/sand-castle/sand-castle-sct.js"></script>
<?php include_once('../components/footer.php');  ?>