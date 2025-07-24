<?php
$page_title = 'Reactivity';
require_once('../config/load.php');
$Search = find_by_id('reactivity', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['UpdateReactivity'])) {
    include('../database/reactivity/update.php');
  } elseif (isset($_POST['RepeatReactivity'])) {
    include('../database/reactivity/repeat.php');
  } elseif (isset($_POST['ReviewedReactivity'])) {
    include('../database/reactivity/reviewed.php');
  } elseif (isset($_POST['DeleteReactivity'])) {
    include('../database/reactivity/delete.php');
  }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reactivity Fine Particles</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Reactivity</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row" onchange="reactivityTest('FM13-006')">

      <form class="row" action="reactivity-fine.php?id=<?php echo $Search['id']; ?>" method="post">

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
                <div class="col-md-4">
                  <label for="Standard" class="form-label">Standard</label>
                  <select id="Standard" class="form-select" name="Standard">
                    <option selected>Choose...</option>
                    <option <?php if ($Search['Standard'] == 'FM13-006') echo 'selected'; ?>>FM13-006</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="Technician" class="form-label">Technician</label>
                  <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                </div>
                <div class="col-md-4">
                  <label for="DateTesting" class="form-label">Date of Testing</label>
                  <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
                </div>
                <div class="col-12">
                  <label for="Comments" class="form-label">Comments</label>
                  <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
                </div>
              </div><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

        <?php $ParticlesReactiveValues = explode(',', $Search["ParticlesReactive"]); ?>

        <div class="col-lg-8">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>

              <h5 class="card-title">Reactivity Test Method FM13-006</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Weight used for the Test (g):</th>
                    <td><input type="text" style="border: none;" class="form-control" name="WeigtTest" id="WeigtTest" value="<?php echo ($Search['WeigtTest']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">A Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles1" id="Particles1" value="<?php echo isset($ParticlesReactiveValues[0]) ? trim($ParticlesReactiveValues[0]) : ''; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">B Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles2" id="Particles2" value="<?php echo isset($ParticlesReactiveValues[1]) ? trim($ParticlesReactiveValues[1]) : ''; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">C Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles3" id="Particles3" value="<?php echo isset($ParticlesReactiveValues[2]) ? trim($ParticlesReactiveValues[2]) : ''; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">D Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles4" id="Particles4" value="<?php echo isset($ParticlesReactiveValues[3]) ? trim($ParticlesReactiveValues[3]) : ''; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">E Particles Reactive #:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="Particles5" id="Particles5" value="<?php echo isset($ParticlesReactiveValues[4]) ? trim($ParticlesReactiveValues[4]) : ''; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Average Particles Reactive:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AvgParticles" id="AvgParticles" readonly tabindex="-1" value="<?php echo ($Search['AvgParticles']); ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row">Reaction Strength Result:</th>
                    <td><input type="text" style="border: none;" class="form-control" name="ReactionResult" id="ReactionResult" readonly tabindex="-1" value="<?php echo ($Search['ReactionResult']); ?>"></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Bordered Table -->

              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Acid Reactivity Test Result</th>
                    <td><input type="text" style="border: none;" class="form-control" name="AcidResult" id="AcidResult" readonly tabindex="-1" value="<?php echo ($Search['AcidReactivityResult']); ?>"></td>
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
                <button type="submit" class="btn btn-success" name="UpdateReactivity" value="FM13-006">Update</button>
                <button type="submit" class="btn btn-danger" name="DeleteReactivity"><i class="bi bi-trash"></i></button>
                <div class="btn-group dropup" role="group">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../pdf/AR-FF.php?id=<?php echo ($Search['id']); ?>">PDF</a></li>
                  </ul>
                </div>
                <?php if (user_can_access(1)): ?>
                  <button type="submit" class="btn btn-primary" name="RepeatReactivity">Repeat</button>
                  <button type="submit" class="btn btn-primary" name="ReviewedReactivity">Reviewed</button>
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>

      </form>

    </div>
  </section>

</main><!-- End #main -->

<script src="../js/reactivity.js"></script>
<?php include_once('../components/footer.php');  ?>