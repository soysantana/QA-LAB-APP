<?php
  $page_title = 'Standard Proctor';
  require_once('../config/load.php');
  $Search = find_by_id('standard_proctor', $_GET['id']);
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_sp'])) {
        include('../database/standard-proctor/update.php');
    } elseif (isset($_POST['repeat_sp'])) {
        include('../database/standard-proctor/repeat.php');
    } elseif (isset($_POST['reviewed_sp'])) {
        include('../database/standard-proctor/reviewed.php');
    } elseif (isset($_POST['delete_sp'])) {
        include('../database/standard-proctor/delete.php');
    }
  }
?>

<?php page_require_level(2); ?>
<?php get_user_review(); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Standard Proctor</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Standard Proctor</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row" oninput="SProctor()">

  <form class="row" action="standard-proctor.php?id=<?php echo $Search['id']; ?>" method="post">

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
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select" name="Standard">
                <option selected>Choose...</option>
                <option <?php if ($Search['Standard'] == 'ASTM-D698') echo 'selected'; ?>>ASTM-D698</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="PMethods" class="form-label">Preparation Methods</label>
              <select id="PMethods" class="form-select" name="PMethods">
                <option selected>Choose...</option>
                <option <?php if ($Search['Preparation_Method'] == 'Oven_Dried') echo 'selected'; ?>>Oven Dried</option>
                <option <?php if ($Search['Preparation_Method'] == 'Air_Dried') echo 'selected'; ?>>Air Dried</option>
                <option <?php if ($Search['Preparation_Method'] == 'Microwave_Dried') echo 'selected'; ?>>Microwave Dried</option>
                <option <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="SMethods" class="form-label">Split Methods</label>
              <select id="SMethods" class="form-select" name="SMethods">
                <option selected>Choose...</option>
                <option <?php if ($Search['Split_Method'] == 'Manual') echo 'selected'; ?>>Manual</option>
                <option <?php if ($Search['Split_Method'] == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" name="TestMethod" id="TestMethod" value="<?php echo ($Search['Methods']); ?>">
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" name="DateTesting" id="DateTesting" value="<?php echo ($Search['Test_Start_Date']); ?>">
            </div>
            <div class="col-md-6">
              <label for="NatMc" class="form-label">Natural Mc %</label>
              <input type="text" class="form-control" name="NatMc" id="NatMc" value="<?php echo ($Search['Nat_Mc']); ?>">
            </div>
            <div class="col-md-6">
              <label for="SpecGravity" class="form-label">Specific Gravity</label>
              <input type="text" class="form-control" name="SpecGravity" id="SpecGravity" value="<?php echo ($Search['Spec_Gravity']); ?>">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" name="Comments" id="Comments" style="height: 100px;"><?php echo ($Search['Comments']); ?></textarea>
            </div>
            <div class="col-12">
              <textarea hidden class="form-control" name="Graph" id="Graph" style="height: 100px;"><?php echo ($Search['Graph']); ?></textarea>
            </div>
          </div><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-12">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Testing Information</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Trial Number</th>
                        <th scope="col">1</th>
                        <th scope="col">2</th>
                        <th scope="col">3</th>
                        <th scope="col">4</th>
                        <th scope="col">5</th>
                        <th scope="col">6</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                    $datos = array(
                        array("Wt Wet Soil + Mold (gr)", "WetSoilMod1", "WetSoilMod2", "WetSoilMod3", "WetSoilMod4", "WetSoilMod5", "WetSoilMod6"),
                        array("Wt Mold (gr)", "WtMold1", "WtMold2", "WtMold3", "WtMold4", "WtMold5", "WtMold6"),
                        array("Wt Wet Soil (gr)", "WtSoil1", "WtSoil2", "WtSoil3", "WtSoil4", "WtSoil5", "WtSoil6"),
                        array("Vol Mold  (cm3)", "VolMold1", "VolMold2", "VolMold3", "VolMold4", "VolMold5", "VolMold6"),
                        array("Wet Density (kg/m3 )", "WetDensity1", "WetDensity2", "WetDensity3", "WetDensity4", "WetDensity5", "WetDensity6"),
                        array("Dry Density, (kg/m3 )", "DryDensity1", "DryDensity2", "DryDensity3", "DryDensity4", "DryDensity5", "DryDensity6"),
                        array("Dry Density Corrected", "DensyCorrected1", "DensyCorrected2", "DensyCorrected3", "DensyCorrected4", "DensyCorrected5", "DensyCorrected6"),
                        // Puedes agregar más filas según sea necesario
                    );

                    foreach ($datos as $fila) {
                        echo '<tr>';
                        foreach ($fila as $index => $valor) {
                            if ($index < 1) {
                                echo '<th scope="row">' . $valor . '</th>';
                            } else {
                                $readonly = (
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'WtSoil') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'WetDensity') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'DryDensity') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'DensyCorrected') !== false)
                                    ) ? 'readonly tabindex="-1"' : '';
                                    
                                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . ' value="' . $Search[$valor] . '"></td>';
                                }
                            }
                            echo '</tr>';
                        }
                        ?>
                        
                </tbody>
            </table>
            <!-- End Bordered Table -->

            <h5 class="card-title"></h5>
            <!-- Bordered Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Trial Number</th>
                        <th scope="col">1</th>
                        <th scope="col">2</th>
                        <th scope="col">3</th>
                        <th scope="col">4</th>
                        <th scope="col">5</th>
                        <th scope="col">6</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                    $datos = array(
                        array("Container", "Container1", "Container2", "Container3", "Container4", "Container5", "Container6"),
                        array("Wt Wet Soil + Tare (gr)", "WetSoilTare1", "WetSoilTare2", "WetSoilTare3", "WetSoilTare4", "WetSoilTare5", "WetSoilTare6"),
                        array("Wt Dry Soil + Tare (gr)", "WetDryTare1", "WetDryTare2", "WetDryTare3", "WetDryTare4", "WetDryTare5", "WetDryTare6"),
                        array("Wt Water (gr)", "WtWater1", "WtWater2", "WtWater3", "WtWater4", "WtWater5", "WtWater6"),
                        array("Tare (gr)", "Tare1", "Tare2", "Tare3", "Tare4", "Tare5", "Tare6"),
                        array("Wt Dry Soil (gr)", "DrySoil1", "DrySoil2", "DrySoil3", "DrySoil4", "DrySoil5", "DrySoil6"),
                        array("Moisture Content (%)", "MoisturePorce1", "MoisturePorce2", "MoisturePorce3", "MoisturePorce4", "MoisturePorce5", "MoisturePorce6"),
                        array("Moisture Content Corrected", "MCcorrected1", "MCcorrected2", "MCcorrected3", "MCcorrected4", "MCcorrected5", "MCcorrected6"),
                        // Puedes agregar más filas según sea necesario
                    );
                    
                    foreach ($datos as $fila) {
                        echo '<tr>';
                        foreach ($fila as $index => $valor) {
                            if ($index < 1) {
                                echo '<th scope="row">' . $valor . '</th>';
                            } else {
                                $readonly = (
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'WtWater') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'DrySoil') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'MoisturePorce') !== false) ||
                                    ($index >= 1 && $index <= 6 && strpos($valor, 'MCcorrected') !== false)
                                    ) ? 'readonly tabindex="-1"' : '';
                                    
                                    echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . ' value="' . $Search[$valor] . '"></td>';
                                }
                            }
                            echo '</tr>';
                        }
                        ?>
                        
                </tbody>
            </table>
            <!-- End Bordered Table -->

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th scope="col">Max Dry Density</th>
                        <td><input type="text" style="border: none;" class="form-control" name="MaxDryDensity" id="MaxDryDensity" readonly tabindex="-1" value="<?php echo ($Search['Max_Dry_Density_kgm3']); ?>"></td>
                        <!-- ohter -->
                        <th scope="col">Optimum Moisture Content</th>
                        <td><input type="text" style="border: none;" class="form-control" name="OptimumMoisture" id="OptimumMoisture" readonly tabindex="-1" value="<?php echo ($Search['Optimun_MC_Porce']); ?>"></td>
                    </tr>
                </tbody>
            </table>
        
        </div>
    </div>
</div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Correction of Unit Weight and Water Content for Soils Containing Oversize Particles ASTM D4718</h5>
                <!-- Bordered Table -->
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="col" colspan="3">Corrected Dry unit weight of the total material (combined finer and oversize fractions) (Kg/mᵌ)</th>
                            <td><input type="text" style="border: none;" class="form-control" name="CorrectedDryUnitWeigt" id="CorrectedDryUnitWeigt" readonly tabindex="-1" value="<?php echo ($Search['Corrected_Dry_Unit_Weigt']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="col" colspan="3">Corrected water contetn of combined finer and oversize fractions expressed in percent ωT (%)</th>
                            <td><input type="text" style="border: none;" class="form-control" name="CorrectedWaterContentFiner" id="CorrectedWaterContentFiner" readonly tabindex="-1" value="<?php echo ($Search['Corrected_Water_Content_Finer']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Wc (%)</th>
                            <td><input type="text" style="border: none;" class="form-control" name="WcPorce" id="WcPorce" value="<?php echo ($Search['Wc_Porce']); ?>"></td>
                            <th scope="row">ɣDF</th>
                            <td><input type="text" style="border: none;" class="form-control" name="Ydf" id="Ydf" value="<?php echo ($Search['YDF_Porce']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Pc (%)</th>
                            <td><input type="text" style="border: none;" class="form-control" name="PcPorce" id="PcPorce" value="<?php echo ($Search['PC_Porce']); ?>"></td>
                            <th scope="row">PF (%)</th>
                            <td><input type="text" style="border: none;" class="form-control" name="PfPorce" id="PfPorce" value="<?php echo ($Search['PF_Porce']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Gm</th>
                            <td><input type="text" style="border: none;" class="form-control" name="Gm" id="Gm" value="<?php echo ($Search['GM_Porce']); ?>"></td>
                            <th scope="row">ɣDT</th>
                            <td><input type="text" style="border: none;" class="form-control" name="Ydt" id="Ydt" value="<?php echo ($Search['YDT_Porce']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">ɣω (KN/mᵌ)</th>
                            <td colspan="3"><input type="text" style="border: none;" class="form-control" name="YwKnm" id="YwKnm" value="<?php echo ($Search['Yw_KnM3']); ?>"></td>
                        </tr>
                    </tbody>
                </table>
                <!-- End Bordered Table -->
            
            </div>
        </div>
    
    </div>
    
    <div class="col-lg-8">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
        
        <!-- Standard Proctor -->
        <div id="StandardProctor" style="min-height: 400px;" class="echart"></div>
        <!-- End Standard Proctor -->
      
      </div>
    </div>
  
  </div>
    
    <div class="col-lg-4">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="submit" class="btn btn-success" name="update_sp">Update Essay</button>
          
          <div class="btn-group dropup" role="group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-printer"></i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../pdf/sp.php?id=<?php echo ($Search['id']); ?>">Site Investigación</a></li>
              <li><a class="dropdown-item" href="../pdf/sp-cqa.php?id=<?php echo ($Search['id']); ?>">Contruccion</a></li>
              <li><a class="dropdown-item" href="#">Agregado Naranjo</a></li>
            </ul>
          </div>

          <button type="submit" class="btn btn-danger" name="delete_sp"><i class="bi bi-trash"></i></button>
        </div>

        <div class="btn-group mt-2" role="group">
        <?php if (user_can_access(1)): ?>
          <button type="submit" class="btn btn-primary" name="repeat_sp">Repeat</button>
          <button type="submit" class="btn btn-primary" name="reviewed_sp">Reviewed</button>
        <?php endif; ?>
          <button type="button" class="btn btn-primary" onclick="search()">Search Moisture</button>
          <button type="button" class="btn btn-primary" onclick="search()">Seach Gravity</button>
        </div>
        <div class="mt-2" id="mensaje-container"></div>
      </div>
    </div>
  
  </div>

  </form>

  </div>
</section>

</main><!-- End #main -->

<script src="../js/Standard-Proctor.js"></script>
<script src="../libs/graph/Standard-Proctor.js"></script>
<?php include_once('../components/footer.php');  ?>