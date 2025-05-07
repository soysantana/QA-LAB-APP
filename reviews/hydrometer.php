<?php
$page_title = 'Hydrometer Analysis';
require_once('../config/load.php');
$Search = find_by_id('hydrometer', $_GET['id']);
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['UpdateHydrometer'])) {
        include('../database/grain-size/hydrometer/update.php');
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
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Hydrometer Analysis</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section" oninput="clasificarSuelo()">
        <div class="row" oninput="HY()">

            <form class="row" action="hydrometer.php?id=<?php echo $Search['id']; ?>" method="post">

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
                                        <option <?php if ($Search['Standard'] == 'ASTM-D7928') echo 'selected'; ?>>ASTM-D7928</option>
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

                <!-- Hydrometer Analysis -->
                <div class="col-lg-5">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Hydrometer Analysis</h5>
                            <!-- Hydrometer Analysis Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Dispersing Agent</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DispersingAgent" id="DispersingAgent" value="<?php echo ($Search['DispersionAgent']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Amount used (g)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Amountused" id="Amountused" value="<?php echo ($Search['AmountUsed']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Temperature of test, T (ºC)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Temperatureoftest" id="Temperatureoftest" value="<?php echo ($Search['Temperatureoftest']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Viscosity of water (g*s/cm2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Viscosityofwater" id="Viscosityofwater" value="<?php echo ($Search['Viscosityofwater']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass density of water Calibrated (ᵨc )</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassdensityofwaterCalibrated" id="MassdensityofwaterCalibrated" value="<?php echo ($Search['MassdensityofwaterCalibrated']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Acceleration (cm/s2)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Acceleration" id="Acceleration" value="<?php echo ($Search['Acceleration']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Volume of suspension (Vsp) cm3</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Volumeofsuspension" id="Volumeofsuspension" value="<?php echo ($Search['Volumeofsuspension']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Meniscus Correction, Cm </th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MeniscusCorrection" id="MeniscusCorrection" value="<?php echo ($Search['MeniscusCorrection']); ?>"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="LiquidLimit" id="LiquidLimit" value="<?php echo ($Search['LiquidLimit']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Plasticity Index (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="PlasticityIndex" id="PlasticityIndex" value="<?php echo ($Search['PlasticityIndex']); ?>"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="SG_Result" id="SG_Result" value="<?php echo ($Search['SG_Result']); ?>"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="TareName" id="TareName" value="<?php echo ($Search['TareName']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Oven Temperature (°C)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="OvenTemp" id="OvenTemp" value="<?php echo ($Search['OvenTemp']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Wet Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareWetSoil" id="TareWetSoil" value="<?php echo ($Search['TareWetSoil']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare Plus Dry Soil (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareDrySoil" id="TareDrySoil" value="<?php echo ($Search['TareDrySoil']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Water, Ww (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WaterWw" id="WaterWw" value="<?php echo ($Search['WaterWw']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TareMc" id="TareMc" value="<?php echo ($Search['TareMc']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry Soil, Ws (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DrySoilWs" id="DrySoilWs" value="<?php echo ($Search['DrySoilWs']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Moisture Content (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MC" id="MC" value="<?php echo ($Search['MC']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                <!-- end Moisture Content Companion Sample Table -->

                <!-- Correction For the No200 Passing Moisture -->
                <div class="col-lg-3">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <!-- Data Correction Table -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Air dried mass hidrometer specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="AirDriedMassHydrometer" id="AirDriedMassHydrometer" value="<?php echo ($Search['AirDriedMassHydrometer']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of Hidrometer Specimen (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHydrometer" id="DryMassHydrometer" value="<?php echo ($Search['DryMassHydrometer']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mass retained on No. 200 after Hidrometer (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="MassRetainedAfterHy" id="MassRetainedAfterHy" value="<?php echo ($Search['MassRetainedAfterHy']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Dry mass of hidrometer Specimen passing No. 200 (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="DryMassHySpecimenPassing" id="DryMassHySpecimenPassing" value="<?php echo ($Search['DryMassHySpecimenPassing']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fine Content of Hidrometer Specimen (%)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="FineContentHySpecimen" id="FineContentHySpecimen" value="<?php echo ($Search['FineContentHySpecimen']); ?>" readonly tabindex="-1"></td>
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

                            <table class="table table-bordered" oninput="GS()">
                                <tbody>
                                    <tr>
                                        <th scope="row">Container</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Container" id="Container" value="<?php echo ($Search['Container']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Wet Soil + Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWetSoilTare" id="WtWetSoilTare" value="<?php echo ($Search['Wet_Soil_Tare']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Dry Soil + Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtDrySoilTare" id="WtDrySoilTare" value="<?php echo ($Search['Wet_Dry_Tare']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tare (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Tare_GS" id="Tare_GS" value="<?php echo ($Search['Tare']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Dry Soil (gr) </th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtDrySoil" id="WtDrySoil" value="<?php echo ($Search['Wt_Dry_Soil']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Washed (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWashed" id="WtWashed" value="<?php echo ($Search['Wt_Washed']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Wt Wash Pan (gr)</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="WtWashPan" id="WtWashPan" value="<?php echo ($Search['Wt_Wash_Pan']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="card-title">Classification of Soils as per USCS, <br>ASTM designation D 2487-06</h5>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><input type="text" style="border: none; text-align:center;" class="form-control" name="Classification1" id="Classification1" value="<?php echo ($Search['Classification1']); ?>"></td>
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

                            <table class="table table-bordered" oninput="GS()">
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
                                                echo '<td><input type="text" style="border: none;" class="form-control" name="' . $valor . '" id="' . $valor . '" ' . $readonly . ' value="' . $Search[$valor] . '"></td>';
                                            }
                                        }
                                        echo '</tr>';
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row" colspan="2">Pan</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="PanWtRen" id="PanWtRen" value="<?php echo ($Search['PanWtRen']); ?>"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="PanRet" id="PanRet" value="<?php echo ($Search['PanRet']); ?>" readonly tabindex="-1"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="2">Total Pan</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalWtRet" id="TotalWtRet" value="<?php echo ($Search['TotalWtRet']); ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalRet" id="TotalRet" value="<?php echo ($Search['TotalRet']); ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalCumRet" id="TotalCumRet" value="<?php echo ($Search['TotalCumRet']); ?>" readonly tabindex="-1"></td>
                                        <td><input type="text" style="border: none;" class="form-control" name="TotalPass" id="TotalPass" value="<?php echo ($Search['TotalPass']); ?>" readonly tabindex="-1"></td>
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
                                        <td><input type="text" style="border: none;" class="form-control" name="CoarserGravel" id="CoarserGravel" value="<?php echo ($Search['Coarser_than_Gravel']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gravel%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Gravel" id="Gravel" value="<?php echo ($Search['Gravel']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Sand%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Sand" id="Sand" value="<?php echo ($Search['Sand']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fines%</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Fines" id="Fines" value="<?php echo ($Search['Fines']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D10 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D10" id="D10" value="<?php echo ($Search['D10']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D15 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D15" id="D15" value="<?php echo ($Search['D15']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D30 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D30" id="D30" value="<?php echo ($Search['D30']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D60 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D60" id="D60" value="<?php echo ($Search['D60']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">D85 (mm) :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="D85" id="D85" value="<?php echo ($Search['D85']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cc :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Cc" id="Cc" value="<?php echo ($Search['Cc']); ?>" readonly tabindex="-1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cu :</th>
                                        <td><input type="text" style="border: none;" class="form-control" name="Cu" id="Cu" value="<?php echo ($Search['Cu']); ?>" readonly tabindex="-1"></td>
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
                                    <?php
                                        // Convertir cada cadena en un array
                                        $hyCalibrationTemps = explode(',', $Search["HyCalibrationTemp"]);
                                        $hyCalibrationReads = explode(',', $Search["HyCalibrationRead"]);
                                        $hyMeasureTemps = explode(',', $Search["HyMeasureTemp"]);
                                        $hyMeasureFluids = explode(',', $Search["HyMeasureFluid"]);

                                        for ($i = 1; $i <= 9; $i++) {
                                            $tempValue   = isset($hyCalibrationTemps[$i - 1]) ? trim($hyCalibrationTemps[$i - 1]) : '';
                                            $readValue   = isset($hyCalibrationReads[$i - 1]) ? trim($hyCalibrationReads[$i - 1]) : '';
                                            $measTemp    = isset($hyMeasureTemps[$i - 1]) ? trim($hyMeasureTemps[$i - 1]) : '';
                                            $fluidValue  = isset($hyMeasureFluids[$i - 1]) ? trim($hyMeasureFluids[$i - 1]) : '';

                                            echo '<tr>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationTemp' . $i . '" id="HyCalibrationTemp' . $i . '" value="' . $tempValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="HyCalibrationRead' . $i . '" id="HyCalibrationRead' . $i . '" value="' . $readValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureTemp' . $i . '" id="HyMeasureTemp' . $i . '" value="' . $measTemp . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="HyMeasureFluid' . $i . '" id="HyMeasureFluid' . $i . '" value="' . $fluidValue . '"></td>';
                                            echo '</tr>';
                                        }
                                    ?>

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

                            <div id="HydrometerGraph" style="min-height: 500px;" class="echart"></div>

                        </div>
                    </div>
                </div>
                <!-- end Grain Size Graph For the Hydrometer -->

                <!-- Information Calculation Hydrometer Table -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <table class="table table-bordered table-sm" oninput="hydrometer()">
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
                                    <?php
                                        $readingTimeValues = [1, 2, 4, 15, 30, 60, 240, 360, 1440];
                                        $dateValues                    = explode(',', $Search["Date"]);
                                        $hourValues                    = explode(',', $Search["Hour"]);
                                        $temps                         = explode(',', $Search["Temp"]);
                                        $hyReadings                    = explode(',', $Search["HyReading"]);
                                        $abDependingHy                = explode(',', $Search["ABdependingHy"]);
                                        $offsetReading                = explode(',', $Search["OffsetReading"]);
                                        $massPercentFiner             = explode(',', $Search["MassPercentFiner"]);
                                        $effectiveLength              = explode(',', $Search["EffectiveLength"]);
                                        $dmm                           = explode(',', $Search["DMm"]);
                                        $passingPerceTotalSample      = explode(',', $Search["PassingPerceTotalSample"]);

                                        for ($i = 1; $i <= 9; $i++) {
                                            $dateValue       = isset($dateValues[$i - 1]) ? trim($dateValues[$i - 1]) : '';
                                            $hourValue       = isset($hourValues[$i - 1]) ? trim($hourValues[$i - 1]) : '';
                                            $tempValue       = isset($temps[$i - 1]) ? trim($temps[$i - 1]) : '';
                                            $hyReadingValue  = isset($hyReadings[$i - 1]) ? trim($hyReadings[$i - 1]) : '';
                                            $abValue         = isset($abDependingHy[$i - 1]) ? trim($abDependingHy[$i - 1]) : '';
                                            $offsetValue     = isset($offsetReading[$i - 1]) ? trim($offsetReading[$i - 1]) : '';
                                            $massValue       = isset($massPercentFiner[$i - 1]) ? trim($massPercentFiner[$i - 1]) : '';
                                            $lengthValue     = isset($effectiveLength[$i - 1]) ? trim($effectiveLength[$i - 1]) : '';
                                            $dmmValue        = isset($dmm[$i - 1]) ? trim($dmm[$i - 1]) : '';
                                            $passingValue    = isset($passingPerceTotalSample[$i - 1]) ? trim($passingPerceTotalSample[$i - 1]) : '';

                                            echo '<tr>';
                                            echo '<th scope="row">' . $i . '</th>';
                                            echo '<td><input type="date" style="border: none;" class="form-control" name="Date' . $i . '" id="Date' . $i . '" value="' . $dateValue . '"></td>';
                                            echo '<td><input type="time" style="border: none;" class="form-control" name="Hour' . $i . '" id="Hour' . $i . '" value="' . $hourValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="ReadingTimeT' . $i . '" id="ReadingTimeT' . $i . '" value="' . $readingTimeValues[$i - 1] . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="Temp' . $i . '" id="Temp' . $i . '" value="' . $tempValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="HyReading' . $i . '" id="HyReading' . $i . '" value="' . $hyReadingValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="ABdependingHy' . $i . '" id="ABdependingHy' . $i . '" value="' . $abValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="OffsetReading' . $i . '" id="OffsetReading' . $i . '" value="' . $offsetValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="MassPercentFiner' . $i . '" id="MassPercentFiner' . $i . '" value="' . $massValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="EffectiveLength' . $i . '" id="EffectiveLength' . $i . '" value="' . $lengthValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="DMm' . $i . '" id="DMm' . $i . '" value="' . $dmmValue . '"></td>';
                                            echo '<td><input type="text" style="border: none;" class="form-control" name="PassingPerceTotalSample' . $i . '" id="PassingPerceTotalSample' . $i . '" value="' . $passingValue . '"></td>';
                                            echo '</tr>';
                                        }
                                    ?>


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
                                <button type="submit" class="btn btn-success" name="UpdateHydrometer">Update Hydrometer</button>
                            </div>

                        </div>
                    </div>

                </div>

            </form>

        </div>
    </section>

</main><!-- End #main -->

<script src="../js/hydrometer/hy.js"></script>
<script src="../libs/graph/hydrometer.js"></script>
<?php include_once('../components/footer.php');  ?>