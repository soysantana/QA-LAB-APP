<?php
 require_once('../config/load.php');
 $user = current_user();

 if (isset($_POST['brazilian'])) {
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
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $ExEquip = $db->escape($_POST['ExEquip']);
        $CutterEquip = $db->escape($_POST['CutterEquip']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "BTT";
        $id = uuid();

        if ($_FILES['SpecimenBefore']['error'] === UPLOAD_ERR_OK && $_FILES['SpecimenAfter']['error'] === UPLOAD_ERR_OK) {
            // Manejar SpecimenBefore
            $imagen_tmp_before = $_FILES['SpecimenBefore']['tmp_name'];
            $imagen_data_before = file_get_contents($imagen_tmp_before);
            $imagen_data_before = $db->escape($imagen_data_before);
        
            // Manejar SpecimenAfter
            $imagen_tmp_after = $_FILES['SpecimenAfter']['tmp_name'];
            $imagen_data_after = file_get_contents($imagen_tmp_after);
            $imagen_data_after = $db->escape($imagen_data_after);
        } else {}

        $DcmNoAvge = $db->escape($_POST['DcmNoAvge']);
        $TcmNoAvge = $db->escape($_POST['TcmNoAvge']);
        $ReltdNoAvge = $db->escape($_POST['ReltdNoAvge']);
        $LoandNoAvge = $db->escape($_POST['LoandNoAvge']);
        $TimeFaiNoAvge = $db->escape($_POST['TimeFaiNoAvge']);
        $MaxKnNoAvge = $db->escape($_POST['MaxKnNoAvge']);
        $TensStrNoAvge = $db->escape($_POST['TensStrNoAvge']);
        $FailureAvge = $db->escape($_POST['FailureAvge']);

        for ($i = 1; $i <= 10; $i++) {
            ${"DcmNo" . $i} = $db->escape($_POST["DcmNo$i"]);
            ${"TcmNo" . $i} = $db->escape($_POST["TcmNo$i"]);
            ${"ReltdNo" . $i} = $db->escape($_POST["ReltdNo$i"]);
            ${"LoandNo" . $i} = $db->escape($_POST["LoandNo$i"]);
            ${"TimeFaiNo" . $i} = $db->escape($_POST["TimeFaiNo$i"]);
            ${"MaxKnNo" . $i} = $db->escape($_POST["MaxKnNo$i"]);
            ${"TensStrNo" . $i} = $db->escape($_POST["TensStrNo$i"]);
            ${"FailureNo" . $i} = $db->escape($_POST["FailureNo$i"]);
        }
        
        $sql = "INSERT INTO brazilian (
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
            Sample_Date,
            Technician,
            Extraction_Equipment,
            Cutter_Equipment,
            Methods,
            Comments,
            Test_Start_Date,
            Registed_Date,
            Standard,
            Sample_By,
            Register_By,
            Test_Type,
            DcmNoAvge,
            TcmNoAvge,
            ReltdNoAvge,
            LoandNoAvge,
            TimeFaiNoAvge, 
            MaxKnNoAvge, 
            TensStrNoAvge, 
            FailureNoAvge,
            SpecimenBefore,
            SpecimenAfter";

            // Add the dynamically generated fields to the query
            for ($i = 1; $i <= 10; $i++) {
                $sql .= ", DcmNo$i, TcmNo$i, ReltdNo$i, LoandNo$i, TimeFaiNo$i, MaxKnNo$i, TensStrNo$i, FailureNo$i";
            }
            
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
            '$CollectionDate',
            '$Technician',
            '$ExEquip',
            '$CutterEquip',
            '$TestMethod',
            '$Comments',
            '$DateTesting',
            '$RegistedDate',
            '$Standard',
            '$SampleBy',
            '$RegisterBy',
            '$TestType',
            '$DcmNoAvge',
            '$TcmNoAvge',
            '$ReltdNoAvge',
            '$LoandNoAvge',
            '$TimeFaiNoAvge',
            '$MaxKnNoAvge',
            '$TensStrNoAvge',
            '$FailureAvge',
            '$imagen_data_before',
            '$imagen_data_after'";

            // Add the dynamically generated values to the query
            for ($i = 1; $i <= 10; $i++) {
                $sql .= ", '${"DcmNo$i"}', '${"TcmNo$i"}', '${"ReltdNo$i"}', '${"LoandNo$i"}', '${"TimeFaiNo$i"}', '${"MaxKnNo$i"}', '${"TensStrNo$i"}', '${"FailureNo$i"}'";
            }
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/brazilian.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/brazilian.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/brazilian.php', false);
    }
 }
?>