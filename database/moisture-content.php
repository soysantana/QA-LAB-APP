<!-- Moisture Oven -->
<?php
 $user = current_user();

 if (isset($_POST['moisture-oven'])) {
    $req_fields = array(
        'SampleName',
        'Standard',
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
        $Standard = $db->escape($_POST['Standard']);
        $OvenTemp = $db->escape($_POST['OvenTemp']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "MC_Oven";

        $TareName = $db->escape($_POST['TareName']);
        $WetSoil = $db->escape($_POST['WetSoil']);
        $DrySoil = $db->escape($_POST['DrySoil']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);
        

        $sql = "INSERT INTO moisture_oven (
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
            Temperature,
            Method,
            Technician,
            Test_Start_Date,
            Comments,
            Tare_Name,
            Tare_Plus_Wet_Soil,
            Tare_Plus_Dry_Soil,
            Water_Ww,
            Tare_g,
            Dry_Soil_Ws,
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
            '$Standard',
            '$OvenTemp',
            '$TestMethod',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$TareName',
            '$WetSoil',
            '$DrySoil',
            '$Water',
            '$Tare',
            '$DrySoilWs',
            '$Moisture'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('../pages/moisture-oven.php', false);
        } else {
            $session->msg('d', 'Lo siento, no se pudo agregar el ensayo.');
            redirect('../pages/moisture-oven.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/moisture-oven.php', false);
    }
 }
?>

<!-- Update MC Oven -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-mc-oven'])) {
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
        $OvenTemp = $db->escape($_POST['OvenTemp']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "MC_Oven";

        $TareName = $db->escape($_POST['TareName']);
        $WetSoil = $db->escape($_POST['WetSoil']);
        $DrySoil = $db->escape($_POST['DrySoil']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);

        $query = "UPDATE moisture_oven SET ";
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
        $query .= "Temperature = '{$OvenTemp}', ";
        $query .= "Method = '{$TestMethod}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Tare_Name = '{$TareName}', ";
        $query .= "Tare_Plus_Wet_Soil = '{$WetSoil}', ";
        $query .= "Tare_Plus_Dry_Soil = '{$DrySoil}', ";
        $query .= "Water_Ww = '{$Water}', ";
        $query .= "Tare_g = '{$Tare}', ";
        $query .= "Dry_Soil_Ws = '{$DrySoilWs}', ";
        $query .= "Moisture_Content_Porce = '{$Moisture}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/moisture-oven.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/moisture-oven.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/moisture-oven.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat MC Oven -->
<?php
 if (isset($_POST["repeat-mc-oven"])) {
    $Search =  $_GET["id"];
    $Rcomment = $db->escape($_POST['Rcomment']);

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_oven WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];
                $TestType = "MC_Oven";

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status,
                    Comment
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat',
                    '$Rcomment'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to repeat");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/moisture-oven.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed MC Oven -->
<?php
 if (isset($_POST["reviewed-mc-oven"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_oven WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $id = uuid();
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
                redirect("../reviews/moisture-oven.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Moisture Microwave -->
<?php
 if (isset($_POST['moisture-microwave'])) {
    $req_fields = array(
        'SampleName',
        'Standard',
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
        $Standard = $db->escape($_POST['Standard']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "MC_Microwave";

        $TareName = $db->escape($_POST['TareName']);
        $MicrowaveModel = $db->escape($_POST['MicrowaveModel']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);
        for ($i = 1; $i <= 6; $i++) {
            ${"WetSoil" . $i} = $db->escape($_POST["WetSoil$i"]);
        }

        $sql = "INSERT INTO moisture_microwave (
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
            Method,
            Technician,
            Test_Start_Date,
            Comments,
            Tare_Name,
            Microwave_Model,
            Tare_Plus_Wet_Soil,
            Tare_Plus_Wet_Soil_1,
            Tare_Plus_Wet_Soil_2,
            Tare_Plus_Wet_Soil_3,
            Tare_Plus_Wet_Soil_4,
            Tare_Plus_Wet_Soil_5,
            Water_Ww,
            Tare,
            Dry_Soil,
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
            '$Standard',
            '$TestMethod',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$TareName',
            '$MicrowaveModel',
            '$WetSoil1',
            '$WetSoil2',
            '$WetSoil3',
            '$WetSoil4',
            '$WetSoil5',
            '$WetSoil6',
            '$Water',
            '$Tare',
            '$DrySoilWs',
            '$Moisture'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('../pages/moisture-microwave.php', false);
        } else {
            $session->msg('d', 'Lo siento, no se pudo agregar el ensayo.');
            redirect('../pages/moisture-microwave.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/moisture-microwave.php', false);
    }
 }
?>

<!-- Update Microwave -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-microwave'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "MC_Microwave";

        $TareName = $db->escape($_POST['TareName']);
        $MicrowaveModel = $db->escape($_POST['MicrowaveModel']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);
        for ($i = 1; $i <= 6; $i++) {
            ${"WetSoil" . $i} = $db->escape($_POST["WetSoil$i"]);
        }

        $query = "UPDATE moisture_microwave SET ";
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
        $query .= "Method = '{$TestMethod}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Tare_Name = '{$TareName}', ";
        $query .= "Microwave_Model = '{$MicrowaveModel}', ";
        $query .= "Water_Ww = '{$Water}', ";
        $query .= "Tare = '{$Tare}', ";
        $query .= "Dry_Soil = '{$DrySoilWs}', ";
        $query .= "Moisture_Content_Porce = '{$Moisture}', ";
        $query .= "Tare_Plus_Wet_Soil = '{$WetSoil1}', ";
        $query .= "Tare_Plus_Wet_Soil_1 = '{$WetSoil2}', ";
        $query .= "Tare_Plus_Wet_Soil_2 = '{$WetSoil3}', ";
        $query .= "Tare_Plus_Wet_Soil_3 = '{$WetSoil4}', ";
        $query .= "Tare_Plus_Wet_Soil_4 = '{$WetSoil5}', ";
        $query .= "Tare_Plus_Wet_Soil_5 = '{$WetSoil6}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/moisture-microwave.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/moisture-microwave.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/moisture-microwave.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat MC Microwave -->
<?php
 if (isset($_POST["repeat-mc-microwave"])) {
    $Search =  $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_microwave WHERE id = '{$Search}' LIMIT 1"
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
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
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
                redirect("../reviews/moisture-microwave.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed MC Microwave -->
<?php
 if (isset($_POST["reviewed-mc-microwave"])) {
    $Search =  $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_microwave WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/moisture-microwave.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Moisture Constant Mass -->
<?php
 if (isset($_POST['moisture-constant-mass'])) {
    $req_fields = array(
        'SampleName',
        'Standard',
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
        $Standard = $db->escape($_POST['Standard']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "MC_Constant_Mass";

        $TareName = $db->escape($_POST['TareName']);
        $OvenTemp = $db->escape($_POST['OvenTemp']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);
        for ($i = 1; $i <= 6; $i++) {
            ${"WetSoil" . $i} = $db->escape($_POST["WetSoil$i"]);
        }

        $sql = "INSERT INTO moisture_constant_mass (
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
            Method,
            Technician,
            Test_Start_Date,
            Comments,
            Tare_Name,
            Temperature,
            Tare_Plus_Wet_Soil,
            Tare_Plus_Wet_Soil_1,
            Tare_Plus_Wet_Soil_2,
            Tare_Plus_Wet_Soil_3,
            Tare_Plus_Wet_Soil_4,
            Tare_Plus_Wet_Soil_5,
            Water_Ww,
            Tare,
            Dry_Soil,
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
            '$Standard',
            '$TestMethod',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$TareName',
            '$OvenTemp',
            '$WetSoil1',
            '$WetSoil2',
            '$WetSoil3',
            '$WetSoil4',
            '$WetSoil5',
            '$WetSoil6',
            '$Water',
            '$Tare',
            '$DrySoilWs',
            '$Moisture'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('../pages/moisture-microwave.php', false);
        } else {
            $session->msg('d', 'Lo siento, no se pudo agregar el ensayo.');
            redirect('../pages/moisture-microwave.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/moisture-microwave.php', false);
    }
 }
?>

<!-- Update MC Constant Mass -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-mc-constant-mass'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "MC_Constant_Mass";

        $TareName = $db->escape($_POST['TareName']);
        $OvenTemp = $db->escape($_POST['OvenTemp']);
        $Water = $db->escape($_POST['Water']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $Moisture = $db->escape($_POST['Moisture']);
        for ($i = 1; $i <= 6; $i++) {
            ${"WetSoil" . $i} = $db->escape($_POST["WetSoil$i"]);
        }

        $query = "UPDATE moisture_constant_mass SET ";
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
        $query .= "Method = '{$TestMethod}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Tare_Name = '{$TareName}', ";
        $query .= "Temperature = '{$OvenTemp}', ";
        $query .= "Water_Ww = '{$Water}', ";
        $query .= "Tare = '{$Tare}', ";
        $query .= "Dry_Soil = '{$DrySoilWs}', ";
        $query .= "Moisture_Content_Porce = '{$Moisture}', ";
        $query .= "Tare_Plus_Wet_Soil = '{$WetSoil1}', ";
        $query .= "Tare_Plus_Wet_Soil_1 = '{$WetSoil2}', ";
        $query .= "Tare_Plus_Wet_Soil_2 = '{$WetSoil3}', ";
        $query .= "Tare_Plus_Wet_Soil_3 = '{$WetSoil4}', ";
        $query .= "Tare_Plus_Wet_Soil_4 = '{$WetSoil5}', ";
        $query .= "Tare_Plus_Wet_Soil_5 = '{$WetSoil6}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/moisture-constant-mass.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/moisture-constant-mass.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/moisture-constant-mass.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat MC Constant Mass -->
<?php
 if (isset($_POST["repeat-mc-constant-mass"])) {
    $Search =  $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_constant_mass WHERE id = '{$Search}' LIMIT 1"
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
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
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
                redirect("../reviews/moisture-constant-mass.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed MC Constant Mass -->
<?php
 if (isset($_POST["reviewed-mc-constant-mass"])) {
    $Search =  $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM moisture_constant_mass WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/moisture-constant-mass.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>