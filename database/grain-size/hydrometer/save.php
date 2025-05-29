<?php
$user = current_user();

if (isset($_POST['SaveHydrometer'])) {
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
        $FieldComment = $db->escape($_POST['FieldComment']);
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
            ${"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            ${"Ret" . $i} = $db->escape($_POST["Ret$i"]);
            ${"CumRet" . $i} = $db->escape($_POST["CumRet$i"]);
            ${"Pass" . $i} = $db->escape($_POST["Pass$i"]);
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
            if (!empty(${"HyCalibrationTemp" . $i})) {
                $combinedHyCalibrationTemp .= ($combinedHyCalibrationTemp ? ", " : "") . ${"HyCalibrationTemp" . $i};
            }
            if (!empty(${"HyCalibrationRead" . $i})) {
                $combinedHyCalibrationRead .= ($combinedHyCalibrationRead ? ", " : "") . ${"HyCalibrationRead" . $i};
            }
            if (!empty(${"HyMeasureTemp" . $i})) {
                $combinedHyMeasureTemp .= ($combinedHyMeasureTemp ? ", " : "") . ${"HyMeasureTemp" . $i};
            }
            if (!empty(${"HyMeasureFluid" . $i})) {
                $combinedHyMeasureFluid .= ($combinedHyMeasureFluid ? ", " : "") . ${"HyMeasureFluid" . $i};
            }
            if (!empty(${"Date" . $i})) {
                $combinedDates .= ($combinedDates ? ", " : "") . ${"Date" . $i};
            }
            if (!empty(${"Hour" . $i})) {
                $combinedHour .= ($combinedHour ? ", " : "") . ${"Hour" . $i};
            }
            if (!empty(${"ReadingTimeT" . $i})) {
                $combinedReadingTimeT .= ($combinedReadingTimeT ? ", " : "") . ${"ReadingTimeT" . $i};
            }
            if (!empty(${"Temp" . $i})) {
                $combinedTemp .= ($combinedTemp ? ", " : "") . ${"Temp" . $i};
            }
            if (!empty(${"HyReading" . $i})) {
                $combinedHyReading .= ($combinedHyReading ? ", " : "") . ${"HyReading" . $i};
            }
            if (!empty(${"ABdependingHy" . $i})) {
                $combinedABdependingHy .= ($combinedABdependingHy ? ", " : "") . ${"ABdependingHy" . $i};
            }
            if (!empty(${"OffsetReading" . $i})) {
                $combinedOffsetReading .= ($combinedOffsetReading ? ", " : "") . ${"OffsetReading" . $i};
            }
            if (!empty(${"MassPercentFiner" . $i})) {
                $combinedMassPercentFiner .= ($combinedMassPercentFiner ? ", " : "") . ${"MassPercentFiner" . $i};
            }
            if (!empty(${"EffectiveLength" . $i})) {
                $combinedEffectiveLength .= ($combinedEffectiveLength ? ", " : "") . ${"EffectiveLength" . $i};
            }
            if (!empty(${"DMm" . $i})) {
                $combinedDMm .= ($combinedDMm ? ", " : "") . ${"DMm" . $i};
            }
            if (!empty(${"PassingPerceTotalSample" . $i})) {
                $combinedPassingPerceTotalSample .= ($combinedPassingPerceTotalSample ? ", " : "") . ${"PassingPerceTotalSample" . $i};
            }
        }


        $sql = "INSERT INTO hydrometer (
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
            DispersionDevice,
            HydrometerType,
            MixingMethod,
            SpecificGravitywas,
            DispersionAgent,
            AmountUsed,
            Temperatureoftest,
            Viscosityofwater,
            MassdensityofwaterCalibrated,
            Acceleration,
            Volumeofsuspension,
            MeniscusCorrection,
            TareName,
            OvenTemp,
            TareWetSoil,
            TareDrySoil,
            WaterWw,
            TareMc,
            DrySoilWs,
            MC,
            AirDriedMassHydrometer,
            DryMassHydrometer,
            MassRetainedAfterHy,
            DryMassHySpecimenPassing,
            FineContentHySpecimen,
            LiquidLimit,
            PlasticityIndex,
            SG_Result,
            HyCalibrationTemp,
            HyCalibrationRead,
            HyMeasureTemp,
            HyMeasureFluid,
            Date,
            Hour,
            ReadingTimeT,
            Temp,
            HyReading,
            ABdependingHy,
            OffsetReading,
            MassPercentFiner,
            EffectiveLength,
            DMm,
            PassingPerceTotalSample,
            Container,
            Wet_Soil_Tare,
            Wet_Dry_Tare,
            Tare,
            Wt_Dry_Soil,
            Wt_Washed,
            Wt_Wash_Pan,
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
            Classification1,
            PanWtRen,
            PanRet,
            TotalWtRet,
            TotalRet,
            TotalCumRet,
            TotalPass";

        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 17; $i++) {
            $sql .= ", WtRet$i, Ret$i, CumRet$i, Pass$i";
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
            '$DispersionDevice',
            '$HydrometerType',
            '$MixingMethod',
            '$SpecificGravitywas',
            '$DispersingAgent',
            '$Amountused',
            '$Temperatureoftest',
            '$Viscosityofwater',
            '$MassdensityofwaterCalibrated',
            '$Acceleration',
            '$Volumeofsuspension',
            '$MeniscusCorrection',
            '$TareName',
            '$OvenTemp',
            '$TareWetSoil',
            '$TareDrySoil',
            '$WaterWw',
            '$TareMc',
            '$DrySoilWs',
            '$MC',
            '$AirDriedMassHydrometer',
            '$DryMassHydrometer',
            '$MassRetainedAfterHy',
            '$DryMassHySpecimenPassing',
            '$FineContentHySpecimen',
            '$LiquidLimit',
            '$PlasticityIndex',
            '$SG_Result',
            '$combinedHyCalibrationTemp',
            '$combinedHyCalibrationRead',
            '$combinedHyMeasureTemp',
            '$combinedHyMeasureFluid',
            '$combinedDates',
            '$combinedHour',
            '$combinedReadingTimeT',
            '$combinedTemp',
            '$combinedHyReading',
            '$combinedABdependingHy',
            '$combinedOffsetReading',
            '$combinedMassPercentFiner',
            '$combinedEffectiveLength',
            '$combinedDMm',
            '$combinedPassingPerceTotalSample',
            '$Container',
            '$WetSoil',
            '$DrySoilTare',
            '$Tare',
            '$DrySoil',
            '$Washed',
            '$WashPan',
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
            '$Classification1',
            '$PanWtRen',
            '$PanRet',
            '$TotalWtRet',
            '$TotalRet',
            '$TotalCumRet',
            '$TotalPass'";

        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 17; $i++) {
            $sql .= ", '${"WtRet$i"}', '${"Ret$i"}', '${"CumRet$i"}', '${"Pass$i"}'";
        }

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado con éxito.");
            redirect('/pages/hydrometer.php', false);
        } else {
            $session->msg('w', 'Lo sentimos, el ensayo no se pudo agregar.');
            redirect('/pages/hydrometer.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/hydrometer.php', false);
    }
}
