<?php
$user = current_user();

if (isset($_POST['specific-gravity'])) {
    $req_fields = array(
        'SampleName',
        'Technician',
        'DateTesting'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $ProjectName = $db->escape($_POST['ProjectName']);
        $Client = $db->escape($_POST['Client']);
        $ProjectNumber = $db->escape($_POST['ProjectNumber']);
        $Structure = $db->escape($_POST['Structure']);
        $Area = $db->escape($_POST['Area']);
        $Source = $db->escape($_POST['Source']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $SampleID = $db->escape($_POST['SampleName']);
        $SampleNumber = $db->escape($_POST['SampleNumber']);
        $DepthFrom = $db->escape($_POST['DepthFrom']);
        $DepthTo = $db->escape($_POST['DepthTo']);
        $MType = $db->escape($_POST['MType']);
        $SType = $db->escape($_POST['SType']);
        $North = $db->escape($_POST['North']);
        $East = $db->escape($_POST['East']);
        $Elev = $db->escape($_POST['Elev']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        // ohters
        $Standard = $db->escape($_POST['Standard']);
        $SMethods = $db->escape($_POST['SMethods']);
        $PMethods = $db->escape($_POST['PMethods']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $FieldComment = $db->escape($_POST['FieldComment']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SG";
        $id = uuid();

        $PycnUsed = $db->escape($_POST['PycnUsed']);
        $PycnNumber = $db->escape($_POST['PycnNumber']);
        $TestTemp = $db->escape($_POST['TestTemp']);
        $MassDryPycn = $db->escape($_POST['MassDryPycn']);
        $VolumePycn = $db->escape($_POST['VolumePycn']);
        $DensityWaterTemp = $db->escape($_POST['DensityWaterTemp']);
        $PycnWaterTeDensityWaterTempmp = $db->escape($_POST['PycnWaterTemp']);
        $WeightTare = $db->escape($_POST['WeightTare']);
        $WeightSoil = $db->escape($_POST['WeightSoil']);
        $WeightPycnSoilWaterMpws = $db->escape($_POST['WeightPycnSoilWaterMpws']);
        $TestTempAfter = $db->escape($_POST['TestTempAfter']);
        $DensityWaterTempAfter = $db->escape($_POST['DensityWaterTempAfter']);
        $PycnWaterTempAfter = $db->escape($_POST['PycnWaterTempAfter']);
        $SgSoilTemp = $db->escape($_POST['SgSoilTemp']);
        $TempCoefficent = $db->escape($_POST['TempCoefficent']);
        $SgSolid = $db->escape($_POST['SgSolid']);


        $sql = "INSERT INTO specific_gravity (
            id,
            Project_Name,
            Client,
            Project_Number,
            Sample_ID,
            Sample_Number,
            Structure,
            Area,
            Source,
            Depth_From,
            Depth_To,
            Material_Type,
            Sample_Type,
            North,
            East,
            Elev,
            Sample_By,
            Sample_Date,
            Registed_Date,
            Register_By,
            Test_Type,
            Standard,
            SMethods,
            PMethods,
            Technician,
            Test_Start_Date,
            Comments,
            FieldComment,
            Methods,
            Pycnometer_Used,
            Pycnometer_Number,
            Test_Temperatur,
            Average_Calibrated_Mass_Dry_Pycnometer_Mp,
            Average_Calibrated_Volume_Pycnometer_Vp,
            Density_Water_Test_Temperature,
            Calibration_Weight_Pynometer_Temperature_Mpw,
            Weight__Dry_Soil_Tare,
            Weight_Dry_Soil_Ms,
            Weight_Pycnometer_Soil_Water_Mpws,
            Test_Temperatur_After,
            Density_Water_Test_Temperature_After,
            Calibration_Weight_Pynometer_Temp_After,
            Specific_Gravity_Soil_Solid_Test_Temp_Gt,
            Temperature_Coefficent_K,
            Specific_Gravity_Soil_Solid";

        $sql .= ") VALUES (
            '$id',
            '$ProjectName',
            '$Client',
            '$ProjectNumber',
            '$SampleID',
            '$SampleNumber',
            '$Structure',
            '$Area',
            '$Source',
            '$DepthFrom',
            '$DepthTo',
            '$MType',
            '$SType',
            '$North',
            '$East',
            '$Elev',
            '$SampleBy',
            '$CollectionDate',
            '$RegistedDate',
            '$RegisterBy',
            '$TestType',
            '$Standard',
            '$SMethods',
            '$PMethods',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$FieldComment',
            '$TestMethod',
            '$PycnUsed',
            '$PycnNumber',
            '$TestTemp',
            '$MassDryPycn',
            '$VolumePycn',
            '$DensityWaterTemp',
            '$PycnWaterTeDensityWaterTempmp',
            '$WeightTare',
            '$WeightSoil',
            '$WeightPycnSoilWaterMpws',
            '$TestTempAfter',
            '$DensityWaterTempAfter',
            '$PycnWaterTempAfter',
            '$SgSoilTemp',
            '$TempCoefficent',
            '$SgSolid'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/specific-gravity.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/specific-gravity.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/specific-gravity.php', false);
    }
}
