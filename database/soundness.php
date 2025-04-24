<!-- Update Soundness -->
<?php
$Search = $_GET['id'];
if (isset($_POST['Update_SND'])) {
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SND";

        $WtDrySoil = $db->escape($_POST['WtDrySoil']);
        $WtWashed = $db->escape($_POST['WtWashed']);
        $WtRetPan = $db->escape($_POST['WtRetPan']);
        $PctRetPan = $db->escape($_POST['PctRetPan']);
        $WtRetTotalPan = $db->escape($_POST['WtRetTotalPan']);
        $PctRetTotalPan = $db->escape($_POST['PctRetTotalPan']);
        $WtRetTotal = $db->escape($_POST['WtRetTotal']);
        $PctRetTotal = $db->escape($_POST['PctRetTotal']);

        $WtRetCoarseTotal = $db->escape($_POST['WtRetCoarseTotal']);
        $PctRetCoarseTotal = $db->escape($_POST['PctRetCoarseTotal']);
        $WtRetFineTotal = $db->escape($_POST['WtRetFineTotal']);
        $PctRetFineTotal = $db->escape($_POST['PctRetFineTotal']);

        $TotalStarWeightRet = $db->escape($_POST['TotalStarWeightRet']);
        $TotalFinalWeightRet = $db->escape($_POST['TotalFinalWeightRet']);
        $TotalWeightedLoss = $db->escape($_POST['TotalWeightedLoss']);

        $TotalWeightRetCoarse = $db->escape($_POST['TotalWeightRetCoarse']);
        $TotalFinalWeightRetCoarse = $db->escape($_POST['TotalFinalWeightRetCoarse']);
        $TotalWeightedLossCoarse = $db->escape($_POST['TotalWeightedLossCoarse']);

        $inputValues = array();
        for ($i = 1; $i <= 16; $i++) {
            $inputValues["WtRet" . $i] = $db->escape($_POST["WtRet$i"]);
            $inputValues["PctRet" . $i] = $db->escape($_POST["PctRet$i"]);
        }

        for ($i = 1; $i <= 11; $i++) {
            $inputValues["WtRetCoarse" . $i] = $db->escape($_POST["WtRetCoarse$i"]);
            $inputValues["PctRetCoarse" . $i] = $db->escape($_POST["PctRetCoarse$i"]);
        }

        for ($i = 1; $i <= 7; $i++) {
            $inputValues["WtRetFine" . $i] = $db->escape($_POST["WtRetFine$i"]);
            $inputValues["PctRetFine" . $i] = $db->escape($_POST["PctRetFine$i"]);
        }

        for ($i = 1; $i <= 7; $i++) {
            $inputValues["StarWeightRet" . $i] = $db->escape($_POST["StarWeightRet$i"]);
            $inputValues["FinalWeightRet" . $i] = $db->escape($_POST["FinalWeightRet$i"]);
            $inputValues["PercentagePassing" . $i] = $db->escape($_POST["PercentagePassing$i"]);
            $inputValues["WeightedLoss" . $i] = $db->escape($_POST["WeightedLoss$i"]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $inputValues["StarWeightRetCoarse" . $i] = $db->escape($_POST["StarWeightRetCoarse$i"]);
        }

        for ($i = 1; $i <= 6; $i++) {
            $inputValues["FinalWeightRetCoarse" . $i] = $db->escape($_POST["FinalWeightRetCoarse$i"]);
            $inputValues["PercentagePassingCoarse" . $i] = $db->escape($_POST["PercentagePassingCoarse$i"]);
            $inputValues["WeightedLossCoarse" . $i] = $db->escape($_POST["WeightedLossCoarse$i"]);
        }

        for ($i = 1; $i <= 5; $i++) {
            $inputValues["SplittingNo" . $i] = $db->escape($_POST["SplittingNo$i"]);
            $inputValues["SplittingPct" . $i] = $db->escape($_POST["SplittingPct$i"]);
            $inputValues["CrumblingNo" . $i] = $db->escape($_POST["CrumblingNo$i"]);
            $inputValues["CrumblingPct" . $i] = $db->escape($_POST["CrumblingPct$i"]);
            $inputValues["CrackingNo" . $i] = $db->escape($_POST["CrackingNo$i"]);
            $inputValues["CrackingPct" . $i] = $db->escape($_POST["CrackingPct$i"]);
            $inputValues["FlakingNo" . $i] = $db->escape($_POST["FlakingNo$i"]);
            $inputValues["FlakingPct" . $i] = $db->escape($_POST["FlakingPct$i"]);
            $inputValues["TotalParticles" . $i] = $db->escape($_POST["TotalParticles$i"]);
        }

        for ($i = 1; $i <= 5; $i++) {
            $inputValues["StartDate" . $i] = $db->escape($_POST["StartDate$i"]);
            $inputValues["RoomTemp" . $i] = $db->escape($_POST["RoomTemp$i"]);
            $inputValues["SolutionTemp" . $i] = $db->escape($_POST["SolutionTemp$i"]);
            $inputValues["SpecificGravity" . $i] = $db->escape($_POST["SpecificGravity$i"]);
        }

        $query = "UPDATE soundness SET ";
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
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";

        $query .= "WtDrySoil = '{$WtDrySoil}', ";
        $query .= "WtWashed = '{$WtWashed}', ";
        $query .= "WtRetPan = '{$WtRetPan}', ";
        $query .= "PctRetPan = '{$PctRetPan}', ";
        $query .= "WtRetTotalPan = '{$WtRetTotalPan}', ";
        $query .= "PctRetTotalPan = '{$PctRetTotalPan}', ";
        $query .= "WtRetTotal = '{$WtRetTotal}', ";
        $query .= "PctRetTotal = '{$PctRetTotal}', ";
        $query .= "WtRetCoarseTotal = '{$WtRetCoarseTotal}', ";
        $query .= "PctRetCoarseTotal = '{$PctRetCoarseTotal}', ";
        $query .= "WtRetFineTotal = '{$WtRetFineTotal}', ";
        $query .= "PctRetFineTotal = '{$PctRetFineTotal}', ";
        $query .= "TotalStarWeightRet = '{$TotalStarWeightRet}', ";
        $query .= "TotalFinalWeightRet = '{$TotalFinalWeightRet}', ";
        $query .= "TotalWeightedLoss = '{$TotalWeightedLoss}', ";
        $query .= "TotalWeightRetCoarse = '{$TotalWeightRetCoarse}', ";
        $query .= "TotalFinalWeightRetCoarse = '{$TotalFinalWeightRetCoarse}', ";
        $query .= "TotalWeightedLossCoarse = '{$TotalWeightedLossCoarse}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/soundness.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/soundness.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/soundness.php?id=' . $Search, false);
    }
}
?>

<!-- Repeat Soundness -->
<?php
if (isset($_POST["Repeat_SND"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM soundness WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $Register_By = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $SendBy = $user["name"];


                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Send_By,
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
                    '$SendBy',
                    '$Register_By',
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
                redirect("../reviews/soundness.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Reviewed Soundness -->
<?php
if (isset($_POST["Reviewed_SND"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM soundness WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/soundness.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Delete SND -->
<?php
if (isset($_POST['delete_snd']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('soundness', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
?>