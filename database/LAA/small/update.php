<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update'])) {
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        // ohters
        $Standard = $db->escape($_POST['Standard']);
        $Technician = $db->escape($_POST['Technician']);
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $Modified_Date = make_date();
        $Modified_By = $user['name'];
        $TestType = "LAA_Small";

        $NominalMaxSize = $db->escape($_POST['NominalMaxSize']);
        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $WeigSpheres = $db->escape($_POST['WeigSpheres']);
        $Revolutions = $db->escape($_POST['Revolutions']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);

        $query = "UPDATE los_angeles_abrasion_small SET ";
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
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Standard = '{$Standard}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Preparation_Method = '{$PMethods}', ";
        $query .= "Split_Method = '{$SMethods}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Modified_Date = '{$Modified_Date}', ";
        $query .= "Modified_By = '{$Modified_By}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "NominalMaxSize = '{$NominalMaxSize}', ";
        $query .= "Grading = '{$SelectGrading}', ";
        $query .= "Weight_Spheres = '{$WeigSpheres}', ";
        $query .= "Revolutions = '{$Revolutions}', ";
        $query .= "Initial_Weight = '{$InitWeig}', ";
        $query .= "Final_Weight = '{$FinalWeig}', ";
        $query .= "Weight_Loss = '{$WeigLoss}', ";
        $query .= "Weight_Loss_Porce = '{$WeigLossPorce}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('../reviews/LAA-Small.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se realizaron cambios');
            redirect('../reviews/LAA-Small.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/LAA-Small.php?id=' . $Search, false);
    }
}
