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

        <div id="product_info">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Sample Information</h5>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label for="ProjectName" class="form-label">Project Name</label>
                    <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo ($Search['Project_Name']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Client" class="form-label">Client</label>
                    <input type="text" class="form-control" name="Client" id="Client" value="<?php echo ($Search['Client']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="ProjectNumber" class="form-label">Project Number</label>
                    <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo ($Search['Project_Number']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Structure" class="form-label">Structure</label>
                    <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo ($Search['Structure']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Area" class="form-label">Work Area</label>
                    <input type="text" class="form-control" name="Area" id="Area" value="<?php echo ($Search['Area']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Source" class="form-label">Borrow Source</label>
                    <input type="text" class="form-control" name="Source" id="Source" value="<?php echo ($Search['Source']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="MType" class="form-label">Material Type</label>
                    <input type="text" class="form-control" name="MType" id="MType" value="<?php echo ($Search['Material_Type']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="SType" class="form-label">Sample Type</label>
                    <input type="text" class="form-control" name="SType" id="SType" value="<?php echo ($Search['Sample_Type']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="SampleName" class="form-label">Sample Name</label>
                    <input type="text" class="form-control" name="SampleName" id="SampleName" value="<?php echo ($Search['Sample_ID']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="SampleNumber" class="form-label">Sample Number</label>
                    <input type="text" class="form-control" name="SampleNumber" id="SampleNumber" value="<?php echo ($Search['Sample_Number']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Sample Date" class="form-label">Sample Date</label>
                    <input type="text" class="form-control" name="CollectionDate" id="Sample Date" value="<?php echo ($Search['Sample_Date']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="SampleBy" class="form-label">Sample By</label>
                    <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo ($Search['Sample_By']); ?>">
                  </div>
                  <div class="col-md-4">
                    <label for="Depth From" class="form-label">Depth From</label>
                    <input type="text" class="form-control" name="DepthFrom" id="Depth From" value="<?php echo ($Search['Depth_From']); ?>">
                  </div>
                  <div class="col-md-2">
                    <label for="Depth To" class="form-label">Depth To</label>
                    <input type="text" class="form-control" name="DepthTo" id="Depth To" value="<?php echo ($Search['Depth_To']); ?>">
                  </div>
                  <div class="col-md-2">
                    <label for="North" class="form-label">North</label>
                    <input type="text" class="form-control" name="North" id="North" value="<?php echo ($Search['North']); ?>">
                  </div>
                  <div class="col-md-2">
                    <label for="East" class="form-label">East</label>
                    <input type="text" class="form-control" name="East" id="East" value="<?php echo ($Search['East']); ?>">
                  </div>
                  <div class="col-md-2">
                    <label for="Elevation" class="form-label">Elevation</label>
                    <input type="text" class="form-control" name="Elev" id="Elevation" value="<?php echo ($Search['Elev']); ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

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