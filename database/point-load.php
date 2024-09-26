<!-- Point Load -->
<?php
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
        $id = uuid();

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

<!-- Update Point Load -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['Update_PLT'])) {
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
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

        $query = "UPDATE point_load SET ";
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
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";

        $query .= "Extraction_Equipment = '{$ExEquip}', ";
        $query .= "Cutter_Equipment = '{$CuttEquip}', ";
        $query .= "JackPiston = '{$JackPiston}', ";
        $query .= "K1assumed = '{$K1assumed}', ";
        $query .= "K2assumed = '{$K2assumed}', ";
        $query .= "TypeABCD = '{$TypeABCD}', ";
        $query .= "DimensionL = '{$DimensionL}', ";
        $query .= "DimensionD = '{$DimensionD}', ";
        $query .= "PlattensSeparation = '{$PlattensSeparation}', ";
        $query .= "LoadDirection = '{$LoadDirection}', ";
        $query .= "GaugeReading = '{$GaugeReading}', ";
        $query .= "FailureLoad = '{$FailureLoad}', ";
        $query .= "Demm = '{$Demm}', ";
        $query .= "IsMpa = '{$IsMpa}', ";
        $query .= "F = '{$F}', ";
        $query .= "Is50 = '{$Is50}', ";
        $query .= "UCSK1Mpa = '{$UCSK1Mpa}', ";
        $query .= "UCSK2Mpa = '{$UCSK2Mpa}', ";
        $query .= "Classification = '{$Classification}', ";
        $query .= "SpecimenBefore = '{$imagen_data_before}', ";
        $query .= "SpecimenAfter = '{$imagen_data_after}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/point-load.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/point-load.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/point-load.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat Point Load -->
<?php
 if (isset($_POST["Repeat_PLT"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM point_load WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/point-load.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed PLT -->
<?php
 if (isset($_POST["Reviewed_PLT"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM point_load WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/point-load.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Delete PLT -->
<?php
 if (isset($_POST['delete_plt']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('point_load', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>