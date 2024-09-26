<?php
 $user = current_user();

 if (isset($_POST['save_mc_scale'])) {
    $req_fields = array(
        'SampleName',
        'Technician',
        'DateTesting'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $id = uuid();
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "MC_Scale";

        $TareName = $db->escape($_POST['TareName']);
        $Moisture = $db->escape($_POST['Moisture']);
        

        $sql = "INSERT INTO moisture_scale (
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
            Methods,
            Technician,
            Test_Start_Date,
            Comments,
            Tare_Name,
            Moisture_Content_Porce
        )
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
            '$TestMethod',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$TareName',
            '$Moisture'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('/pages/moisture-scale.php', false);
        } else {
            $session->msg('d', 'Lo siento, no se pudo agregar el ensayo.');
            redirect('/pages/moisture-scale.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/moisture-scale.php', false);
    }
 }
?>