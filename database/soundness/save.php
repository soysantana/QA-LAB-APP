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
        $FieldComment = $db->escape($_POST['FieldComment']);
        // Information for the Laboratory sample
        $Standard = $db->escape($_POST['Standard']);
        $Technician = $db->escape($_POST['Technician']);
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SND";
        $id = uuid();

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

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM soundness
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
            Preparation_Methods,
            Split_Methods,
            Methods,
            Test_Start_Date,
            Comments,
            FieldComment,
            WtDrySoil,
            WtWashed,
            WtRetCoarse,
            PctRetCoarse,
            WtRetCoarseTotal,
            PctRetCoarseTotal,
            WtRetFine,
            PctRetFine,
            WtRetFineTotal,
            PctRetFineTotal,
            StarWeightRetFine,
            TotalStarWeightRetFine,
            FinalWeightRetFine,
            TotalFinalWeightRetFine,
            PercentagePassingFine,
            WeightedLossFine,
            TotalWeightedLossFine,
            StarWeightRetCoarse,
            TotalStarWeightRetCoarse,
            FinalWeightRetCoarse,
            TotalFinalWeightRetCoarse,
            PercentagePassingCoarse,
            WeightedLossCoarse,
            TotalWeightedLossCoarse,
            StartDate,
            RoomTemp,
            SolutionTemp,
            SpecificGravity,
            SplittingNo, 
            SplittingPct, 
            CrumblingNo,
            CrumblingPct,
            CrackingNo,
            CrackingPct,
            FlakingNo,
            FlakingPct,
            TotalParticles";

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
            '$PMethods',
            '$SMethods',
            '$TestMethod',
            '$DateTesting',
            '$Comments',
            '$FieldComment',
            '$WtDrySoil',
            '$WtWashed',
            '$WtRetCoarse',
            '$PctRetCoarse',
            '$WtRetCoarseTotal',
            '$PctRetCoarseTotal',
            '$WtRetFine',
            '$PctRetFine',
            '$WtRetFineTotal',
            '$PctRetFineTotal',
            '$StarWeightRetFine',
            '$TotalStarWeightRet',
            '$FinalWeightRetFine',
            '$TotalFinalWeightRet',
            '$PercentagePassingFine',
            '$WeightedLossFine',
            '$TotalWeightedLoss',
            '$StarWeightRetCoarse',
            '$TotalStarWeightRetCoarse',
            '$FinalWeightRetCoarse',
            '$TotalFinalWeightRetCoarse',
            '$PercentagePassingCoarse',
            '$WeightedLossCoarse',
            '$TotalWeightedLossCoarse',
            '$StartDate',
            '$RoomTemp',
            '$SolutionTemp',
            '$SpecificGravity',
            '$SplittingNo', 
            '$SplittingPct', 
            '$CrumblingNo',
            '$CrumblingPct',
            '$CrackingNo',
            '$CrackingPct',
            '$FlakingNo',
            '$FlakingPct',
            '$TotalParticles'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado exitosamente.");
            redirect('../pages/soundness.php', false);
        } else {
            $session->msg('d', 'Lo siento, el ensayo no pudo ser agregado.');
            redirect('../pages/soundness.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/soundness.php', false);
    }
}
