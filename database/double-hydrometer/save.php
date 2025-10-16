<?php
$user = current_user();

if (isset($_POST['Save'])) {
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
        $TestType = "DHY";
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
        $TareName = $db->escape($_POST['TareName1']);
        $OvenTemp = $db->escape($_POST['OvenTemp1']);
        $TareWetSoil = $db->escape($_POST['TareWetSoil1']);
        $TareDrySoil = $db->escape($_POST['TareDrySoil1']);
        $WaterWw = $db->escape($_POST['WaterWw1']);
        $TareMc = $db->escape($_POST['TareMc1']);
        $DrySoilWs = $db->escape($_POST['DrySoilWs1']);
        $MC = $db->escape($_POST['MC1']);

        $TareName50g = $db->escape($_POST['TareName2']);
        $OvenTemp50g = $db->escape($_POST['OvenTemp2']);
        $TareWetSoil50g = $db->escape($_POST['TareWetSoil2']);
        $TareDrySoil50g = $db->escape($_POST['TareDrySoil2']);
        $WaterWw50g = $db->escape($_POST['WaterWw2']);
        $TareMc50g = $db->escape($_POST['TareMc2']);
        $DrySoilWs50g = $db->escape($_POST['DrySoilWs2']);
        $MC50g = $db->escape($_POST['MC2']);

        // Atterberg Limits & Speceific Gravity
        $LiquidLimit = $db->escape($_POST['LiquidLimit']);
        $PlasticityIndex = $db->escape($_POST['PlasticityIndex']);
        $SG_Result = $db->escape($_POST['SG_Result']);

        function buildCSV($name, $total, $db)
        {
            $data = [];
            for ($i = 1; $i <= $total; $i++) {
                $val = trim($_POST[$name . $i]);
                $data[] = ($val === '') ? 'null' : $db->escape($val);
            }
            return implode(',', $data);
        }

        $combinedHyCalibrationTemp = buildCSV('HyCalibrationTemp', 9, $db);
        $combinedHyCalibrationRead = buildCSV('HyCalibrationRead', 9, $db);
        $combinedHyMeasureTemp = buildCSV('HyMeasureTemp', 9, $db);
        $combinedHyMeasureFluid = buildCSV('HyMeasureFluid', 9, $db);

        $combinedHyCalibrationTemp50g = buildCSV('HyCalibrationTemp50g', 9, $db);
        $combinedHyCalibrationRead50g = buildCSV('HyCalibrationRead50g', 9, $db);
        $combinedHyMeasureTemp50g = buildCSV('HyMeasureTemp50g', 9, $db);
        $combinedHyMeasureFluid50g = buildCSV('HyMeasureFluid50g', 9, $db);

        $combinedAirDriedMassHydrometer = buildCSV('AirDriedMassHydrometer', 2, $db);
        $combinedDryMassHydrometer = buildCSV('DryMassHydrometer', 2, $db);
        $combinedMassRetainedAfterHy = buildCSV('MassRetainedAfterHy', 2, $db);
        $combinedDryMassHySpecimenPassing = buildCSV('DryMassHySpecimenPassing', 2, $db);
        $combinedFineContentHySpecimen = buildCSV('FineContentHySpecimen', 2, $db);

        $combinedDates = buildCSV('Date', 9, $db);
        $combinedHour = buildCSV('Hour', 9, $db);
        $combinedReadingTimeT = buildCSV('ReadingTimeT', 9, $db);
        $combinedTemp = buildCSV('Temp', 9, $db);
        $combinedHyReading = buildCSV('HyReading', 9, $db);
        $combinedABdependingHy = buildCSV('ABdependingHy', 9, $db);
        $combinedOffsetReading = buildCSV('OffsetReading', 9, $db);
        $combinedMassPercentFiner = buildCSV('MassPercentFiner', 9, $db);
        $combinedEffectiveLength = buildCSV('EffectiveLength', 9, $db);
        $combinedDMm = buildCSV('DMm', 9, $db);

        $combinedDates50g = buildCSV('Date50g', 9, $db);
        $combinedHour50g = buildCSV('Hour50g', 9, $db);
        $combinedReadingTimeT50g = buildCSV('ReadingTimeT50g', 9, $db);
        $combinedTemp50g = buildCSV('Temp50g', 9, $db);
        $combinedHyReading50g = buildCSV('HyReading50g', 9, $db);
        $combinedABdependingHy50g = buildCSV('ABdependingHy50g', 9, $db);
        $combinedOffsetReading50g = buildCSV('OffsetReading50g', 9, $db);
        $combinedMassPercentFiner50g = buildCSV('MassPercentFiner50g', 9, $db);
        $combinedEffectiveLength50g = buildCSV('EffectiveLength50g', 9, $db);
        $combinedDMm50g = buildCSV('DMm50g', 9, $db);

        $combinedNm2umDispersed = buildCSV('Nm2umDispersed', 4, $db);

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM hydrometer
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
            $SampleNumber = $baseSampleNumber . '-R' . ($maxSuffix + 1);
        }


        $sql = "INSERT INTO double_hydrometer (
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
            TareName50g,
            OvenTemp50g,
            TareWetSoil50g,
            TareDrySoil50g,
            WaterWw50g,
            TareMc50g,
            DrySoilWs50g,
            MC50g,
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
            HyCalibrationTemp50g,
            HyCalibrationRead50g,
            HyMeasureTemp50g,
            HyMeasureFluid50g,
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
            Date50g,
            Hour50g,
            ReadingTimeT50g,
            Temp50g,
            HyReading50g,
            ABdependingHy50g,
            OffsetReading50g,
            MassPercentFiner50g,
            EffectiveLength50g,
            DMm50g,
            Classification";

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
            '$TareName50g',
            '$OvenTemp50g',
            '$TareWetSoil50g',
            '$TareDrySoil50g',
            '$WaterWw50g',
            '$TareMc50g',
            '$DrySoilWs50g',
            '$MC50g',
            '$combinedAirDriedMassHydrometer',
            '$combinedDryMassHydrometer',
            '$combinedMassRetainedAfterHy',
            '$combinedDryMassHySpecimenPassing',
            '$combinedFineContentHySpecimen',
            '$LiquidLimit',
            '$PlasticityIndex',
            '$SG_Result',
            '$combinedHyCalibrationTemp',
            '$combinedHyCalibrationRead',
            '$combinedHyMeasureTemp',
            '$combinedHyMeasureFluid',
            '$combinedHyCalibrationTemp50g',
            '$combinedHyCalibrationRead50g',
            '$combinedHyMeasureTemp50g',
            '$combinedHyMeasureFluid50g',
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
            '$combinedDates50g',
            '$combinedHour50g',
            '$combinedReadingTimeT50g',
            '$combinedTemp50g',
            '$combinedHyReading50g',
            '$combinedABdependingHy50g',
            '$combinedOffsetReading50g',
            '$combinedMassPercentFiner50g',
            '$combinedEffectiveLength50g',
            '$combinedDMm50g',
            '$combinedNm2umDispersed'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado con éxito.");
            redirect('/pages/double-hydrometer.php', false);
        } else {
            $session->msg('w', 'Lo sentimos, el ensayo no se pudo agregar.');
            redirect('/pages/double-hydrometer.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/double-hydrometer.php', false);
    }
}
