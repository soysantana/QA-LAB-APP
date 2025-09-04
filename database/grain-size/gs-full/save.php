<?php
$user = current_user();

if (isset($_POST['GSFull'])) {
    $req_fields = array(
        'SampleName',
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
        $FieldComment = $db->escape($_POST['FieldComment']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $Material = $db->escape($_POST['materialSelect']);
        $TestType = "GS-" . $Material;
        $id = uuid();

        $TDMPHumedo = $db->escape($_POST['TDMPHumedo']);
        $TDMRSecoSucio = $db->escape($_POST['TDMRSecoSucio']);
        $More3p = $db->escape($_POST['More3Ex']);
        $Lees3P = $db->escape($_POST['Less3Ex']);
        $TotalPesoSecoSucio = $db->escape($_POST['TotalPesoSecoSucio']);
        $TotalPesoLavado = $db->escape($_POST['TotalPesoLavado']);
        $PerdidaPorLavado = $db->escape($_POST['PerdidaPorLavado']);
        $PesoSecoSucio = $db->escape($_POST['PesoSecoSucio']);
        $PesoLavado = $db->escape($_POST['PesoLavado']);
        $PanLavado = $db->escape($_POST['PanLavado']);

        $PanWtRet = $db->escape($_POST['PanWtRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $MoistureContentAvg = $db->escape($_POST['MoistureContentAvg']);
        $TotalDryWtSampleLess3g = $db->escape($_POST['TotalDryWtSampleLess3g']);
        $ConvertionFactor = $db->escape($_POST['ConvertionFactor']);

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
        $ClassificationUSCS1 = $db->escape($_POST['classification']);

        $combinedScreen40 = "";
        $combinedScreen30 = "";
        $combinedScreen20 = "";
        $combinedScreen13 = "";
        $combinedScreen12 = "";
        $combinedScreen10 = "";
        $combinedScreen8 = "";
        $combinedScreen6 = "";
        $combinedScreen4 = "";
        $combinedScreen3 = "";
        $combinedScreen2 = "";
        $combinedScreen1p5 = "";
        $combinedScreen1 = "";
        $combinedScreen3p4 = "";
        $combinedScreen1p2 = "";
        $combinedScreen3p8 = "";
        $combinedScreenNo4 = "";
        $combinedScreenNo20 = "";
        $combinedScreenNo200 = "";
        $combinedScreenPan = "";
        $combinedsTotal = "";
        $combinedWtPhumedo = "";
        $combinedWtReSecoSucio = "";
        $combinedContainer = "";
        $combinedWetSoil = "";
        $combinedWetDry = "";
        $combinedWetWater = "";
        $combinedTareMC = "";
        $combinedWtDrySoil = "";
        $combinedMoisturePercet = "";
        $combinedWtRet = "";
        $combinedRet = "";
        $combinedCumRet = "";
        $combinedPass = "";
        $combinedSpecs = "";


        for ($i = 1; $i <= 10; $i++) {
            ${"screen40_" . $i} = $db->escape($_POST["screen40_$i"]);
            ${"screen30_" . $i} = $db->escape($_POST["screen30_$i"]);
            ${"screen20_" . $i} = $db->escape($_POST["screen20_$i"]);
            ${"screen13_" . $i} = $db->escape($_POST["screen13_$i"]);
            ${"screen12_" . $i} = $db->escape($_POST["screen12_$i"]);
            ${"screen10_" . $i} = $db->escape($_POST["screen10_$i"]);
            ${"screen8_" . $i} = $db->escape($_POST["screen8_$i"]);
            ${"screen6_" . $i} = $db->escape($_POST["screen6_$i"]);
            ${"screen4_" . $i} = $db->escape($_POST["screen4_$i"]);
            ${"screen3_" . $i} = $db->escape($_POST["screen3_$i"]);
            ${"screen2_" . $i} = $db->escape($_POST["screen2_$i"]);
            ${"screen1p5_" . $i} = $db->escape($_POST["screen1p5_$i"]);
            ${"screen1_" . $i} = $db->escape($_POST["screen1_$i"]);
            ${"screen3p4_" . $i} = $db->escape($_POST["screen3p4_$i"]);
            ${"screen1p2_" . $i} = $db->escape($_POST["screen1p2_$i"]);
            ${"screen3p8_" . $i} = $db->escape($_POST["screen3p8_$i"]);
            ${"screenNo4_" . $i} = $db->escape($_POST["screenNo4_$i"]);
            ${"screenNo20_" . $i} = $db->escape($_POST["screenNo20_$i"]);
            ${"screenNo200_" . $i} = $db->escape($_POST["screenNo200_$i"]);
            ${"screenPan_" . $i} = $db->escape($_POST["screenPan_$i"]);
            // Concatenar si el campo tiene valor
            if (!empty(${"screen40_" . $i})) {
                $combinedScreen40 .= ($combinedScreen40 ? ", " : "") . ${"screen40_" . $i};
            }
            if (!empty(${"screen30_" . $i})) {
                $combinedScreen30 .= ($combinedScreen30 ? ", " : "") . ${"screen30_" . $i};
            }
            if (!empty(${"screen20_" . $i})) {
                $combinedScreen20 .= ($combinedScreen20 ? ", " : "") . ${"screen20_" . $i};
            }
            if (!empty(${"screen13_" . $i})) {
                $combinedScreen13 .= ($combinedScreen13 ? ", " : "") . ${"screen13_" . $i};
            }
            if (!empty(${"screen12_" . $i})) {
                $combinedScreen12 .= ($combinedScreen12 ? ", " : "") . ${"screen12_" . $i};
            }
            if (!empty(${"screen10_" . $i})) {
                $combinedScreen10 .= ($combinedScreen10 ? ", " : "") . ${"screen10_" . $i};
            }
            if (!empty(${"screen8_" . $i})) {
                $combinedScreen8 .= ($combinedScreen8 ? ", " : "") . ${"screen8_" . $i};
            }
            if (!empty(${"screen6_" . $i})) {
                $combinedScreen6 .= ($combinedScreen6 ? ", " : "") . ${"screen6_" . $i};
            }
            if (!empty(${"screen4_" . $i})) {
                $combinedScreen4 .= ($combinedScreen4 ? ", " : "") . ${"screen4_" . $i};
            }
            if (!empty(${"screen3_" . $i})) {
                $combinedScreen3 .= ($combinedScreen3 ? ", " : "") . ${"screen3_" . $i};
            }
            if (!empty(${"screen2_" . $i})) {
                $combinedScreen2 .= ($combinedScreen2 ? ", " : "") . ${"screen2_" . $i};
            }
            if (!empty(${"screen1p5_" . $i})) {
                $combinedScreen1p5 .= ($combinedScreen1p5 ? ", " : "") . ${"screen1p5_" . $i};
            }
            if (!empty(${"screen1_" . $i})) {
                $combinedScreen1 .= ($combinedScreen1 ? ", " : "") . ${"screen1_" . $i};
            }
            if (!empty(${"screen3p4_" . $i})) {
                $combinedScreen3p4 .= ($combinedScreen3p4 ? ", " : "") . ${"screen3p4_" . $i};
            }
            if (!empty(${"screen1p2_" . $i})) {
                $combinedScreen1p2 .= ($combinedScreen1p2 ? ", " : "") . ${"screen1p2_" . $i};
            }
            if (!empty(${"screen3p8_" . $i})) {
                $combinedScreen3p8 .= ($combinedScreen3p8 ? ", " : "") . ${"screen3p8_" . $i};
            }
            if (!empty(${"screenNo4_" . $i})) {
                $combinedScreenNo4 .= ($combinedScreenNo4 ? ", " : "") . ${"screenNo4_" . $i};
            }
            if (!empty(${"screenNo20_" . $i})) {
                $combinedScreenNo20 .= ($combinedScreenNo20 ? ", " : "") . ${"screenNo20_" . $i};
            }
            if (!empty(${"screenNo200_" . $i})) {
                $combinedScreenNo200 .= ($combinedScreenNo200 ? ", " : "") . ${"screenNo200_" . $i};
            }
            if (!empty(${"screenPan_" . $i})) {
                $combinedScreenPan .= ($combinedScreenPan ? ", " : "") . ${"screenPan_" . $i};
            }
        }

        for ($i = 1; $i <= 20; $i++) {
            ${"sTotal_" . $i} = $db->escape($_POST["sTotal_$i"]);
            if (!empty(${"sTotal_" . $i})) {
                $combinedsTotal .= ($combinedsTotal ? ", " : "") . ${"sTotal_" . $i};
            }
        }

        for ($i = 1; $i <= 55; $i++) {
            ${"WtPhumedo_" . $i} = $db->escape($_POST["WtPhumedo_$i"]);
            if (!empty(${"WtPhumedo_" . $i})) {
                $combinedWtPhumedo .= ($combinedWtPhumedo ? ", " : "") . ${"WtPhumedo_" . $i};
            }
        }

        for ($i = 1; $i <= 8; $i++) {
            ${"WtReSecoSucio_" . $i} = $db->escape($_POST["WtReSecoSucio_$i"]);
            if (!empty(${"WtReSecoSucio_" . $i})) {
                $combinedWtReSecoSucio .= ($combinedWtReSecoSucio ? ", " : "") . ${"WtReSecoSucio_" . $i};
            }
        }

        for ($i = 1; $i <= 4; $i++) {
            ${"Container" . $i} = $db->escape($_POST["Container$i"]);
            ${"WetSoil" . $i} = $db->escape($_POST["WetSoil$i"]);
            ${"WetDry" . $i} = $db->escape($_POST["WetDry$i"]);
            ${"WetWater" . $i} = $db->escape($_POST["WetWater$i"]);
            ${"TareMC" . $i} = $db->escape($_POST["TareMC$i"]);
            ${"WtDrySoil" . $i} = $db->escape($_POST["WtDrySoil$i"]);
            ${"MoisturePercet" . $i} = $db->escape($_POST["MoisturePercet$i"]);
            if (!empty(${"Container" . $i})) {
                $combinedContainer .= ($combinedContainer ? ", " : "") . ${"Container" . $i};
            }
            if (!empty(${"WetSoil" . $i})) {
                $combinedWetSoil .= ($combinedWetSoil ? ", " : "") . ${"WetSoil" . $i};
            }
            if (!empty(${"WetDry" . $i})) {
                $combinedWetDry .= ($combinedWetDry ? ", " : "") . ${"WetDry" . $i};
            }
            if (!empty(${"WetWater" . $i})) {
                $combinedWetWater .= ($combinedWetWater ? ", " : "") . ${"WetWater" . $i};
            }
            if (!empty(${"TareMC" . $i})) {
                $combinedTareMC .= ($combinedTareMC ? ", " : "") . ${"TareMC" . $i};
            }
            if (!empty(${"WtDrySoil" . $i})) {
                $combinedWtDrySoil .= ($combinedWtDrySoil ? ", " : "") . ${"WtDrySoil" . $i};
            }
            if (!empty(${"MoisturePercet" . $i})) {
                $combinedMoisturePercet .= ($combinedMoisturePercet ? ", " : "") . ${"MoisturePercet" . $i};
            }
        }

        for ($i = 1; $i <= 8; $i++) {
            ${"Specs" . $i} = $db->escape($_POST["Specs$i"]);
            if (!empty(${"Specs" . $i})) {
                $combinedSpecs .= ($combinedSpecs ? ", " : "") . ${"Specs" . $i};
            }
        }

        for ($i = 1; $i <= 19; $i++) {
            ${"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            ${"Ret" . $i} = $db->escape($_POST["Ret$i"]);
            ${"CumRet" . $i} = $db->escape($_POST["CumRet$i"]);
            ${"Pass" . $i} = $db->escape($_POST["Pass$i"]);

            if (!empty(${"WtRet" . $i})) {
                $combinedWtRet .= ($combinedWtRet ? ", " : "") . ${"WtRet" . $i};
            }
            if (!empty(${"Ret" . $i})) {
                $combinedRet .= ($combinedRet ? ", " : "") . ${"Ret" . $i};
            }
            if (!empty(${"CumRet" . $i})) {
                $combinedCumRet .= ($combinedCumRet ? ", " : "") . ${"CumRet" . $i};
            }
            if (!empty(${"Pass" . $i})) {
                $combinedPass .= ($combinedPass ? ", " : "") . ${"Pass" . $i};
            }
        }

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM grain_size_full
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

        $sql = "INSERT INTO grain_size_full (
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
            FieldComment,
            Technician,
            Test_Start_Date,
            Comments,
            Preparation_Method,
            Split_Method,
            Screen40,
            Screen30,
            Screen20,
            Screen13,
            Screen12,
            Screen10,
            Screen8,
            Screen6,
            Screen4,
            Screen3,
            Screen2,
            Screen1p5,
            Screen1,
            Screen3p4,
            Screen1p2,
            Screen3p8,
            ScreenNo4,
            ScreenNo20,
            ScreenNo200,
            ScreenPan,
            ScreenTotal,
            WtPassHumedoLess3,
            TotalPassHumedoLess3,
            WtPassSecoSucioLess3,
            TotalPassSecoSucioLess3,
            More3p,
            Lees3P,
            TotalPesoSecoSucio,
            TotalPesoLavado,
            PerdidaPorLavado,
            PesoSecoSucio,
            PesoLavado,
            PanLavado,
            Container,
            WtSoilTare,
            WtSoilDry,
            WtWater,
            TareMC,
            WtDrySoil,
            MC,
            PanWtRen,
            TotalWtRet,
            TotalRet,
            TotalCumRet,
            TotalPass,
            WtRet,
            Ret,
            CumRet,
            Pass,
            Specs,
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
            MoistureContentAvg,
            TotalDryWtSampleLess3g,
            ConvertionFactor,
            ClassificationUSCS1";

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
            '$FieldComment',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$PMethods',
            '$SMethods',
            '$combinedScreen40',
            '$combinedScreen30',
            '$combinedScreen20',
            '$combinedScreen13',
            '$combinedScreen12',
            '$combinedScreen10',
            '$combinedScreen8',
            '$combinedScreen6',
            '$combinedScreen4',
            '$combinedScreen3',
            '$combinedScreen2',
            '$combinedScreen1p5',
            '$combinedScreen1',
            '$combinedScreen3p4',
            '$combinedScreen1p2',
            '$combinedScreen3p8',
            '$combinedScreenNo4',
            '$combinedScreenNo20',
            '$combinedScreenNo200',
            '$combinedScreenPan',
            '$combinedsTotal',
            '$combinedWtPhumedo',
            '$TDMPHumedo',
            '$combinedWtReSecoSucio',
            '$TDMRSecoSucio',
            '$More3p',
            '$Lees3P',
            '$TotalPesoSecoSucio',
            '$TotalPesoLavado',
            '$PerdidaPorLavado',
            '$PesoSecoSucio',
            '$PesoLavado',
            '$PanLavado',
            '$combinedContainer',
            '$combinedWetSoil',
            '$combinedWetDry',
            '$combinedWetWater',
            '$combinedTareMC',
            '$combinedWtDrySoil',
            '$combinedMoisturePercet',
            '$PanWtRet',
            '$TotalWtRet',
            '$TotalRet',
            '$TotalCumRet',
            '$TotalPass',
            '$combinedWtRet',
            '$combinedRet',
            '$combinedCumRet',
            '$combinedPass',
            '$combinedSpecs',
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
            '$MoistureContentAvg',
            '$TotalDryWtSampleLess3g',
            '$ConvertionFactor',
            '$ClassificationUSCS1'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado con éxito.");
            redirect('/pages/grain-size-full.php', false);
        } else {
            $session->msg('w', 'Lo sentimos, el ensayo no se pudo agregar.');
            redirect('/pages/grain-size-full.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/grain-size-full.php', false);
    }
}
