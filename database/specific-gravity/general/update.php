<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update-sg'])) {
    $req_fields = array(
        'SampleName',
        'Standard',
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SG";

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

        $query = "UPDATE specific_gravity SET ";
        $query .= "Project_Name = '{$ProjectName}',";
        $query .= "Client = '{$Client}', ";
        $query .= "Project_Number = '{$ProjectNumber}', ";
        $query .= "Structure = '{$Structure}', ";
        $query .= "Area = '{$Area}', ";
        $query .= "Source = '{$Source}', ";
        $query .= "Sample_Date = '{$CollectionDate}', ";
        $query .= "Sample_ID = '{$SampleID}', ";
        $query .= "Sample_Number = '{$SampleNumber}', ";
        $query .= "Depth_From = '{$DepthFrom}', ";
        $query .= "Depth_To = '{$DepthTo}', ";
        $query .= "Material_Type = '{$MType}', ";
        $query .= "Sample_Type = '{$SType}', ";
        $query .= "North = '{$North}', ";
        $query .= "East = '{$East}', ";
        $query .= "Elev = '{$Elev}', ";
        $query .= "Sample_By = '{$SampleBy}', ";
        $query .= "Standard = '{$Standard}', ";
        $query .= "SMethods = '{$SMethods}', ";
        $query .= "PMethods = '{$PMethods}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Pycnometer_Used = '{$PycnUsed}', ";
        $query .= "Pycnometer_Number = '{$PycnNumber}', ";
        $query .= "Test_Temperatur = '{$TestTemp}', ";
        $query .= "Average_Calibrated_Mass_Dry_Pycnometer_Mp = '{$MassDryPycn}', ";
        $query .= "Average_Calibrated_Volume_Pycnometer_Vp = '{$VolumePycn}', ";
        $query .= "Density_Water_Test_Temperature = '{$DensityWaterTemp}', ";
        $query .= "Calibration_Weight_Pynometer_Temperature_Mpw = '{$PycnWaterTeDensityWaterTempmp}', ";
        $query .= "Weight__Dry_Soil_Tare = '{$WeightTare}', ";
        $query .= "Weight_Dry_Soil_Ms = '{$WeightSoil}', ";
        $query .= "Weight_Pycnometer_Soil_Water_Mpws = '{$WeightPycnSoilWaterMpws}', ";
        $query .= "Test_Temperatur_After = '{$TestTempAfter}', ";
        $query .= "Density_Water_Test_Temperature_After = '{$DensityWaterTempAfter}', ";
        $query .= "Calibration_Weight_Pynometer_Temp_After = '{$PycnWaterTempAfter}', ";
        $query .= "Specific_Gravity_Soil_Solid_Test_Temp_Gt = '{$SgSoilTemp}', ";
        $query .= "Temperature_Coefficent_K = '{$TempCoefficent}', ";
        $query .= "Specific_Gravity_Soil_Solid = '{$SgSolid}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/specific-gravity.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/specific-gravity.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/specific-gravity.php?id=' . $Search, false);
    }
}
