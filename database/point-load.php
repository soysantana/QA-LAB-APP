<?php
 require_once('../config/load.php');
 $user = current_user();

 if (isset($_POST['point-load'])) {
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
        $CuttEquip = $db->escape($_POST['CuttEquip']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "PLT";

        $JackPiston = $db->escape($_POST['JackPiston']);
        $K1assumed = $db->escape($_POST['K1assumed']);
        $K2assumed = $db->escape($_POST['K2assumed']);

        $TypeABCD = $db->escape($_POST['TypeABCD']);
        $DimensionL = $db->escape($_POST['DimensionL']);
        $DimensionD = $db->escape($_POST['DimensionD']);
        $PlattensSeparation = $db->escape($_POST['PlattensSeparation']);
        $LoadDirection = $db->escape($_POST['LoadDirection']);
        $GaugeReading = $db->escape($_POST['GaugeReading']);
        $FailureLoad = $db->escape($_POST['FailureLoad']);
        $Demm = $db->escape($_POST['Demm']);
        $IsMpa = $db->escape($_POST['IsMpa']);
        $F = $db->escape($_POST['F']);
        $Is50 = $db->escape($_POST['Is50']);
        $UCSK1Mpa = $db->escape($_POST['UCSK1Mpa']);
        $UCSK2Mpa = $db->escape($_POST['UCSK2Mpa']);
        $Classification = $db->escape($_POST['Classification']);

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
        
        $sql = "INSERT INTO point_load (
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
            Test_Start_Date,
            Comments,
            Extraction_Equipment,
            Cutter_Equipment,
            Methods,
            JackPiston,
            K1assumed,
            K2assumed,
            TypeABCD,
            DimensionL,
            DimensionD,
            PlattensSeparation,
            LoadDirection,
            GaugeReading,
            FailureLoad,
            Demm,
            IsMpa,
            F,
            Is50,
            UCSK1Mpa,
            UCSK2Mpa,
            Classification,
            SpecimenBefore,
            SpecimenAfter";
            
            $sql .= ") 
            
            VALUES (
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
            '$DateTesting',
            '$Comments',
            '$ExEquip',
            '$CuttEquip',
            '$TestMethod',
            '$JackPiston',
            '$K1assumed',
            '$K2assumed',
            '$TypeABCD',
            '$DimensionL',
            '$DimensionD',
            '$PlattensSeparation',
            '$LoadDirection',
            '$GaugeReading',
            '$FailureLoad',
            '$Demm',
            '$IsMpa',
            '$F',
            '$Is50',
            '$UCSK1Mpa',
            '$UCSK2Mpa',
            '$Classification',
            '$imagen_data_before',
            '$imagen_data_after'";
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/point-load.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/point-load.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/point-load.php', false);
    }
 }
?>