<?php
$user = current_user();

if (isset($_POST['standard-proctor'])) {
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
        $TestType = "SP";
        $id = uuid();

        $NatMc = $db->escape($_POST['NatMc']);
        $SpecGravity = $db->escape($_POST['SpecGravity']);
        $MaxDryDensity = $db->escape($_POST['MaxDryDensity']);
        $OptimumMoisture = $db->escape($_POST['OptimumMoisture']);
        $CorrectedDryUnitWeigt = $db->escape($_POST['CorrectedDryUnitWeigt']);
        $CorrectedWaterContentFiner = $db->escape($_POST['CorrectedWaterContentFiner']);
        $WcPorce = $db->escape($_POST['WcPorce']);
        $Ydf = $db->escape($_POST['Ydf']);
        $PcPorce = $db->escape($_POST['PcPorce']);
        $PfPorce = $db->escape($_POST['PfPorce']);
        $Gm = $db->escape($_POST['Gm']);
        $Ydt = $db->escape($_POST['Ydt']);
        $YwKnm = $db->escape($_POST['YwKnm']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        for ($i = 1; $i <= 6; $i++) {
            ${"WetSoilMod" . $i} = $db->escape($_POST["WetSoilMod$i"]);
            ${"WtMold" . $i} = $db->escape($_POST["WtMold$i"]);
            ${"WtSoil" . $i} = $db->escape($_POST["WtSoil$i"]);
            ${"VolMold" . $i} = $db->escape($_POST["VolMold$i"]);
            ${"WetDensity" . $i} = $db->escape($_POST["WetDensity$i"]);
            ${"DryDensity" . $i} = $db->escape($_POST["DryDensity$i"]);
            ${"DensyCorrected" . $i} = $db->escape($_POST["DensyCorrected$i"]);
            ${"Container" . $i} = $db->escape($_POST["Container$i"]);
            ${"WetSoilTare" . $i} = $db->escape($_POST["WetSoilTare$i"]);
            ${"WetDryTare" . $i} = $db->escape($_POST["WetDryTare$i"]);
            ${"WtWater" . $i} = $db->escape($_POST["WtWater$i"]);
            ${"Tare" . $i} = $db->escape($_POST["Tare$i"]);
            ${"DrySoil" . $i} = $db->escape($_POST["DrySoil$i"]);
            ${"MoisturePorce" . $i} = $db->escape($_POST["MoisturePorce$i"]);
            ${"MCcorrected" . $i} = $db->escape($_POST["MCcorrected$i"]);
        }

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM standard_proctor
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

        $sql = "INSERT INTO standard_proctor (
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
            Nat_Mc,
            Spec_Gravity,
            Max_Dry_Density_kgm3,
            Optimun_MC_Porce,
            Wc_Porce,
            GM_Porce,
            PC_Porce,
            PF_Porce,
            YDF_Porce,
            YDT_Porce,
            Yw_KnM3,
            Corrected_Dry_Unit_Weigt,
            Corrected_Water_Content_Finer,
            Graph";

        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 6; $i++) {
            $sql .= ", WetSoilMod$i, WtMold$i, WtSoil$i, VolMold$i,
            WetDensity$i, DryDensity$i, DensyCorrected$i, Container$i,
            WetSoilTare$i, WetDryTare$i, WtWater$i, Tare$i, DrySoil$i, MoisturePorce$i,
            MCcorrected$i";
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
            '$NatMc',
            '$SpecGravity',
            '$MaxDryDensity',
            '$OptimumMoisture',
            '$WcPorce',
            '$Gm',
            '$PcPorce',
            '$PfPorce',
            '$Ydf',
            '$Ydt',
            '$YwKnm',
            '$CorrectedDryUnitWeigt',
            '$CorrectedWaterContentFiner',
            '$Graph64'";

        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 6; $i++) {
            $sql .= ", '${"WetSoilMod$i"}', '${"WtMold$i"}', '${"WtSoil$i"}', '${"VolMold$i"}',
            '${"WetDensity$i"}', '${"DryDensity$i"}', '${"DensyCorrected$i"}', '${"Container$i"}', '${"WetSoilTare$i"}',
            '${"WetDryTare$i"}', '${"WtWater$i"}', '${"Tare$i"}', '${"DrySoil$i"}', '${"MoisturePorce$i"}', '${"MCcorrected$i"}'";
        }

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('/pages/standard-proctor.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('/pages/standard-proctor.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/standard-proctor.php', false);
    }
}
