<!-- Grain Size General -->
<?php
$user = current_user();

if (isset($_POST['grain-size-general'])) {
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
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "GS";
        $id = uuid();

        $Container = $db->escape($_POST['Container']);
        $WetSoil = $db->escape($_POST['WetSoil']);
        $DrySoilTare = $db->escape($_POST['DrySoilTare']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoil = $db->escape($_POST['DrySoil']);
        $Washed = $db->escape($_POST['Washed']);
        $WashPan = $db->escape($_POST['WashPan']);

        $CoarserGravel = $db->escape($_POST['CoarserGravel']);
        $Gravel = $db->escape($_POST['Gravel']);
        $Sand = $db->escape($_POST['Sand']);
        $Fines = $db->escape($_POST['Fines']);
        $D10 = $db->escape($_POST['D10']);
        $D15 = $db->escape($_POST['D15']);
        $D30 = $db->escape($_POST['D30']);
        $D60 = $db->escape($_POST['D60']);
        $D85 = $db->escape($_POST['D85']);
        $Cc = $db->escape($_POST['Cc']);
        $Cu = $db->escape($_POST['Cu']);
        $ClassificationUSCS1 = $db->escape($_POST['ClassificationUSCS1']);
        $ClassificationUSCS2 = $db->escape($_POST['ClassificationUSCS2']);

        $PanWtRen = $db->escape($_POST['PanWtRen']);
        $PanRet = $db->escape($_POST['PanRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        for ($i = 1; $i <= 22; $i++) {
            ${"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            ${"Ret" . $i} = $db->escape($_POST["Ret$i"]);
            ${"CumRet" . $i} = $db->escape($_POST["CumRet$i"]);
            ${"Pass" . $i} = $db->escape($_POST["Pass$i"]);
        }

        $sql = "INSERT INTO grain_size_general (
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
            FieldComment,
            Preparation_Method,
            Split_Method,
            Methods,
            Container,
            Wet_Soil_Tare,
            Wet_Dry_Tare,
            Tare,
            Wt_Dry_Soil,
            Wt_Washed,
            Wt_Wash_Pan,
            Coarser_than_Gravel,
            Gravel,
            Sand,
            Fines,
            D10,
            D15,
            D30,
            D60,
            D85,
            Cc,
            Cu,
            ClassificationUSCS1,
            ClassificationUSCS2,
            PanWtRen,
            PanRet,
            TotalWtRet,
            TotalRet,
            TotalCumRet,
            TotalPass,
            Graph";

        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 22; $i++) {
            $sql .= ", WtRet$i, Ret$i, CumRet$i, Pass$i";
        }

        $sql .= ") VALUES (
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
            '$FieldComment',
            '$PMethods',
            '$SMethods',
            '$TestMethod',
            '$Container',
            '$WetSoil',
            '$DrySoilTare',
            '$Tare',
            '$DrySoil',
            '$Washed',
            '$WashPan',
            '$CoarserGravel',
            '$Gravel',
            '$Sand',
            '$Fines',
            '$D10',
            '$D15',
            '$D30',
            '$D60',
            '$D85',
            '$Cc',
            '$Cu',
            '$ClassificationUSCS1',
            '$ClassificationUSCS2',
            '$PanWtRen',
            '$PanRet',
            '$TotalWtRet',
            '$TotalRet',
            '$TotalCumRet',
            '$TotalPass',
            '$Graph64'";

        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 22; $i++) {
            $sql .= ", '${"WtRet$i"}', '${"Ret$i"}', '${"CumRet$i"}', '${"Pass$i"}'";
        }

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/grain-size.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/grain-size.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/grain-size.php', false);
    }
}
?>

<!-- Update GS General -->
<?php
$Search = $_GET['id'];
if (isset($_POST['update-gs-general'])) {
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
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "GS";

        $Container = $db->escape($_POST['Container']);
        $WetSoil = $db->escape($_POST['WetSoil']);
        $DrySoilTare = $db->escape($_POST['DrySoilTare']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoil = $db->escape($_POST['DrySoil']);
        $Washed = $db->escape($_POST['Washed']);
        $WashPan = $db->escape($_POST['WashPan']);

        $CoarserGravel = $db->escape($_POST['CoarserGravel']);
        $Gravel = $db->escape($_POST['Gravel']);
        $Sand = $db->escape($_POST['Sand']);
        $Fines = $db->escape($_POST['Fines']);
        $D10 = $db->escape($_POST['D10']);
        $D15 = $db->escape($_POST['D15']);
        $D30 = $db->escape($_POST['D30']);
        $D60 = $db->escape($_POST['D60']);
        $D85 = $db->escape($_POST['D85']);
        $Cc = $db->escape($_POST['Cc']);
        $Cu = $db->escape($_POST['Cu']);
        $ClassificationUSCS1 = $db->escape($_POST['ClassificationUSCS1']);
        $ClassificationUSCS2 = $db->escape($_POST['ClassificationUSCS2']);

        $PanWtRen = $db->escape($_POST['PanWtRen']);
        $PanRet = $db->escape($_POST['PanRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        $inputValues = array();
        for ($i = 1; $i <= 22; $i++) {
            $inputValues["WtRet" . $i] = $db->escape($_POST["WtRet$i"]);
            $inputValues["Ret" . $i] = $db->escape($_POST["Ret$i"]);
            $inputValues["CumRet" . $i] = $db->escape($_POST["CumRet$i"]);
            $inputValues["Pass" . $i] = $db->escape($_POST["Pass$i"]);
        }

        $query = "UPDATE grain_size_general SET ";
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
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Preparation_Method = '{$PMethods}', ";
        $query .= "Split_Method = '{$SMethods}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Container = '{$Container}', ";
        $query .= "Wet_Soil_Tare = '{$WetSoil}', ";
        $query .= "Wet_Dry_Tare = '{$DrySoilTare}', ";
        $query .= "Tare = '{$Tare}', ";
        $query .= "Wt_Dry_Soil = '{$DrySoil}', ";
        $query .= "Wt_Washed = '{$Washed}', ";
        $query .= "Wt_Wash_Pan = '{$WashPan}', ";
        $query .= "Coarser_than_Gravel = '{$CoarserGravel}', ";
        $query .= "Gravel = '{$Gravel}', ";
        $query .= "Sand = '{$Sand}', ";
        $query .= "Fines = '{$Fines}', ";
        $query .= "D10 = '{$D10}', ";
        $query .= "D15 = '{$D15}', ";
        $query .= "D30 = '{$D30}', ";
        $query .= "D60 = '{$D60}', ";
        $query .= "D85 = '{$D85}', ";
        $query .= "Cc = '{$Cc}', ";
        $query .= "Cu = '{$Cu}', ";
        $query .= "ClassificationUSCS1 = '{$ClassificationUSCS1}', ";
        $query .= "ClassificationUSCS2 = '{$ClassificationUSCS2}', ";
        $query .= "PanWtRen = '{$PanWtRen}', ";
        $query .= "PanRet = '{$PanRet}', ";
        $query .= "TotalWtRet = '{$TotalWtRet}', ";
        $query .= "TotalRet = '{$TotalRet}', ";
        $query .= "TotalCumRet = '{$TotalCumRet}', ";
        $query .= "TotalPass = '{$TotalPass}', ";
        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/grain-size.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/grain-size.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/grain-size.php?id=' . $Search, false);
    }
}
?>

<!-- Repeat GS General -->
<?php
if (isset($_POST["repeat-gs-general"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM grain_size_general WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/grain-size.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Reviewed GS General -->
<?php
if (isset($_POST["reviewed-gs-general"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM grain_size_general WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/grain-size.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Delete GS General -->
<?php
if (isset($_POST['delete_gs_general']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('grain_size_general', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
?>