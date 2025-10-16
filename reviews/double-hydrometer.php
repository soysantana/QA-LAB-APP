<?php
$page_title = 'Double Hydrometer';
require_once('../config/load.php');
$Search = find_by_id('double_hydrometer', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Update'])) {
        include('../database/double-hydrometer/update.php');
    } elseif (isset($_POST['Repeat'])) {
        include('../database/double-hydrometer/repeat.php');
    } elseif (isset($_POST['Reviewed'])) {
        include('../database/double-hydrometer/reviewed.php');
    } elseif (isset($_POST['Delete'])) {
        include('../database/double-hydrometer/delete.php');
    }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Double Hydrometer</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Double Hydrometer</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">

            <form class="row" action="double-hydrometer.php?id=<?php echo $Search['id']; ?>" method="post">

                <div class="col-md-4">
                    <?php echo display_msg($msg); ?>
                </div>

                <!-- Sample Information -->
                <?php include_once('../includes/sample-info-form.php'); ?>
                <!-- End Sample Information -->

                <!-- Test Information -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Trial Information</h5>

                            <!-- Multi Columns Form -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="Standard" class="form-label">Standard</label>
                                    <select id="Standard" class="form-select" name="Standard">
                                        <option <?php if ($Search['Standard'] == 'ASTM-D4221') echo 'selected'; ?>>ASTM-D4221</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="PMethods" class="form-label">Preparation Methods</label>
                                    <select id="PMethods" class="form-select" name="PMethods">
                                        <option selected>Choose...</option>
                                        <option <?php if ($Search['Preparation_Method'] == 'Oven Dried') echo 'selected'; ?>>Oven Dried</option>
                                        <option <?php if ($Search['Preparation_Method'] == 'Air Dried') echo 'selected'; ?>>Air Dried</option>
                                        <option <?php if ($Search['Preparation_Method'] == 'Microwave Dried') echo 'selected'; ?>>Microwave Dried</option>
                                        <option <?php if ($Search['Preparation_Method'] == 'Wet') echo 'selected'; ?>>Wet</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="DispersionDevice" class="form-label">Dispersion Device</label>
                                    <select id="DispersionDevice" class="form-select" name="DispersionDevice">
                                        <option selected>Choose...</option>
                                        <option <?php if ($Search['DispersionDevice'] == 'Cup-Mixer') echo 'selected'; ?>>Cup Mixer</option>
                                        <option <?php if ($Search['DispersionDevice'] == 'Air-Jet') echo 'selected'; ?>>Air Jet</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="HydrometerType" class="form-label">Hydrometer Type</label>
                                    <select id="HydrometerType" class="form-select" name="HydrometerType">
                                        <option selected>Choose...</option>
                                        <option <?php if ($Search['HydrometerType'] == '152H') echo 'selected'; ?>>152H</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="MixingMethod" class="form-label">Mixing Method</label>
                                    <select id="MixingMethod" class="form-select" name="MixingMethod">
                                        <option selected>Choose...</option>
                                        <option <?php if ($Search['MixingMethod'] == 'Agitador') echo 'selected'; ?>>Agitador</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="SpecificGravitywas<" class="form-label">Specific Gravity was</label>
                                    <select id="SpecificGravitywas<" class="form-select" name="SpecificGravitywas">
                                        <option selected>Choose...</option>
                                        <option <?php if ($Search['SpecificGravitywas'] == 'Assumed') echo 'selected'; ?>>Assumed</option>
                                        <option <?php if ($Search['SpecificGravitywas'] == 'Measured') echo 'selected'; ?>>Measured</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="Technician" class="form-label">Technician</label>
                                    <input type="text" class="form-control" name="Technician" id="Technician" value="<?php echo ($Search['Technician']); ?>">
                                </div>
                                <div class="col-md-6">
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
                <!-- End Test Information -->

                <!-- Moisture Content 25g -->
                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Moisture Content Companion Sample 25g</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Trial No.</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TrialNo1" id="TrialNo1" value="1" disabled></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Name</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName1" id="TareName1" value="<?php echo ($Search['TareName']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp1" id="OvenTemp1" value="<?php echo ($Search['OvenTemp']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil1" id="TareWetSoil1" value="<?php echo ($Search['TareWetSoil']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil1" id="TareDrySoil1" value="<?php echo ($Search['TareDrySoil']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw1" id="WaterWw1" value="<?php echo ($Search['WaterWw']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc1" id="TareMc1" value="<?php echo ($Search['TareMc']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs1" id="DrySoilWs1" value="<?php echo ($Search['DrySoilWs']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC1" id="MC1" value="<?php echo ($Search['MC']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                <!-- end Moisture Content 25g -->

                <!-- Moisture Content 50g -->
                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Moisture Content Companion Sample 50g</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Trial No.</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TrialNo2" id="TrialNo2" value="1" disabled></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Name</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName2" id="TareName2" value="<?php echo ($Search['TareName50g']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp2" id="OvenTemp2" value="<?php echo ($Search['OvenTemp50g']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil2" id="TareWetSoil2" value="<?php echo ($Search['TareWetSoil50g']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil2" id="TareDrySoil2" value="<?php echo ($Search['TareDrySoil50g']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw2" id="WaterWw2" value="<?php echo ($Search['WaterWw50g']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc2" id="TareMc2" value="<?php echo ($Search['TareMc50g']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs2" id="DrySoilWs2" value="<?php echo ($Search['DrySoilWs50g']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC2" id="MC2" value="<?php echo ($Search['MC50g']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                <!-- end Moisture Content 50g -->

                <!-- Correction Hydrometer 25g & 50g -->
                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <!-- Data Correction Table -->
                            <table class="table table-bordered">
                                <thead>
                                    <th></th>
                                    <th>25g</th>
                                    <th>50g</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $AirDriedMassHydrometer = explode(',', str_replace('null', '', $Search["AirDriedMassHydrometer"]));
                                    $DryMassHydrometer =    explode(',', str_replace('null', '', $Search["DryMassHydrometer"]));
                                    $MassRetainedAfterHy = explode(',', str_replace('null', '', $Search["MassRetainedAfterHy"]));
                                    $DryMassHySpecimenPassing = explode(',', str_replace('null', '', $Search["DryMassHySpecimenPassing"]));
                                    $FineContentHySpecimen = explode(',', str_replace('null', '', $Search["FineContentHySpecimen"]));
                                    ?>
                                    <tr>
                                        <th scope="row">Air dried mass hidrometer specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer1" id="AirDriedMassHydrometer1" value="<?= $AirDriedMassHydrometer[0]; ?>"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer2" id="AirDriedMassHydrometer2" value="<?= $AirDriedMassHydrometer[1]; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of Hidrometer Specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer1" id="DryMassHydrometer1" value="<?= $DryMassHydrometer[0]; ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer2" id="DryMassHydrometer2" value="<?= $DryMassHydrometer[1]; ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass retained on No. 200 after Hidrometer (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy1" id="MassRetainedAfterHy1" value="<?= $MassRetainedAfterHy[0] ?>"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy2" id="MassRetainedAfterHy2" value="<?= $MassRetainedAfterHy[1] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of hidrometer Specimen passing No. 200 (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing1" id="DryMassHySpecimenPassing1" value="<?= $DryMassHySpecimenPassing[0] ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing2" id="DryMassHySpecimenPassing2" value="<?= $DryMassHySpecimenPassing[1] ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fine Content of Hidrometer Specimen (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen1" id="FineContentHySpecimen1" value="<?= $FineContentHySpecimen[0]; ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen2" id="FineContentHySpecimen2" value="<?= $FineContentHySpecimen[1]; ?>" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- end Data Correction Table -->

                        </div>
                    </div>

                </div>
                <!-- Correction Hydrometer 25g & 50g -->

                <!-- Hydrometer Limit Specific Gravity -->
                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Hydrometer Analysis</h5>
                            <!-- Hydrometer Analysis Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Dispersing Agent</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DispersingAgent" id="DispersingAgent" value="<?php echo $Search['DispersionAgent']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Amount used (g)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Amountused" id="Amountused" value="<?php echo $Search['AmountUsed']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Temperature of test, T (ºC)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temperatureoftest" id="Temperatureoftest" value="<?php echo $Search['Temperatureoftest']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Viscosity of water (g*s/cm2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Viscosityofwater" id="Viscosityofwater" value="<?php echo $Search['Viscosityofwater']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass density of water Calibrated (ᵨc )</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassdensityofwaterCalibrated" id="MassdensityofwaterCalibrated" value="<?php echo $Search['MassdensityofwaterCalibrated']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Acceleration (cm/s2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Acceleration" id="Acceleration" value="<?php echo $Search['Acceleration']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Volume of suspension (Vsp) cm3</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Volumeofsuspension" id="Volumeofsuspension" value="<?php echo $Search['Volumeofsuspension']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Meniscus Correction, Cm </th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MeniscusCorrection" id="MeniscusCorrection" value="<?php echo $Search['MeniscusCorrection']; ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- end Hydrometer Analysis Table -->

                            <h5 class="card-title">Atterber Limit Results</h5>
                            <!-- Atterber Limit Results Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Liquid Limit (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="LiquidLimit" id="LiquidLimit" value="<?php echo $Search['LiquidLimit']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Plasticity Index (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="PlasticityIndex" id="PlasticityIndex" value="<?php echo $Search['PlasticityIndex']; ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- end Atterber Limit Results Table -->

                            <h5 class="card-title">Specific Gravity Result</h5>
                            <!-- Specific Gravity Result Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">SG</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="SG_Result" id="SG_Result" value="<?php echo $Search['SG_Result']; ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- end Specific Gravity Result Table -->

                        </div>
                    </div>

                </div>
                <!-- Hydrometer Limit Specific Gravity -->

                <!-- Hydrometer Calibration 25g -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">25g</h5>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="row" colspan="2">Hydrometer Calibration</th>
                                        <th scope="row" colspan="2">Hydrometer measure of fluid</th>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="2">Hydrometer ID</th>
                                        <th scope="row" colspan="2">Hydrometer ID</th>
                                    </tr>
                                    <tr>
                                        <th scope="row">Temperature (ºC)</th>
                                        <th scope="row">Actual Reading</th>
                                        <th scope="row">Temperature (ºC)</th>
                                        <th scope="row">Actual Reading</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $HyCalibrationTemp = explode(',', str_replace('null', '', $Search["HyCalibrationTemp"]));
                                    $HyCalibrationRead =    explode(',', str_replace('null', '', $Search["HyCalibrationRead"]));
                                    $HyMeasureTemp = explode(',', str_replace('null', '', $Search["HyMeasureTemp"]));
                                    $HyMeasureFluid = explode(',', str_replace('null', '', $Search["HyMeasureFluid"]));

                                    for ($i = 1; $i <= 9; $i++) {
                                        $tempValue   = isset($HyCalibrationTemp[$i - 1]) ? trim($HyCalibrationTemp[$i - 1]) : '';
                                        $readValue   = isset($HyCalibrationRead[$i - 1]) ? trim($HyCalibrationRead[$i - 1]) : '';
                                        $measTemp    = isset($HyMeasureTemp[$i - 1]) ? trim($HyMeasureTemp[$i - 1]) : '';
                                        $fluidValue  = isset($HyMeasureFluid[$i - 1]) ? trim($HyMeasureFluid[$i - 1]) : '';

                                        echo '<tr>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp' . $i . '" id="HyCalibrationTemp' . $i . '" value="' . $tempValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead' . $i . '" id="HyCalibrationRead' . $i . '" value="' . $readValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp' . $i . '" id="HyMeasureTemp' . $i . '" value="' . $measTemp . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid' . $i . '" id="HyMeasureFluid' . $i . '" value="' . $fluidValue . '"></td>';
                                        echo '</tr>';
                                    };
                                    ?>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Hydrometer Calibration 25g -->

                <!-- Hydrometer Calibration 50g -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">50g</h5>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="row" colspan="2">Hydrometer Calibration</th>
                                        <th scope="row" colspan="2">Hydrometer measure of fluid</th>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="2">Hydrometer ID</th>
                                        <th scope="row" colspan="2">Hydrometer ID</th>
                                    </tr>
                                    <tr>
                                        <th scope="row">Temperature (ºC)</th>
                                        <th scope="row">Actual Reading</th>
                                        <th scope="row">Temperature (ºC)</th>
                                        <th scope="row">Actual Reading</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $HyCalibrationTemp50g = explode(',', str_replace('null', '', $Search["HyCalibrationTemp50g"]));
                                    $HyCalibrationRead50g =    explode(',', str_replace('null', '', $Search["HyCalibrationRead50g"]));
                                    $HyMeasureTemp50g = explode(',', str_replace('null', '', $Search["HyMeasureTemp50g"]));
                                    $HyMeasureFluid50g = explode(',', str_replace('null', '', $Search["HyMeasureFluid50g"]));

                                    for ($i = 1; $i <= 9; $i++) {
                                        $tempValue   = isset($HyCalibrationTemp50g[$i - 1]) ? trim($HyCalibrationTemp50g[$i - 1]) : '';
                                        $readValue   = isset($HyCalibrationRead50g[$i - 1]) ? trim($HyCalibrationRead50g[$i - 1]) : '';
                                        $measTemp    = isset($HyMeasureTemp50g[$i - 1]) ? trim($HyMeasureTemp50g[$i - 1]) : '';
                                        $fluidValue  = isset($HyMeasureFluid50g[$i - 1]) ? trim($HyMeasureFluid50g[$i - 1]) : '';

                                        echo '<tr>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp50g' . $i . '" id="HyCalibrationTemp50g' . $i . '" value="' . $tempValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead50g' . $i . '" id="HyCalibrationRead50g' . $i . '" value="' . $readValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp50g' . $i . '" id="HyMeasureTemp50g' . $i . '" value="' . $measTemp . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid50g' . $i . '" id="HyMeasureFluid50g' . $i . '" value="' . $fluidValue . '"></td>';
                                        echo '</tr>';
                                    };
                                    ?>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Hydrometer Calibration 50g -->

                <!-- Calculation Hydrometer Table 25g -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">25g</h5>

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Hour</th>
                                        <th>Reading Time, T (min)</th>
                                        <th>Temp °C</th>
                                        <th>Hydrometer Readings (Rm)</th>
                                        <th>A or B depending of the Hydrometer type</th>
                                        <th>Offset at Reading (rdm)</th>
                                        <th>Mass Percent Finer (Nm) (%)</th>
                                        <th>Effective Length (Hm)</th>
                                        <th>D, mm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $date                     = explode(',',  str_replace('null', '', $Search["Date"]));
                                    $hour                     = explode(',',  str_replace('null', '', $Search["Hour"]));
                                    $readingTime              = explode(',',  str_replace('null', '', $Search["ReadingTimeT"]));
                                    $temps                    = explode(',',  str_replace('null', '', $Search["Temp"]));
                                    $hyReadings               = explode(',',  str_replace('null', '', $Search["HyReading"]));
                                    $abDependingHy            = explode(',',  str_replace('null', '', $Search["ABdependingHy"]));
                                    $offsetReading            = explode(',',  str_replace('null', '', $Search["OffsetReading"]));
                                    $massPercentFiner         = explode(',',  str_replace('null', '', $Search["MassPercentFiner"]));
                                    $effectiveLength          = explode(',',  str_replace('null', '', $Search["EffectiveLength"]));
                                    $dmm                      = explode(',',  str_replace('null', '', $Search["DMm"]));

                                    for ($i = 1; $i <= 9; $i++) {
                                        $dateValue        = isset($date[$i - 1]) ? trim($date[$i - 1]) : '';
                                        $hourValue        = isset($hour[$i - 1]) ? trim($hour[$i - 1]) : '';
                                        $readingTimeValue = isset($readingTime[$i - 1]) ? trim($readingTime[$i - 1]) : '';
                                        $tempValue        = isset($temps[$i - 1]) ? trim($temps[$i - 1]) : '';
                                        $hyReadingValue   = isset($hyReadings[$i - 1]) ? trim($hyReadings[$i - 1]) : '';
                                        $abValue          = isset($abDependingHy[$i - 1]) ? trim($abDependingHy[$i - 1]) : '';
                                        $offsetValue      = isset($offsetReading[$i - 1]) ? trim($offsetReading[$i - 1]) : '';
                                        $massValue        = isset($massPercentFiner[$i - 1]) ? trim($massPercentFiner[$i - 1]) : '';
                                        $lengthValue      = isset($effectiveLength[$i - 1]) ? trim($effectiveLength[$i - 1]) : '';
                                        $dmmValue         = isset($dmm[$i - 1]) ? trim($dmm[$i - 1]) : '';

                                        echo '<tr>';
                                        echo '<th scope="row">' . $i . '</th>';
                                        echo '<td><input type="date" style="border: none;" class="form-control" name="Date' . $i . '" id="Date' . $i . '" value="' . $dateValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="time" style="border: none;" class="form-control" name="Hour' . $i . '" id="Hour' . $i . '" value="' . $hourValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT' . $i . '" id="ReadingTimeT' . $i . '" value="' . $readingTimeValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="Temp' . $i . '" id="Temp' . $i . '" value="' . $tempValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyReading' . $i . '" id="HyReading' . $i . '" value="' . $hyReadingValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="ABdependingHy' . $i . '" id="ABdependingHy' . $i . '" value="' . $abValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="OffsetReading' . $i . '" id="OffsetReading' . $i . '" value="' . $offsetValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner' . $i . '" id="MassPercentFiner' . $i . '" value="' . $massValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="EffectiveLength' . $i . '" id="EffectiveLength' . $i . '" value="' . $lengthValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="DMm' . $i . '" id="DMm' . $i . '" value="' . $dmmValue . '" readonly tabindex="-1"></td>';
                                        echo '</tr>';
                                    };
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end Calculation Hydrometer Table 25g -->

                <!-- Calculation Hydrometer Table 50g -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">50g</h5>

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Hour</th>
                                        <th>Reading Time, T (min)</th>
                                        <th>Temp °C</th>
                                        <th>Hydrometer Readings (Rm)</th>
                                        <th>A or B depending of the Hydrometer type</th>
                                        <th>Offset at Reading (rdm)</th>
                                        <th>Mass Percent Finer (Nm) (%)</th>
                                        <th>Effective Length (Hm)</th>
                                        <th>D, mm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $date                     = explode(',',  str_replace('null', '', $Search["Date50g"]));
                                    $hour                     = explode(',',  str_replace('null', '', $Search["Hour50g"]));
                                    $readingTime              = explode(',',  str_replace('null', '', $Search["ReadingTimeT50g"]));
                                    $temps                    = explode(',',  str_replace('null', '', $Search["Temp50g"]));
                                    $hyReadings               = explode(',',  str_replace('null', '', $Search["HyReading50g"]));
                                    $abDependingHy            = explode(',',  str_replace('null', '', $Search["ABdependingHy50g"]));
                                    $offsetReading            = explode(',',  str_replace('null', '', $Search["OffsetReading50g"]));
                                    $massPercentFiner         = explode(',',  str_replace('null', '', $Search["MassPercentFiner50g"]));
                                    $effectiveLength          = explode(',',  str_replace('null', '', $Search["EffectiveLength50g"]));
                                    $dmm                      = explode(',',  str_replace('null', '', $Search["DMm50g"]));

                                    for ($i = 1; $i <= 9; $i++) {
                                        $dateValue        = isset($date[$i - 1]) ? trim($date[$i - 1]) : '';
                                        $hourValue        = isset($hour[$i - 1]) ? trim($hour[$i - 1]) : '';
                                        $readingTimeValue = isset($readingTime[$i - 1]) ? trim($readingTime[$i - 1]) : '';
                                        $tempValue        = isset($temps[$i - 1]) ? trim($temps[$i - 1]) : '';
                                        $hyReadingValue   = isset($hyReadings[$i - 1]) ? trim($hyReadings[$i - 1]) : '';
                                        $abValue          = isset($abDependingHy[$i - 1]) ? trim($abDependingHy[$i - 1]) : '';
                                        $offsetValue      = isset($offsetReading[$i - 1]) ? trim($offsetReading[$i - 1]) : '';
                                        $massValue        = isset($massPercentFiner[$i - 1]) ? trim($massPercentFiner[$i - 1]) : '';
                                        $lengthValue      = isset($effectiveLength[$i - 1]) ? trim($effectiveLength[$i - 1]) : '';
                                        $dmmValue         = isset($dmm[$i - 1]) ? trim($dmm[$i - 1]) : '';

                                        echo '<tr>';
                                        echo '<th scope="row">' . $i . '</th>';
                                        echo '<td><input type="date" style="border: none;" class="form-control" name="Date50g' . $i . '" id="Date50g' . $i . '" value="' . $dateValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="time" style="border: none;" class="form-control" name="Hour50g' . $i . '" id="Hour50g' . $i . '" value="' . $hourValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT50g' . $i . '" id="ReadingTimeT50g' . $i . '" value="' . $readingTimeValue . '" tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="Temp50g' . $i . '" id="Temp50g' . $i . '" value="' . $tempValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="HyReading50g' . $i . '" id="HyReading50g' . $i . '" value="' . $hyReadingValue . '"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="ABdependingHy50g' . $i . '" id="ABdependingHy50g' . $i . '" value="' . $abValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="OffsetReading50g' . $i . '" id="OffsetReading50g' . $i . '" value="' . $offsetValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner50g' . $i . '" id="MassPercentFiner50g' . $i . '" value="' . $massValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="EffectiveLength50g' . $i . '" id="EffectiveLength50g' . $i . '" value="' . $lengthValue . '" readonly tabindex="-1"></td>';
                                        echo '<td><input type="text" style="border: none;" class="form-control" name="DMm50g' . $i . '" id="DMm50g' . $i . '" value="' . $dmmValue . '" readonly tabindex="-1"></td>';
                                        echo '</tr>';
                                    };
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end Calculation Hydrometer Table 50g -->

                <!-- Actions -->
                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Actions</h5>
                            <!-- Actions Buttons -->
                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-success" name="Update">Update Double Hydrometer</button>

                                <div class="btn-group dropup" role="group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" data-exportar="DHY-Naranjo">Naranjo</a></li>
                                        <li><a class="dropdown-item" data-exportar="DHY-Build">Contruccion</a></li>
                                    </ul>
                                </div>

                                <button type="submit" class="btn btn-danger" name="Delete"><i class="bi bi-trash"></i></button>
                                <?php if (user_can_access(1)): ?>
                                    <button type="submit" class="btn btn-primary" name="Repeat">Repeat</button>
                                    <button type="submit" class="btn btn-primary" name="Reviewed">Reviewed</button>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>

                </div>
                <!-- Actions -->

            </form>

        </div>
    </section>

</main><!-- End #main -->

<script type="module" src="../js/hydrometer/dhy.js"></script>
<?php include_once('../components/footer.php');  ?>