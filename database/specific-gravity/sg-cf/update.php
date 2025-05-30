<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update-sg-coarse'])) {
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
        $TestType = "SG-Coarse";

        $SpecificGravityOD = $db->escape($_POST['SpecificGravityOD']);
        $SpecificGravitySSD = $db->escape($_POST['SpecificGravitySSD']);
        $ApparentSpecificGravity = $db->escape($_POST['ApparentSpecificGravity']);
        $PercentAbsortion = $db->escape($_POST['PercentAbsortion']);

        $inputValues = array();
        for ($i = 1; $i <= 10; $i++) {
            $inputValues["Oven_Dry_" . $i] = $db->escape($_POST["OvenDry$i"]);
            $inputValues["Surface_Dry_" . $i] = $db->escape($_POST["SurfaceDry$i"]);
            $inputValues["Samp_Immers_" . $i] = $db->escape($_POST["SampImmers$i"]);
        }

        $query = "UPDATE specific_gravity_coarse SET ";
        foreach ($inputValues as $key => $value) {
            $query .= "$key = '$value', ";
        }
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
        $query .= "Specific_Gravity_OD = '{$SpecificGravityOD}', ";
        $query .= "Specific_Gravity_SSD = '{$SpecificGravitySSD}', ";
        $query .= "Apparent_Specific_Gravity = '{$ApparentSpecificGravity}', ";
        $query .= "Percent_Absortion = '{$PercentAbsortion}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
    }
}
