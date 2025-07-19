<?php
$user = current_user();

if (isset($_POST['save'])) {
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
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "LAA_Large";
        $id = uuid();

        $NominalMaxSize = $db->escape($_POST['NominalMaxSize']);
        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $NoSpheres = $db->escape($_POST['NoSpheres']);
        $WeigSpheres = $db->escape($_POST['WeigSpheres']);
        $Revolutions = $db->escape($_POST['Revolutions']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);

        $sql = "INSERT INTO los_angeles_abrasion_large (
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
            Technician,
            Methods,
            Preparation_Method,
            Split_Method,
            Test_Start_Date,
            Comments,
            FieldComment,
            NominalMaxSize,
            Grading,
            NoSpheres,
            Weight_Spheres,
            Revolutions,
            Initial_Weight,
            Final_Weight,
            Weight_Loss,
            Weight_Loss_Porce";

        $sql .= ") 
            
            VALUES (
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
            '$Technician',
            '$TestMethod',
            '$PMethods',
            '$SMethods',
            '$DateTesting',
            '$Comments',
            '$FieldComment',
            '$NominalMaxSize',
            '$SelectGrading',
            '$NoSpheres',
            '$WeigSpheres',
            '$Revolutions',
            '$InitWeig',
            '$FinalWeig',
            '$WeigLoss',
            '$WeigLossPorce'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('../pages/LAA-Large.php', false);
        } else {
            $session->msg('d', 'Lo siento, el ensayo no se pudo agregar.');
            redirect('../pages/LAA-Large.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/LAA-Large.php', false);
    }
}
