<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['UpdateGSFull'])) {
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $Material = $db->escape($_POST['materialSelect']);
        $TestType = "GS-" . $Material;

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

        $query = "UPDATE grain_size_full SET ";
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
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Preparation_Method = '{$PMethods}', ";
        $query .= "Split_Method = '{$SMethods}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "TotalPassHumedoLess3 = '{$TDMPHumedo}', ";
        $query .= "TotalPassSecoSucioLess3 = '{$TDMRSecoSucio}', ";
        $query .= "More3p = '{$More3p}', ";
        $query .= "Lees3P = '{$Lees3P}', ";
        $query .= "TotalPesoSecoSucio = '{$TotalPesoSecoSucio}', ";
        $query .= "TotalPesoLavado = '{$TotalPesoLavado}', ";
        $query .= "PerdidaPorLavado = '{$PerdidaPorLavado}', ";
        $query .= "PesoSecoSucio = '{$PesoSecoSucio}', ";
        $query .= "PesoLavado = '{$PesoLavado}', ";
        $query .= "PanLavado = '{$PanLavado}', ";
        $query .= "PanWtRen = '{$PanWtRet}', ";
        $query .= "TotalWtRet = '{$TotalWtRet}', ";
        $query .= "TotalRet = '{$TotalRet}', ";
        $query .= "TotalCumRet = '{$TotalCumRet}', ";
        $query .= "TotalPass = '{$TotalPass}', ";
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
        $query .= "Screen40 = '{$combinedScreen40}', ";
        $query .= "Screen30 = '{$combinedScreen30}', ";
        $query .= "Screen20 = '{$combinedScreen20}', ";
        $query .= "Screen13 = '{$combinedScreen13}', ";
        $query .= "Screen12 = '{$combinedScreen12}', ";
        $query .= "Screen10 = '{$combinedScreen10}', ";
        $query .= "Screen8 = '{$combinedScreen8}', ";
        $query .= "Screen6 = '{$combinedScreen6}', ";
        $query .= "Screen4 = '{$combinedScreen4}', ";
        $query .= "Screen3 = '{$combinedScreen3}', ";
        $query .= "Screen2 = '{$combinedScreen2}', ";
        $query .= "Screen1p5 = '{$combinedScreen1p5}', ";
        $query .= "Screen1 = '{$combinedScreen1}', ";
        $query .= "Screen3p4 = '{$combinedScreen3p4}', ";
        $query .= "Screen1p2 = '{$combinedScreen1p2}', ";
        $query .= "Screen3p8 = '{$combinedScreen3p8}', ";
        $query .= "ScreenNo4 = '{$combinedScreenNo4}', ";
        $query .= "ScreenNo20 = '{$combinedScreenNo20}', ";
        $query .= "ScreenNo200 = '{$combinedScreenNo200}', ";
        $query .= "ScreenPan = '{$combinedScreenPan}', ";
        $query .= "ScreenTotal = '{$combinedsTotal}', ";
        $query .= "WtPassHumedoLess3 = '{$combinedWtPhumedo}', ";
        $query .= "WtPassSecoSucioLess3 = '{$combinedWtReSecoSucio}', ";
        $query .= "Container = '{$combinedContainer}', ";
        $query .= "WtSoilTare = '{$combinedWetSoil}', ";
        $query .= "WtSoilDry = '{$combinedWetDry}', ";
        $query .= "WtWater = '{$combinedWetWater}', ";
        $query .= "TareMC = '{$combinedTareMC}', ";
        $query .= "WtDrySoil = '{$combinedWtDrySoil}', ";
        $query .= "MC = '{$combinedMoisturePercet}', ";
        $query .= "WtRet = '{$combinedWtRet}', ";
        $query .= "Ret = '{$combinedRet}', ";
        $query .= "CumRet = '{$combinedCumRet}', ";
        $query .= "Pass = '{$combinedPass}', ";
        $query .= "Specs = '{$combinedSpecs}', ";
        $query .= "MoistureContentAvg = '{$MoistureContentAvg}', ";
        $query .= "TotalDryWtSampleLess3g = '{$TotalDryWtSampleLess3g}', ";
        $query .= "ConvertionFactor = '{$ConvertionFactor}', ";
        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('/reviews/grain-size-full.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('/reviews/grain-size-full.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/reviews/grain-size-full.php?id=' . $Search, false);
    }
}
