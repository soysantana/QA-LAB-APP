<!-- LAA Coarse Filter -->
<?php
 $user = current_user();

 if (isset($_POST['LAA_Coarse_Filter'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "LAA_Coarse_Filter";
        $id = uuid();

        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $WeigSpheres = $db->escape($_POST['WeigSpheres']);
        $Revolutions = $db->escape($_POST['Revolutions']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);
        
        $sql = "INSERT INTO los_angeles_abrasion_coarse_filter (
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
            Methods,
            Grading,
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
            '$DateTesting',
            '$Comments',
            '$TestMethod',
            '$SelectGrading',
            '$WeigSpheres',
            '$Revolutions',
            '$InitWeig',
            '$FinalWeig',
            '$WeigLoss',
            '$WeigLossPorce'";
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/LAA-Small.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/LAA-Small.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/LAA-Small.php', false);
    }
 }
?>

<!-- Update LAA Coarse Filter -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['Update_LAA_Coarse_Filter'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Modified_Date = make_date();
        $Modified_By = $user['name'];
        $TestType = "LAA_Coarse_Filter";

        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $WeigSpheres = $db->escape($_POST['WeigSpheres']);
        $Revolutions = $db->escape($_POST['Revolutions']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);

        $query = "UPDATE los_angeles_abrasion_coarse_filter SET ";
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
        $query .= "Modified_Date = '{$Modified_Date}', ";
        $query .= "Modified_By = '{$Modified_By}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Grading = '{$SelectGrading}', ";
        $query .= "Weight_Spheres = '{$WeigSpheres}', ";
        $query .= "Revolutions = '{$Revolutions}', ";
        $query .= "Initial_Weight = '{$InitWeig}', ";
        $query .= "Final_Weight = '{$FinalWeig}', ";
        $query .= "Weight_Loss = '{$WeigLoss}', ";
        $query .= "Weight_Loss_Porce = '{$WeigLossPorce}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/LAA-Small.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/LAA-Small.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/LAA-Small.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat LAA Coarse Filter -->
<?php
 if (isset($_POST["Repeat_LAA_Coarse_Filter"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM los_angeles_abrasion_coarse_filter WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/LAA-Small.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed LAA Coarse Filter -->
<?php
 if (isset($_POST["Reviewed_LAA_Coarse_Filter"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM los_angeles_abrasion_coarse_filter WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/LAA-Small.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- LAA Coarse Aggregate -->
<?php
 require_once('../config/load.php');
 $user = current_user();

 if (isset($_POST['LAA_Coarse_Aggregate'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "LAA_Coarse_Aggregate";
        $id = uuid();

        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);
        
        $sql = "INSERT INTO los_angeles_abrasion_coarse_aggregate (
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
            Methods,
            Grading,
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
            '$DateTesting',
            '$Comments',
            '$TestMethod',
            '$SelectGrading',
            '$InitWeig',
            '$FinalWeig',
            '$WeigLoss',
            '$WeigLossPorce'";
        
        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/LAA-Large.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/LAA-Large.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/LAA-Large.php', false);
    }
 }
?>

<!-- Update LAA Coarse Aggregate -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['Update_LAA_Coarse_Aggregate'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $Modified_Date = make_date();
        $Modified_By = $user['name'];
        $TestType = "LAA_Coarse_Aggregate";

        $SelectGrading = $db->escape($_POST['SelectGrading']);
        $InitWeig = $db->escape($_POST['InitWeig']);
        $FinalWeig = $db->escape($_POST['FinalWeig']);
        $WeigLoss = $db->escape($_POST['WeigLoss']);
        $WeigLossPorce = $db->escape($_POST['WeigLossPorce']);

        $query = "UPDATE los_angeles_abrasion_coarse_aggregate SET ";
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
        $query .= "Modified_Date = '{$Modified_Date}', ";
        $query .= "Modified_By = '{$Modified_By}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Grading = '{$SelectGrading}', ";
        $query .= "Initial_Weight = '{$InitWeig}', ";
        $query .= "Final_Weight = '{$FinalWeig}', ";
        $query .= "Weight_Loss = '{$WeigLoss}', ";
        $query .= "Weight_Loss_Porce = '{$WeigLossPorce}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/LAA-Large.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/LAA-Large.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/LAA-Large.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat LAA Coarse Aggregate -->
<?php
 if (isset($_POST["Repeat_LAA_Coarse_Aggregate"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM los_angeles_abrasion_coarse_aggregate WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/LAA-Large.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed LAA Coarse Aggregate -->
<?php
 if (isset($_POST["Reviewed_LAA_Coarse_Aggregate"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM los_angeles_abrasion_coarse_aggregate WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/LAA-Large.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>