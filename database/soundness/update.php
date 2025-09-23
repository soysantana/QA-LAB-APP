<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update-snd'])) {
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        // Information for the Laboratory sample
        $Standard = $db->escape($_POST['Standard']);
        $Technician = $db->escape($_POST['Technician']);
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SND";

        $WtDrySoil = $db->escape($_POST['WtDrySoil']);
        $WtWashed = $db->escape($_POST['WtWashed']);

        $WtRetCoarseTotal = $db->escape($_POST['WtRetCoarseTotal']);
        $PctRetCoarseTotal = $db->escape($_POST['PctRetCoarseTotal']);
        $WtRetFineTotal = $db->escape($_POST['WtRetFineTotal']);
        $PctRetFineTotal = $db->escape($_POST['PctRetFineTotal']);

        $TotalStarWeightRet = $db->escape($_POST['TotalStarWeightRet']);
        $TotalFinalWeightRet = $db->escape($_POST['TotalFinalWeightRet']);
        $TotalWeightedLoss = $db->escape($_POST['TotalWeightedLoss']);

        $TotalStarWeightRetCoarse = $db->escape($_POST['TotalStarWeightRetCoarse']);
        $TotalFinalWeightRetCoarse = $db->escape($_POST['TotalFinalWeightRetCoarse']);
        $TotalWeightedLossCoarse = $db->escape($_POST['TotalWeightedLossCoarse']);

        function buildCSV($name, $total, $db)
        {
            $data = [];
            for ($i = 1; $i <= $total; $i++) {
                $val = trim($_POST[$name . $i]);
                $data[] = ($val === '') ? 'null' : $db->escape($val);
            }
            return implode(',', $data);
        }

        $WtRetCoarse = buildCSV('WtRetCoarse', 11, $db);
        $PctRetCoarse = buildCSV('PctRetCoarse', 11, $db);
        $WtRetFine = buildCSV('WtRetFine', 7, $db);
        $PctRetFine = buildCSV('PctRetFine', 7, $db);

        $StartDate = buildCSV('StartDate', 5, $db);
        $RoomTemp = buildCSV('RoomTemp', 5, $db);
        $SolutionTemp = buildCSV('SolutionTemp', 5, $db);
        $SpecificGravity = buildCSV('SpecificGravity', 5, $db);

        $StarWeightRetFine = buildCSV('StarWeightRet', 7, $db);
        $FinalWeightRetFine = buildCSV('FinalWeightRet', 7, $db);
        $PercentagePassingFine = buildCSV('PercentagePassing', 7, $db);
        $WeightedLossFine = buildCSV('WeightedLoss', 7, $db);

        $StarWeightRetCoarse = buildCSV('StarWeightRetCoarse', 10, $db);
        $FinalWeightRetCoarse = buildCSV('FinalWeightRetCoarse', 7, $db);
        $PercentagePassingCoarse = buildCSV('PercentagePassingCoarse', 7, $db);
        $WeightedLossCoarse = buildCSV('WeightedLossCoarse', 7, $db);

        $SplittingNo = buildCSV('SplittingNo', 5, $db);
        $SplittingPct = buildCSV('SplittingPct', 5, $db);
        $CrumblingNo = buildCSV('CrumblingNo', 5, $db);
        $CrumblingPct = buildCSV('CrumblingPct', 5, $db);
        $CrackingNo = buildCSV('CrackingNo', 5, $db);
        $CrackingPct = buildCSV('CrackingPct', 5, $db);
        $FlakingNo = buildCSV('FlakingNo', 5, $db);
        $FlakingPct = buildCSV('FlakingPct', 5, $db);
        $TotalParticles = buildCSV('TotalParticles', 5, $db);

        $query = "UPDATE soundness SET ";
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
        $query .= "Split_Methods = '{$SMethods}', ";
        $query .= "Preparation_Methods = '{$PMethods}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "WtDrySoil = '{$WtDrySoil}', ";
        $query .= "WtWashed = '{$WtWashed}', ";
        $query .= "WtRetCoarseTotal = '{$WtRetCoarseTotal}', ";
        $query .= "PctRetCoarseTotal = '{$PctRetCoarseTotal}', ";
        $query .= "WtRetFineTotal = '{$WtRetFineTotal}', ";
        $query .= "PctRetFineTotal = '{$PctRetFineTotal}', ";
        $query .= "TotalStarWeightRetFine = '{$TotalStarWeightRet}', ";
        $query .= "TotalFinalWeightRetFine = '{$TotalFinalWeightRet}', ";
        $query .= "TotalWeightedLossFine = '{$TotalWeightedLoss}', ";
        $query .= "TotalStarWeightRetCoarse = '{$TotalStarWeightRetCoarse}', ";
        $query .= "TotalFinalWeightRetCoarse = '{$TotalFinalWeightRetCoarse}', ";
        $query .= "TotalWeightedLossCoarse = '{$TotalWeightedLossCoarse}', ";
        $query .= "WtRetCoarse = '{$WtRetCoarse}', ";
        $query .= "PctRetCoarse = '{$PctRetCoarse}', ";
        $query .= "WtRetFine = '{$WtRetFine}', ";
        $query .= "PctRetFine = '{$PctRetFine}', ";
        $query .= "StartDate = '{$StartDate}', ";
        $query .= "RoomTemp = '{$RoomTemp}', ";
        $query .= "SolutionTemp = '{$SolutionTemp}', ";
        $query .= "SpecificGravity = '{$SpecificGravity}', ";
        $query .= "StarWeightRetFine = '{$StarWeightRetFine}', ";
        $query .= "FinalWeightRetFine = '{$FinalWeightRetFine}', ";
        $query .= "PercentagePassingFine = '{$PercentagePassingFine}', ";
        $query .= "WeightedLossFine = '{$WeightedLossFine}', ";
        $query .= "StarWeightRetCoarse = '{$StarWeightRetCoarse}', ";
        $query .= "FinalWeightRetCoarse = '{$FinalWeightRetCoarse}', ";
        $query .= "PercentagePassingCoarse = '{$PercentagePassingCoarse}', ";
        $query .= "WeightedLossCoarse = '{$WeightedLossCoarse}', ";
        $query .= "SplittingNo = '{$SplittingNo}', ";
        $query .= "SplittingPct = '{$SplittingPct}', ";
        $query .= "CrumblingNo = '{$CrumblingNo}', ";
        $query .= "CrumblingPct = '{$CrumblingPct}', ";
        $query .= "CrackingNo = '{$CrackingNo}', ";
        $query .= "CrackingPct = '{$CrackingPct}', ";
        $query .= "FlakingNo = '{$FlakingNo}', ";
        $query .= "FlakingPct = '{$FlakingPct}', ";
        $query .= "TotalParticles = '{$TotalParticles}' ";
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
