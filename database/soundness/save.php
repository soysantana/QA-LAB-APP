<?php
$user = current_user();

if (isset($_POST['Soundness'])) {
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
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SND";
        $id = uuid();

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

        for ($i = 1; $i <= 16; $i++) {
            ${"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            ${"PctRet" . $i} = $db->escape($_POST["PctRet$i"]);
        }

        for ($i = 1; $i <= 11; $i++) {
            ${"WtRetCoarse" . $i} = $db->escape($_POST["WtRetCoarse$i"]);
            ${"PctRetCoarse" . $i} = $db->escape($_POST["PctRetCoarse$i"]);
        }

        for ($i = 1; $i <= 7; $i++) {
            ${"WtRetFine" . $i} = $db->escape($_POST["WtRetFine$i"]);
            ${"PctRetFine" . $i} = $db->escape($_POST["PctRetFine$i"]);
        }

        for ($i = 1; $i <= 7; $i++) {
            ${"StarWeightRet" . $i} = $db->escape($_POST["StarWeightRet$i"]);
            ${"FinalWeightRet" . $i} = $db->escape($_POST["FinalWeightRet$i"]);
            ${"PercentagePassing" . $i} = $db->escape($_POST["PercentagePassing$i"]);
            ${"WeightedLoss" . $i} = $db->escape($_POST["WeightedLoss$i"]);
        }

        for ($i = 1; $i <= 10; $i++) {
            ${"StarWeightRetCoarse" . $i} = $db->escape($_POST["StarWeightRetCoarse$i"]);
        }

        for ($i = 1; $i <= 6; $i++) {
            ${"FinalWeightRetCoarse" . $i} = $db->escape($_POST["FinalWeightRetCoarse$i"]);
            ${"PercentagePassingCoarse" . $i} = $db->escape($_POST["PercentagePassingCoarse$i"]);
            ${"WeightedLossCoarse" . $i} = $db->escape($_POST["WeightedLossCoarse$i"]);
        }

        for ($i = 1; $i <= 5; $i++) {
            ${"SplittingNo" . $i} = $db->escape($_POST["SplittingNo$i"]);
            ${"SplittingPct" . $i} = $db->escape($_POST["SplittingPct$i"]);
            ${"CrumblingNo" . $i} = $db->escape($_POST["CrumblingNo$i"]);
            ${"CrumblingPct" . $i} = $db->escape($_POST["CrumblingPct$i"]);
            ${"CrackingNo" . $i} = $db->escape($_POST["CrackingNo$i"]);
            ${"CrackingPct" . $i} = $db->escape($_POST["CrackingPct$i"]);
            ${"FlakingNo" . $i} = $db->escape($_POST["FlakingNo$i"]);
            ${"FlakingPct" . $i} = $db->escape($_POST["FlakingPct$i"]);
            ${"TotalParticles" . $i} = $db->escape($_POST["TotalParticles$i"]);
        }

        for ($i = 1; $i <= 5; $i++) {
            ${"StartDate" . $i} = $db->escape($_POST["StartDate$i"]);
            ${"RoomTemp" . $i} = $db->escape($_POST["RoomTemp$i"]);
            ${"SolutionTemp" . $i} = $db->escape($_POST["SolutionTemp$i"]);
            ${"SpecificGravity" . $i} = $db->escape($_POST["SpecificGravity$i"]);
        }

        $sql = "INSERT INTO soundness (
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
            WtDrySoil,
            WtWashed,
            WtRetPan,
            PctRetPan,
            WtRetTotalPan,
            PctRetTotalPan,
            WtRetTotal,
            PctRetTotal,
            WtRetCoarseTotal,
            PctRetCoarseTotal,
            WtRetFineTotal,
            PctRetFineTotal,
            TotalStarWeightRet,
            TotalFinalWeightRet,
            TotalWeightedLoss,
            TotalWeightRetCoarse,
            TotalFinalWeightRetCoarse,
            TotalWeightedLossCoarse";

        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 16; $i++) {
            $sql .= ", WtRet$i, PctRet$i";
        }

        for ($i = 1; $i <= 11; $i++) {
            $sql .= ", WtRetCoarse$i, PctRetCoarse$i";
        }

        for ($i = 1; $i <= 7; $i++) {
            $sql .= ", WtRetFine$i, PctRetFine$i";
        }

        for ($i = 1; $i <= 7; $i++) {
            $sql .= ", StarWeightRet$i, FinalWeightRet$i, PercentagePassing$i, WeightedLoss$i";
        }

        for ($i = 1; $i <= 10; $i++) {
            $sql .= ", StarWeightRetCoarse$i";
        }

        for ($i = 1; $i <= 6; $i++) {
            $sql .= ", FinalWeightRetCoarse$i, PercentagePassingCoarse$i, WeightedLossCoarse$i";
        }

        for ($i = 1; $i <= 5; $i++) {
            $sql .= ", SplittingNo$i, SplittingPct$i, CrumblingNo$i, CrumblingPct$i, CrackingNo$i, CrackingPct$i, FlakingNo$i, FlakingPct$i, TotalParticles$i";
        }

        for ($i = 1; $i <= 5; $i++) {
            $sql .= ", StartDate$i, RoomTemp$i, SolutionTemp$i, SpecificGravity$i";
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
            '$WtDrySoil',
            '$WtWashed',
            '$WtRetPan',
            '$PctRetPan',
            '$WtRetTotalPan',
            '$PctRetTotalPan',
            '$WtRetTotal',
            '$PctRetTotal',
            '$WtRetCoarseTotal',
            '$PctRetCoarseTotal',
            '$WtRetFineTotal',
            '$PctRetFineTotal',
            '$TotalStarWeightRet',
            '$TotalFinalWeightRet',
            '$TotalWeightedLoss',
            '$TotalWeightRetCoarse',
            '$TotalFinalWeightRetCoarse',
            '$TotalWeightedLossCoarse'";

        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 16; $i++) {
            $sql .= ", '${"WtRet$i"}', '${"PctRet$i"}'";
        }

        for ($i = 1; $i <= 11; $i++) {
            $sql .= ", '${"WtRetCoarse$i"}', '${"PctRetCoarse$i"}'";
        }

        for ($i = 1; $i <= 7; $i++) {
            $sql .= ", '${"WtRetFine$i"}', '${"PctRetFine$i"}'";
        }

        for ($i = 1; $i <= 7; $i++) {
            $sql .= ", '${"StarWeightRet$i"}', '${"FinalWeightRet$i"}', '${"PercentagePassing$i"}', '${"WeightedLoss$i"}'";
        }

        for ($i = 1; $i <= 10; $i++) {
            $sql .= ", '${"StarWeightRetCoarse$i"}'";
        }

        for ($i = 1; $i <= 6; $i++) {
            $sql .= ", '${"FinalWeightRetCoarse$i"}', '${"PercentagePassingCoarse$i"}', '${"WeightedLossCoarse$i"}'";
        }

        for ($i = 1; $i <= 5; $i++) {
            $sql .= ", '${"SplittingNo$i"}', '${"SplittingPct$i"}', '${"CrumblingNo$i"}', '${"CrumblingPct$i"}', '${"CrackingNo$i"}', '${"CrackingPct$i"}', '${"FlakingNo$i"}', '${"FlakingPct$i"}', '${"TotalParticles$i"}'";
        }

        for ($i = 1; $i <= 5; $i++) {
            $sql .= ", '${"StartDate$i"}', '${"RoomTemp$i"}', '${"SolutionTemp$i"}', '${"SpecificGravity$i"}'";
        }

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/soundness.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/soundness.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/soundness.php', false);
    }
}
