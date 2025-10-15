<?php
$page_title = 'Hydrometer Analysis';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['SaveHydrometer'])) {
        include('../database/grain-size/hydrometer/save.php');
    }
}
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Hydrometer</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item"><a href="grain-size-menu.php">Forms</a></li>
                <li class="breadcrumb-item active">Hydrometer Analysis</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">

            <form class="row" action="hydrometer.php" method="post">

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
                                        <option value="ASTM-D7928">ASTM-D7928</option>
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

                <div class="col-lg-5">

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
                                        <td><input type="text" style="border: none;" class="form-control" name="MeniscusCorrection" id="MeniscusCorrection" value="1"></td>
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

                <!-- Moisture Content Companion Sample Table -->
                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Moisture Content Companion Sample</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Trial No.</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TrialNo" id="TrialNo" value="1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Name</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName" id="TareName"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp" id="OvenTemp" value="110 º C"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil" id="TareWetSoil"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil" id="TareDrySoil"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw" id="WaterWw" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc" id="TareMc"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs" id="DrySoilWs" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC" id="MC" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                <!-- end Moisture Content Companion Sample Table -->

                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <!-- Data Correction Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Air dried mass hidrometer specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer" id="AirDriedMassHydrometer"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of Hidrometer Specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer" id="DryMassHydrometer" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass retained on No. 200 after Hidrometer (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy" id="MassRetainedAfterHy"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of hidrometer Specimen passing No. 200 (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing" id="DryMassHySpecimenPassing" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fine Content of Hidrometer Specimen (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen" id="FineContentHySpecimen" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- end Data Correction Table -->

                        </div>
                    </div>

                </div>

                <!-- Grain Size Distribution Table -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Grain Size Distribution</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Container</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Container" id="Container"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Wet Soil + Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWetSoilTare" id="WtWetSoilTare"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Dry Soil + Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtDrySoilTare" id="WtDrySoilTare"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Tare_GS" id="Tare_GS"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Dry Soil (gr) </th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtDrySoil" id="WtDrySoil" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Washed (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWashed" id="WtWashed"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Wash Pan (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWashPan" id="WtWashPan" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="card-title">Classification of Soils as per USCS, <br>ASTM designation D 2487-06</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><input type="text" style="border: none; text-align:center;" class="form-control" name="Classification1" id="Classification1"></td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Grain Size Distribution Table -->

                <!-- Screen Analysis Table -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Screen</th>
                                        <th scope="col">(mm)</th>
                                        <th scope="col">Wt Ret</th>
                                        <th scope="col">% Ret</th>
                                        <th scope="col">Cum % Ret</th>
                                        <th scope="col">% Pass</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $datos = array(
                                        array("3\"", "75", "WtRet1", "Ret1", "CumRet1", "Pass1",),
                                        array("2.5\"", "63", "WtRet2", "Ret2", "CumRet2", "Pass2",),
                                        array("2\"", "50.8", "WtRet3", "Ret3", "CumRet3", "Pass3",),
                                        array("1.5\"", "37.5", "WtRet4", "Ret4", "CumRet4", "Pass4",),
                                        array("1\"", "25.0", "WtRet5", "Ret5", "CumRet5", "Pass5",),
                                        array("3/4\"", "19.0", "WtRet6", "Ret6", "CumRet6", "Pass6",),
                                        array("1/2\"", "12.50", "WtRet7", "Ret7", "CumRet7", "Pass7",),
                                        array("3/8\"", "9.5", "WtRet8", "Ret8", "CumRet8", "Pass8",),
                                        array("No. 4", "4.75", "WtRet9", "Ret9", "CumRet9", "Pass9",),
                                        array("10", "2.00", "WtRet10", "Ret10", "CumRet10", "Pass10",),
                                        array("16", "1.18", "WtRet11", "Ret11", "CumRet11", "Pass11",),
                                        array("20", "0.85", "WtRet12", "Ret12", "CumRet12", "Pass12",),
                                        array("50", "0.3", "WtRet13", "Ret13", "CumRet13", "Pass13",),
                                        array("60", "0.25", "WtRet14", "Ret14", "CumRet14", "Pass14",),
                                        array("100", "0.15", "WtRet15", "Ret15", "CumRet15", "Pass15",),
                                        array("140", "0.106", "WtRet16", "Ret16", "CumRet16", "Pass16",),
                                        array("200", "0.075", "WtRet17", "Ret17", "CumRet17", "Pass17",),
                                        // Puedes agregar más filas según sea necesario
                                    );

                                    foreach ($datos as $fila) {
                                        echo '<tr>';
                                        foreach ($fila as $index => $valor) {
                                            if ($index < 2) {
                                                echo '<th scope="row">' . $valor . '</th>';
                                            } else {
                                                $readonly = ($index >= 3 && $index <= 8) ? 'readonly tabindex="-1"' : '';
                                                echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . '></td>';
                                            }
                                        }
                                        echo '</tr>';
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row" colspan="2">Pan</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="PanWtRen" id="PanWtRen"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PanRet" id="PanRet" readonly tabindex="-1"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="2">Total Pan</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Screen Analysis Table -->

                <!-- Sumary Grain Size Distribution Table -->
                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Summary Grain Size Distribution Parameter</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Coarser than Gravel%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="CoarserGravel" id="CoarserGravel" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gravel%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Gravel" id="Gravel" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Sand%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Sand" id="Sand" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fines%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Fines" id="Fines" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D10 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D10" id="D10" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D15 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D15" id="D15" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D30 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D30" id="D30" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D60 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D60" id="D60" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D85 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D85" id="D85" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cc :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Cc" id="Cc" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cu :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Cu" id="Cu" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- end Sumary Grain Size Distribution Table -->

                <!-- Hydrometer Calibration Table -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <table class="table table-bordered">
                                <tbody>
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
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp1" id="HyCalibrationTemp1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead1" id="HyCalibrationRead1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp1" id="HyMeasureTemp1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid1" id="HyMeasureFluid1"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp2" id="HyCalibrationTemp2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead2" id="HyCalibrationRead2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp2" id="HyMeasureTemp2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid2" id="HyMeasureFluid2"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp3" id="HyCalibrationTemp3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead3" id="HyCalibrationRead3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp3" id="HyMeasureTemp3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid3" id="HyMeasureFluid3"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp4" id="HyCalibrationTemp4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead4" id="HyCalibrationRead4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp4" id="HyMeasureTemp4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid4" id="HyMeasureFluid4"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp5" id="HyCalibrationTemp5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead5" id="HyCalibrationRead5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp5" id="HyMeasureTemp5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid5" id="HyMeasureFluid5"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp6" id="HyCalibrationTemp6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead6" id="HyCalibrationRead6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp6" id="HyMeasureTemp6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid6" id="HyMeasureFluid6"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp7" id="HyCalibrationTemp7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead7" id="HyCalibrationRead7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp7" id="HyMeasureTemp7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid7" id="HyMeasureFluid7"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp8" id="HyCalibrationTemp8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead8" id="HyCalibrationRead8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp8" id="HyMeasureTemp8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid8" id="HyMeasureFluid8"></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp9" id="HyCalibrationTemp9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead9" id="HyCalibrationRead9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp9" id="HyMeasureTemp9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid9" id="HyMeasureFluid9"></td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Hydrometer Calibration Table -->

                <!-- Grain Size Graph For the Hydrometer -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <div id="HydrometerGraph" style="min-height: 400px;" class="echart"></div>

                        </div>
                    </div>
                </div>
                <!-- end Grain Size Graph For the Hydrometer -->

                <!-- Information Calculation Hydrometer Table -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row">#</th>
                                        <th scope="row">Date</th>
                                        <th scope="row">Hour</th>
                                        <th scope="row">Reading Time, T (min)</th>
                                        <th scope="row">Temp °C</th>
                                        <th scope="row">Hydrometer Readings (Rm)</th>
                                        <th scope="row">A or B depending of the Hydrometer type</th>
                                        <th scope="row">Offset at Reading (rdm)</th>
                                        <th scope="row">Mass Percent Finer (Nm) (%)</th>
                                        <th scope="row">Effective Length(Hm)</th>
                                        <th scope="row">D, mm</th>
                                        <th scope="row">passing percentage respect to the total sample</th>
                                    </tr>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date1" id="Date1"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour1" id="Hour1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT1" id="ReadingTimeT1" value="1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp1" id="Temp1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading1" id="HyReading1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy1" id="ABdependingHy1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading1" id="OffsetReading1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner1" id="MassPercentFiner1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength1" id="EffectiveLength1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm1" id="DMm1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample1" id="PassingPerceTotalSample1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date2" id="Date2"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour2" id="Hour2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT2" id="ReadingTimeT2" value="2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp2" id="Temp2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading2" id="HyReading2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy2" id="ABdependingHy2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading2" id="OffsetReading2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner2" id="MassPercentFiner2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength2" id="EffectiveLength2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm2" id="DMm2"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample2" id="PassingPerceTotalSample2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date3" id="Date3"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour3" id="Hour3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT3" id="ReadingTimeT3" value="4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp3" id="Temp3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading3" id="HyReading3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy3" id="ABdependingHy3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading3" id="OffsetReading3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner3" id="MassPercentFiner3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength3" id="EffectiveLength3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm3" id="DMm3"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample3" id="PassingPerceTotalSample3"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">4</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date4" id="Date4"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour4" id="Hour4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT4" id="ReadingTimeT4" value="15"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp4" id="Temp4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading4" id="HyReading4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy4" id="ABdependingHy4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading4" id="OffsetReading4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner4" id="MassPercentFiner4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength4" id="EffectiveLength4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm4" id="DMm4"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample4" id="PassingPerceTotalSample4"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">5</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date5" id="Date5"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour5" id="Hour5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT5" id="ReadingTimeT5" value="30"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp5" id="Temp5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading5" id="HyReading5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy5" id="ABdependingHy5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading5" id="OffsetReading5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner5" id="MassPercentFiner5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength5" id="EffectiveLength5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm5" id="DMm5"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample5" id="PassingPerceTotalSample5"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">6</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date6" id="Date6"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour6" id="Hour6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT6" id="ReadingTimeT6" value="60"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp6" id="Temp6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading6" id="HyReading6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy6" id="ABdependingHy6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading6" id="OffsetReading6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner6" id="MassPercentFiner6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength6" id="EffectiveLength6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm6" id="DMm6"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample6" id="PassingPerceTotalSample6"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">7</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date7" id="Date7"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour7" id="Hour7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT7" id="ReadingTimeT7" value="240"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp7" id="Temp7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading7" id="HyReading7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy7" id="ABdependingHy7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading7" id="OffsetReading7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner7" id="MassPercentFiner7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength7" id="EffectiveLength7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm7" id="DMm7"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample7" id="PassingPerceTotalSample7"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">8</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date8" id="Date8"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour8" id="Hour8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT8" id="ReadingTimeT8" value="360"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp8" id="Temp8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading8" id="HyReading8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy8" id="ABdependingHy8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading8" id="OffsetReading8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner8" id="MassPercentFiner8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength8" id="EffectiveLength8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm8" id="DMm8"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample8" id="PassingPerceTotalSample8"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">9</th>
                                        <td><input type="date" style="border: none;" class="form-control" name="Date9" id="Date9"></td>
                                        <td><input type="time" style="border: none;" class="form-control" name="Hour9" id="Hour9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT9" id="ReadingTimeT9" value="1440"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temp9" id="Temp9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="HyReading9" id="HyReading9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="ABdependingHy9" id="ABdependingHy9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="OffsetReading9" id="OffsetReading9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner9" id="MassPercentFiner9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="EffectiveLength9" id="EffectiveLength9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="DMm9" id="DMm9"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample9" id="PassingPerceTotalSample9"></td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
                <!-- end Information Calculation Hydrometer Table -->

                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Actions</h5>
                            <!-- Actions Buttons -->
                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-success" name="SaveHydrometer">Save Hydrometer</button>
                            </div>

                        </div>
                    </div>

                </div>

            </form>

        </div>
    </section>

</main><!-- End #main -->

<script type="module" src="../js/hydrometer/hy.js"></script>
<script src="../libs/graph/hydrometer.js"></script>
<?php include_once('../components/footer.php');  ?>