<!-- Brazilian -->
<?php
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        $ExEquip = $db->escape($_POST['ExEquip']);
        $CutterEquip = $db->escape($_POST['CutterEquip']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "BTS";
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
        } else {
        }

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

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM brazilian
             WHERE Sample_ID = '{$SampleID}'
               AND Test_Type = '{$TestType}'
               AND (Sample_Number = '{$baseSampleNumber}' OR Sample_Number LIKE '{$baseSampleNumber}-%')
             ORDER BY id ASC";

        $resultCheck = $db->query($sqlCheck);

        if ($db->num_rows($resultCheck) > 0) {
            $maxSuffix = 0;
            while ($row = $db->fetch_assoc($resultCheck)) {
                if (preg_match('/-R(\d+)$/', $row['Sample_Number'], $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxSuffix) {
                        $maxSuffix = $num;
                    }
                }
            }
            // generar el nuevo SampleNumber con sufijo +1
            $SampleNumber = $baseSampleNumber . '-R' . ($maxSuffix + 1);
        }
        // --- Fin verificación ---

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
            FieldComment,
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
            '$FieldComment',
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

<!-- Update Brazilian -->
<?php
$Search = $_GET['id'];
if (isset($_POST['Update_Brazilian'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "BTS";

        if ($_FILES['SpecimenBefore']['error'] === UPLOAD_ERR_OK && $_FILES['SpecimenAfter']['error'] === UPLOAD_ERR_OK) {
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

        $DcmNoAvge = $db->escape($_POST['DcmNoAvge']);
        $TcmNoAvge = $db->escape($_POST['TcmNoAvge']);
        $ReltdNoAvge = $db->escape($_POST['ReltdNoAvge']);
        $LoandNoAvge = $db->escape($_POST['LoandNoAvge']);
        $TimeFaiNoAvge = $db->escape($_POST['TimeFaiNoAvge']);
        $MaxKnNoAvge = $db->escape($_POST['MaxKnNoAvge']);
        $TensStrNoAvge = $db->escape($_POST['TensStrNoAvge']);
        $FailureAvge = $db->escape($_POST['FailureAvge']);

        $inputValues = array();
        for ($i = 1; $i <= 10; $i++) {
            $inputValues["DcmNo" . $i] = $db->escape($_POST["DcmNo$i"]);
            $inputValues["TcmNo" . $i] = $db->escape($_POST["TcmNo$i"]);
            $inputValues["ReltdNo" . $i] = $db->escape($_POST["ReltdNo$i"]);
            $inputValues["LoandNo" . $i] = $db->escape($_POST["LoandNo$i"]);
            $inputValues["TimeFaiNo" . $i] = $db->escape($_POST["TimeFaiNo$i"]);
            $inputValues["MaxKnNo" . $i] = $db->escape($_POST["MaxKnNo$i"]);
            $inputValues["TensStrNo" . $i] = $db->escape($_POST["TensStrNo$i"]);
            $inputValues["FailureNo" . $i] = $db->escape($_POST["FailureNo$i"]);
        }

        $query = "UPDATE brazilian SET ";
        foreach ($inputValues as $key => $value) {
            $query .= "$key = '$value', ";
        }
        $query .= "Project_Name = '{$ProjectName}',";
        $query .= "Client = '{$Client}', ";
        $query .= "Project_Number = '{$ProjectNumber}', ";
        $query .= "Sample_ID = '{$SampleID}', ";
        $query .= "Sample_Number = '{$SampleNumber}', ";
        $query .= "Structure = '{$Structure}', ";
        $query .= "Area = '{$Area}', ";
        $query .= "Source = '{$Source}', ";
        $query .= "Depth_From = '{$DepthFrom}', ";
        $query .= "Depth_To = '{$DepthTo}', ";
        $query .= "Material_Type = '{$MType}', ";
        $query .= "Sample_Type = '{$SType}', ";
        $query .= "North = '{$North}', ";
        $query .= "East = '{$East}', ";
        $query .= "Elev = '{$Elev}', ";
        $query .= "Sample_Date = '{$CollectionDate}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Extraction_Equipment = '{$ExEquip}', ";
        $query .= "Cutter_Equipment = '{$CutterEquip}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Sample_By = '{$SampleBy}', ";
        $query .= "Standard = '{$Standard}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "DcmNoAvge = '{$DcmNoAvge}', ";
        $query .= "TcmNoAvge = '{$TcmNoAvge}', ";
        $query .= "ReltdNoAvge = '{$ReltdNoAvge}', ";
        $query .= "LoandNoAvge = '{$LoandNoAvge}', ";
        $query .= "TimeFaiNoAvge = '{$TimeFaiNoAvge}', ";
        $query .= "MaxKnNoAvge = '{$MaxKnNoAvge}', ";
        $query .= "TensStrNoAvge = '{$TensStrNoAvge}', ";
        $query .= "FailureNoAvge = '{$FailureAvge}', ";
        $query .= "SpecimenBefore = '{$imagen_data_before}', ";
        $query .= "SpecimenAfter = '{$imagen_data_after}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/brazilian.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/brazilian.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/brazilian.php?id=' . $Search, false);
    }
}
?>

<!-- Repeat Brazilian -->
<?php
if (isset($_POST["Repeat_Brazilian"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM brazilian WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_ID = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_ID,
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
                redirect("../reviews/brazilian.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Reviewed Brazilian -->
<?php
if (isset($_POST["Reviewed_Brazilian"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM brazilian WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_ID = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_ID,
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
                redirect("../reviews/brazilian.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Delete -->
<?php
if (isset($_POST['delete_bts']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('brazilian', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
?>