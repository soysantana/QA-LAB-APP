<?php
$page_title = 'Double Hydrometer';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Save'])) {
        include('../database/double-hydrometer/save.php');
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
                <li class="breadcrumb-item"><a href="dispercion-menu.php">Forms</a></li>
                <li class="breadcrumb-item active">Double Hydrometer</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">

            <form class="row" action="double-hydrometer.php" method="post">

                <div class="col-md-4">
                    <?php echo display_msg($msg); ?>
                </div>

                <!-- Sample Information -->
                <div id="product_info"></div>
                <!-- Sample Information -->

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
                                        <option value="ASTM-D4221">ASTM-D4221</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="PMethods" class="form-label">Preparation Methods</label>
                                    <select id="PMethods" class="form-select" name="PMethods">
                                        <option selected>Choose...</option>
                                        <option value="Oven Dried">Oven Dried</option>
                                        <option value="Air Dried">Air Dried</option>
                                        <option value="Microwave Dried">Microwave Dried</option>
                                        <option value="Wet">Wet</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="DispersionDevice" class="form-label">Dispersion Device</label>
                                    <select id="DispersionDevice" class="form-select" name="DispersionDevice">
                                        <option selected>Choose...</option>
                                        <option value="Cup-Mixer">Cup Mixer</option>
                                        <option value="Air-Jet">Air Jet</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="HydrometerType" class="form-label">Hydrometer Type</label>
                                    <select id="HydrometerType" class="form-select" name="HydrometerType">
                                        <option selected>Choose...</option>
                                        <option value="152H">152H</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="MixingMethod" class="form-label">Mixing Method</label>
                                    <select id="MixingMethod" class="form-select" name="MixingMethod">
                                        <option selected>Choose...</option>
                                        <option value="Agitador">Agitador</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="SpecificGravitywas<" class="form-label">Specific Gravity was</label>
                                    <select id="SpecificGravitywas<" class="form-select" name="SpecificGravitywas">
                                        <option selected>Choose...</option>
                                        <option value="Assumed">Assumed</option>
                                        <option value="Measured">Measured</option>
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
                <!-- Test Information -->

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
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName1" id="TareName1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp1" id="OvenTemp1" value="110 º C" tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil1" id="TareWetSoil1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil1" id="TareDrySoil1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw1" id="WaterWw1" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc1" id="TareMc1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs1" id="DrySoilWs1" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC1" id="MC1" readonly tabindex="-1"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName2" id="TareName2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp2" id="OvenTemp2" value="110 º C" tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil2" id="TareWetSoil2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil2" id="TareDrySoil2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw2" id="WaterWw2" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc2" id="TareMc2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs2" id="DrySoilWs2" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC2" id="MC2" readonly tabindex="-1"></td>
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
                                    <tr>
                                        <th scope="row">Air dried mass hidrometer specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer1" id="AirDriedMassHydrometer1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer2" id="AirDriedMassHydrometer2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of Hidrometer Specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer1" id="DryMassHydrometer1" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer2" id="DryMassHydrometer2" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass retained on No. 200 after Hidrometer (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy1" id="MassRetainedAfterHy1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy2" id="MassRetainedAfterHy2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of hidrometer Specimen passing No. 200 (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing1" id="DryMassHySpecimenPassing1" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing2" id="DryMassHySpecimenPassing2" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fine Content of Hidrometer Specimen (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen1" id="FineContentHySpecimen1" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen2" id="FineContentHySpecimen2" readonly tabindex="-1"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="DispersingAgent" id="DispersingAgent" value="(NaPO3)6"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Amount used (g)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Amountused" id="Amountused"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Temperature of test, T (ºC)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temperatureoftest" id="Temperatureoftest" value="25"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Viscosity of water (g*s/cm2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Viscosityofwater" id="Viscosityofwater" value="0.01"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass density of water Calibrated (ᵨc )</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassdensityofwaterCalibrated" id="MassdensityofwaterCalibrated" value="0.99821"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Acceleration (cm/s2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Acceleration" id="Acceleration" value="980.7"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Volume of suspension (Vsp) cm3</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Volumeofsuspension" id="Volumeofsuspension" value="1000"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Meniscus Correction, Cm </th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MeniscusCorrection" id="MeniscusCorrection" value="-0.5"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="LiquidLimit" id="LiquidLimit"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Plasticity Index (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="PlasticityIndex" id="PlasticityIndex"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="SG_Result" id="SG_Result"></td>
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
                                    <?php for ($i = 1; $i <= 9; $i++): ?>
                                        <tr>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp<?= $i ?>" id="HyCalibrationTemp<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead<?= $i ?>" id="HyCalibrationRead<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp<?= $i ?>" id="HyMeasureTemp<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid<?= $i ?>" id="HyMeasureFluid<?= $i ?>"></td>
                                        </tr>
                                    <?php endfor; ?>
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
                                    <?php for ($i = 1; $i <= 9; $i++): ?>
                                        <tr>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp50g<?= $i ?>" id="HyCalibrationTemp50g<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead50g<?= $i ?>" id="HyCalibrationRead50g<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp50g<?= $i ?>" id="HyMeasureTemp50g<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid50g<?= $i ?>" id="HyMeasureFluid50g<?= $i ?>"></td>
                                        </tr>
                                    <?php endfor; ?>
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
                                    // Valores predefinidos para "Reading Time, T (min)"
                                    $readingTimes = [1, 2, 4, 15, 30, 60, 240, 360, 1440];

                                    for ($i = 1; $i <= 9; $i++):
                                    ?>
                                        <tr>
                                            <th scope="row"><?= $i ?></th>
                                            <td><input type="date" style="border: none;" class="form-control" name="Date<?= $i ?>" id="Date<?= $i ?>" tabindex="-1"></td>
                                            <td><input type="time" style="border: none;" class="form-control" name="Hour<?= $i ?>" id="Hour<?= $i ?>" tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT<?= $i ?>" id="ReadingTimeT<?= $i ?>" value="<?= $readingTimes[$i - 1] ?>" tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="Temp<?= $i ?>" id="Temp<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyReading<?= $i ?>" id="HyReading<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy<?= $i ?>" id="ABdependingHy<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="OffsetReading<?= $i ?>" id="OffsetReading<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner<?= $i ?>" id="MassPercentFiner<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength<?= $i ?>" id="EffectiveLength<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="DMm<?= $i ?>" id="DMm<?= $i ?>" readonly tabindex="-1"></td>
                                        </tr>
                                    <?php endfor; ?>
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
                                    // Valores predefinidos para "Reading Time, T (min)"
                                    $readingTimes = [1, 2, 4, 15, 30, 60, 240, 360, 1440];

                                    for ($i = 1; $i <= 9; $i++):
                                    ?>
                                        <tr>
                                            <th scope="row"><?= $i ?></th>
                                            <td><input type="date" style="border: none;" class="form-control" name="Date50g<?= $i ?>" id="Date50g<?= $i ?>" tabindex="-1"></td>
                                            <td><input type="time" style="border: none;" class="form-control" name="Hour50g<?= $i ?>" id="Hour50g<?= $i ?>" tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT50g<?= $i ?>" id="ReadingTimeT50g<?= $i ?>" value="<?= $readingTimes[$i - 1] ?>" tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="Temp50g<?= $i ?>" id="Temp50g<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="HyReading50g<?= $i ?>" id="HyReading50g<?= $i ?>"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy50g<?= $i ?>" id="ABdependingHy50g<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="OffsetReading50g<?= $i ?>" id="OffsetReading50g<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner50g<?= $i ?>" id="MassPercentFiner50g<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength50g<?= $i ?>" id="EffectiveLength50g<?= $i ?>" readonly tabindex="-1"></td>
                                            <td><input type="text" style="border: none;" class="form-control" name="DMm50g<?= $i ?>" id="DMm50g<?= $i ?>" readonly tabindex="-1"></td>
                                        </tr>
                                    <?php endfor; ?>
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
                                <button type="submit" class="btn btn-success" name="Save">Save Double Hydrometer</button>
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