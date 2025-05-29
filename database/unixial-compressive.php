<!-- UCS -->
<?php
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        $ExEquip = $db->escape($_POST['ExEquip']);
        $CutterEquip = $db->escape($_POST['CutterEquip']);
        $TestDevice = $db->escape($_POST['TestDevice']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "UCS";
        $id = uuid();

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
        } else {
        }

        $sql = "INSERT INTO unixial_compressive (
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
            Test_Device,
            Comments,
            FieldComment,
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
            '$TestDevice',
            '$Comments',
            '$FieldComment',
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

<!-- Update UCS -->
<?php
$Search = $_GET['id'];
if (isset($_POST['Update_UCS'])) {
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        $ExEquip = $db->escape($_POST['ExEquip']);
        $CutterEquip = $db->escape($_POST['CutterEquip']);
        $TestDevice = $db->escape($_POST['TestDevice']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
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
        } else {
        }

        $query = "UPDATE unixial_compressive SET ";
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
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";

        $query .= "Extraction_Equipment = '{$ExEquip}', ";
        $query .= "Cutter_Equipment = '{$CutterEquip}', ";
        $query .= "Test_Device = '{$TestDevice}', ";
        $query .= "DimensionD = '{$DimensionD}', ";
        $query .= "DimensionH = '{$DimensionH}', ";
        $query .= "RelationHD = '{$RelationHD}', ";
        $query .= "AreaM2 = '{$AreaM2}', ";
        $query .= "VolM3 = '{$VolM3}', ";
        $query .= "WeightKg = '{$WeightKg}', ";
        $query .= "UnitWeigKgm3 = '{$UnitWeigKgm3}', ";
        $query .= "FailLoadKn = '{$FailLoadKn}', ";
        $query .= "TestTimingS = '{$TestTimingS}', ";
        $query .= "LoadpMpas = '{$LoadpMpas}', ";
        $query .= "UCSMpa = '{$UCSMpa}', ";
        $query .= "FailureType = '{$FailureType}', ";
        $query .= "Graphic = '{$imagen_data_graphic}', ";
        $query .= "SpecimenBefore = '{$imagen_data_before}', ";
        $query .= "SpecimenAfter = '{$imagen_data_after}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/unixial-compressive.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/unixial-compressive.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/unixial-compressive.php?id=' . $Search, false);
    }
}
?>

<!-- Repeat UCS -->
<?php
if (isset($_POST["Repeat_UCS"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM unixial_compressive WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to repeat");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/unixial-compressive.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Reviewed UCS -->
<?php
if (isset($_POST["Reviewed_UCS"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM unixial_compressive WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Reviewed_By,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$ReviewedBy',
                    '$RegisBy',
                    '$TestType',
                    '$ID',
                    'Reviewed'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to reviewd");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/unixial-compressive.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<?php
if (isset($_POST['delete_ucs']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('unixial_compressive', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
?>