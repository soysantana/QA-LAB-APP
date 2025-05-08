<?php
$page_title = 'Reactivity';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['save_reactivity'])) {
    include('../database/reactivity/ar/save.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reactivity Coarse Filter</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Reactivity</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row" oninput="Reactivity()">

      <form class="row" action="reactivity.php" method="post">

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
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option value="ASTM-C136">ASTM-C136</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="PMethods" class="form-label">Preparation Methods</label>
                  <select id="PMethods" class="form-select" name="PMethods">
                    <option selected>Choose...</option>
                    <option value="Oven Dried">Oven Dried</option>
                    <option value="Air Dried">Air Dried</option>
                    <option value="Microwave Dried">Microwave Dried</option>
                    <option value="Wet">Wet</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="SMethods" class="form-label">Split Methods</label>
                  <select id="SMethods" class="form-select" name="SMethods">
                    <option selected>Choose...</option>
                    <option value="Manual">Manual</option>
                    <option value="Mechanical">Mechanical</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician">
                </div>
                <div class="col-md-6">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>

              <h5 class="card-title">Reactivity Test Method FM13-007</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Total Sample Weight (g):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="TotalWeight" id="TotalWeight"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight used for the Test (g):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeigtTest" id="WeigtTest"></td>
                  </tr>
                  <tr>
                    <th scope="row">A Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles1" id="Particles1"></td>
                  </tr>
                  <tr>
                    <th scope="row">B Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles2" id="Particles2"></td>
                  </tr>
                  <tr>
                    <th scope="row">C Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles3" id="Particles3"></td>
                  </tr>
                  <tr>
                    <th scope="row">Weight Mat. Ret. No. 4 (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightNo4" id="WeightNo4"></td>
                  </tr>
                  <tr>
                    <th scope="row">Wt Reactive Part. Ret. No.4 (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeightReactiveNo4" id="WeightReactiveNo4"></td>
                  </tr>
                  <tr>
                    <th scope="row">Percent Reactive Particles (If Applicable)</th>
                    <td><input type="text" style="border: none;" class="form-control" name="PercentReactive" id="PercentReactive"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average Particles Reactive:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AvgParticles" id="AvgParticles" readonly tabindex="-1"></td>
                  </tr>
                  <tr>
                    <th scope="row">Reaction Strength Result:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ReactionResult" id="ReactionResult" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Acid Reactivity Test Result</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AcidResult" id="AcidResult" readonly tabindex="-1"></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <div class="col-lg-4">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actions</h5>
              <!-- Actions Buttons -->
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-success" name="save_reactivity">Save Essay</button>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>