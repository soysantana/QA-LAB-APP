<?php
 require_once('../config/load.php');
 $user = current_user();

 if (isset($_POST['unixial-compressive'])) {
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
        $TestDevice = $db->escape($_POST['TestDevice']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "UCS";

        $DimensionD = $db->escape($_POST['DimensionD']);
        $DimensionH = $db->escape($_POST['DimensionH']);
        $RelationHD = $db->escape($_POST['RelationHD']);
        $AreaM2 = $db->escape($_POST['AreaM2']);
        $VolM3 = $db->escape($_POST['VolM3']);

        $WeightKg = $db->escape($_POST['WeightKg']);
        $UnitWeigKgm3 = $db->escape($_POST['UnitWeigKgm3']);
        $FailLoadKn = $db->escape($_POST['FailLoadKn']);
        $TestTimingS = $db->escape($_POST['TestTimingS']);
        $LoadpMpas = $db->escape($_POST['LoadpMpas']);
        $UCSMpa = $db->escape($_POST['UCSMpa']);
        $FailureType = $db->escape($_POST['FailureType']);

        if ($_FILES['Graphic']['error'] === UPLOAD_ERR_OK && $_FILES['SpecimenBefore']['error'] === UPLOAD_ERR_OK && $_FILES['SpecimenAfter']['error'] === UPLOAD_ERR_OK) {
            // Manejar Graphic
            $imagen_tmp_graphic = $_FILES['Graphic']['tmp_name'];
            $imagen_data_graphic = file_get_contents($imagen_tmp_graphic);
            $imagen_data_graphic = $db->escape($imagen_data_graphic);

            // Manejar SpecimenBefore
            $imagen_tmp_before = $_FILES['SpecimenBefore']['tmp_name'];
            $imagen_data_before = file_get_contents($imagen_tmp_before);
            $imagen_data_before = $db->escape($imagen_data_before);
        
            // Manejar SpecimenAfter
            $imagen_tmp_after = $_FILES['SpecimenAfter']['tmp_name'];
            $imagen_data_after = file_get_contents($imagen_tmp_after);
            $imagen_data_after = $db->escape($imagen_data_after);
        } else {}
        
        $sql = "INSERT INTO unixial_compressive (
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
            Test_Device,
            Comments,
            Test_Start_Date,
            Registed_Date,
            Standard,
            Sample_By,
            Register_By,
            DimensionD,
            DimensionH,
            RelationHD,
            AreaM2,
            VolM3,
            WeightKg,
            UnitWeigKgm3,
            FailLoadKn,
            TestTimingS,
            LoadpMpas,
            UCSMpa,
            FailureType,
            Graphic,
            SpecimenBefore,
            SpecimenAfter,
            Test_Type";
            
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
            '$CollectionDate',
            '$Technician',
            '$ExEquip',
            '$CutterEquip',
            '$TestDevice',
            '$Comments',
            '$DateTesting',
            '$RegistedDate',
            '$Standard',
            '$SampleBy',
            '$RegisterBy',
            '$DimensionD',
            '$DimensionH',
            '$RelationHD',
            '$AreaM2',
            '$VolM3',
            '$WeightKg',
            '$UnitWeigKgm3',
            '$FailLoadKn',
            '$TestTimingS',
            '$LoadpMpas',
            '$UCSMpa',
            '$FailureType',
            '$imagen_data_graphic',
            '$imagen_data_before',
            '$imagen_data_after',
            '$TestType'";
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/unixial-compressive.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/unixial-compressive.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/unixial-compressive.php', false);
    }
 }
?>