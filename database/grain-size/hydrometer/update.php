<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['UpdateHydrometer'])) {
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
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $PMethods = $db->escape($_POST['PMethods']);
        $DispersionDevice = $db->escape($_POST['DispersionDevice']);
        $HydrometerType = $db->escape($_POST['HydrometerType']);
        $MixingMethod = $db->escape($_POST['MixingMethod']);
        $SpecificGravitywas = $db->escape($_POST['SpecificGravitywas']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "HY";
        $id = uuid();

        // Hydrometer Analisis
        $DispersingAgent = $db->escape($_POST['DispersingAgent']);
        $Amountused = $db->escape($_POST['Amountused']);
        $Temperatureoftest = $db->escape($_POST['Temperatureoftest']);
        $Viscosityofwater = $db->escape($_POST['Viscosityofwater']);
        $MassdensityofwaterCalibrated = $db->escape($_POST['MassdensityofwaterCalibrated']);
        $Acceleration = $db->escape($_POST['Acceleration']);
        $Volumeofsuspension = $db->escape($_POST['Volumeofsuspension']);
        $MeniscusCorrection = $db->escape($_POST['MeniscusCorrection']);

        //Moisture Content Compaction
        $TareName = $db->escape($_POST['TareName']);
        $OvenTemp = $db->escape($_POST['OvenTemp']);
        $TareWetSoil = $db->escape($_POST['TareWetSoil']);
        $TareDrySoil = $db->escape($_POST['TareDrySoil']);
        $WaterWw = $db->escape($_POST['WaterWw']);
        $TareMc = $db->escape($_POST['TareMc']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs']);
        $MC = $db->escape($_POST['MC']);

        // Corretion Hydrometer
        $AirDriedMassHydrometer = $db->escape($_POST['AirDriedMassHydrometer']);
        $DryMassHydrometer = $db->escape($_POST['DryMassHydrometer']);
        $MassRetainedAfterHy = $db->escape($_POST['MassRetainedAfterHy']);
        $DryMassHySpecimenPassing = $db->escape($_POST['DryMassHySpecimenPassing']);
        $FineContentHySpecimen = $db->escape($_POST['FineContentHySpecimen']);

        // Atterberg Limits & Speceific Gravity
        $LiquidLimit = $db->escape($_POST['LiquidLimit']);
        $PlasticityIndex = $db->escape($_POST['PlasticityIndex']);
        $SG_Result = $db->escape($_POST['SG_Result']);


        $Container = $db->escape($_POST['Container']);
        $WetSoil = $db->escape($_POST['WtWetSoilTare']);
        $DrySoilTare = $db->escape($_POST['WtDrySoilTare']);
        $Tare = $db->escape($_POST['Tare_GS']);
        $DrySoil = $db->escape($_POST['WtDrySoil']);
        $Washed = $db->escape($_POST['WtWashed']);
        $WashPan = $db->escape($_POST['WtWashPan']);

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
        $Classification1 = $db->escape($_POST['Classification1']);

        $PanWtRen = $db->escape($_POST['PanWtRen']);
        $PanRet = $db->escape($_POST['PanRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $inputValues = array();

        $combinedHyCalibrationTemp = "";
        $combinedHyCalibrationRead = "";
        $combinedHyMeasureTemp = "";
        $combinedHyMeasureFluid = "";
        $combinedDates = "";
        $combinedHour = "";
        $combinedReadingTimeT = "";
        $combinedTemp = "";
        $combinedHyReading = "";
        $combinedABdependingHy = "";
        $combinedOffsetReading = "";
        $combinedMassPercentFiner = "";
        $combinedEffectiveLength = "";
        $combinedDMm = "";
        $combinedPassingPerceTotalSample = "";

        // Grain Size
        for ($i = 1; $i <= 17; $i++) {
            $inputValues{"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            $inputValues{"Ret" . $i} = $db->escape($_POST["Ret$i"]);
            $inputValues{"CumRet" . $i} = $db->escape($_POST["CumRet$i"]);
            $inputValues{"Pass" . $i} = $db->escape($_POST["Pass$i"]);
        }

        // Hydrometer Calibration & Analysis
        for ($i = 1; $i <= 9; $i++) {
            ${"HyCalibrationTemp" . $i} = $db->escape($_POST["HyCalibrationTemp$i"]);
            ${"HyCalibrationRead" . $i} = $db->escape($_POST["HyCalibrationRead$i"]);
            ${"HyMeasureTemp" . $i} = $db->escape($_POST["HyMeasureTemp$i"]);
            ${"HyMeasureFluid" . $i} = $db->escape($_POST["HyMeasureFluid$i"]);
            ${"Date" . $i} = $db->escape($_POST["Date$i"]);
            ${"Hour" . $i} = $db->escape($_POST["Hour$i"]);
            ${"ReadingTimeT" . $i} = $db->escape($_POST["ReadingTimeT$i"]);
            ${"Temp" . $i} = $db->escape($_POST["Temp$i"]);
            ${"HyReading" . $i} = $db->escape($_POST["HyReading$i"]);
            ${"ABdependingHy" . $i} = $db->escape($_POST["ABdependingHy$i"]);
            ${"OffsetReading" . $i} = $db->escape($_POST["OffsetReading$i"]);
            ${"MassPercentFiner" . $i} = $db->escape($_POST["MassPercentFiner$i"]);
            ${"EffectiveLength" . $i} = $db->escape($_POST["EffectiveLength$i"]);
            ${"DMm" . $i} = $db->escape($_POST["DMm$i"]);
            ${"PassingPerceTotalSample" . $i} = $db->escape($_POST["PassingPerceTotalSample$i"]);

            // Concatenar si el campo tiene valor
            if (!empty($inputValues{"HyCalibrationTemp" . $i})) {
                $combinedHyCalibrationTemp .= ($combinedHyCalibrationTemp ? ", " : "") . ${"HyCalibrationTemp" . $i};
            }
            if (!empty($inputValues{"HyCalibrationRead" . $i})) {
                $combinedHyCalibrationRead .= ($combinedHyCalibrationRead ? ", " : "") . ${"HyCalibrationRead" . $i};
            }
            if (!empty($inputValues{"HyMeasureTemp" . $i})) {
                $combinedHyMeasureTemp .= ($combinedHyMeasureTemp ? ", " : "") . ${"HyMeasureTemp" . $i};
            }
            if (!empty($inputValues{"HyMeasureFluid" . $i})) {
                $combinedHyMeasureFluid .= ($combinedHyMeasureFluid ? ", " : "") . ${"HyMeasureFluid" . $i};
            }
            if (!empty($inputValues{"Date" . $i})) {
                $combinedDates .= ($combinedDates ? ", " : "") . ${"Date" . $i};
            }
            if (!empty($inputValues{"Hour" . $i})) {
                $combinedHour .= ($combinedHour ? ", " : "") . ${"Hour" . $i};
            }
            if (!empty($inputValues{"ReadingTimeT" . $i})) {
                $combinedReadingTimeT .= ($combinedReadingTimeT ? ", " : "") . ${"ReadingTimeT" . $i};
            }
            if (!empty($inputValues{"Temp" . $i})) {
                $combinedTemp .= ($combinedTemp ? ", " : "") . ${"Temp" . $i};
            }
            if (!empty($inputValues{"HyReading" . $i})) {
                $combinedHyReading .= ($combinedHyReading ? ", " : "") . ${"HyReading" . $i};
            }
            if (!empty($inputValues{"ABdependingHy" . $i})) {
                $combinedABdependingHy .= ($combinedABdependingHy ? ", " : "") . ${"ABdependingHy" . $i};
            }
            if (!empty($inputValues{"OffsetReading" . $i})) {
                $combinedOffsetReading .= ($combinedOffsetReading ? ", " : "") . ${"OffsetReading" . $i};
            }
            if (!empty($inputValues{"MassPercentFiner" . $i})) {
                $combinedMassPercentFiner .= ($combinedMassPercentFiner ? ", " : "") . ${"MassPercentFiner" . $i};
            }
            if (!empty($inputValues{"EffectiveLength" . $i})) {
                $combinedEffectiveLength .= ($combinedEffectiveLength ? ", " : "") . ${"EffectiveLength" . $i};
            }
            if (!empty($inputValues{"DMm" . $i})) {
                $combinedDMm .= ($combinedDMm ? ", " : "") . ${"DMm" . $i};
            }
            if (!empty($inputValues{"PassingPerceTotalSample" . $i})) {
                $combinedPassingPerceTotalSample .= ($combinedPassingPerceTotalSample ? ", " : "") . ${"PassingPerceTotalSample" . $i};
            }
        }

        $query = "UPDATE hydrometer SET ";
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
        $query .= "Preparation_Method = '{$PMethods}', ";
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
        $query .= "Classification1 = '{$Classification1}', ";
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
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('/reviews/hydrometer.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('/reviews/hydrometer.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/reviews/hydrometer.php?id=' . $Search, false);
    }
}
