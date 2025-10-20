<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['Update'])) {
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "DHY";

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

        $query = "UPDATE double_hydrometer SET ";
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
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Preparation_Method = '{$PMethods}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "DispersionAgent = '{$DispersingAgent}', ";
        $query .= "Amountused = '{$Amountused}', ";
        $query .= "Temperatureoftest = '{$Temperatureoftest}', ";
        $query .= "Viscosityofwater = '{$Viscosityofwater}', ";
        $query .= "MassdensityofwaterCalibrated = '{$MassdensityofwaterCalibrated}', ";
        $query .= "Acceleration = '{$Acceleration}', ";
        $query .= "Volumeofsuspension = '{$Volumeofsuspension}', ";
        $query .= "MeniscusCorrection = '{$MeniscusCorrection}', ";
        $query .= "TareName = '{$TareName}', ";
        $query .= "OvenTemp = '{$OvenTemp}', ";
        $query .= "TareWetSoil = '{$TareWetSoil}', ";
        $query .= "TareDrySoil = '{$TareDrySoil}', ";
        $query .= "WaterWw = '{$WaterWw}', ";
        $query .= "TareMc = '{$TareMc}', ";
        $query .= "DrySoilWs = '{$DrySoilWs}', ";
        $query .= "MC = '{$MC}', ";
        $query .= "TareName50g = '{$TareName50g}', ";
        $query .= "OvenTemp50g = '{$OvenTemp50g}', ";
        $query .= "TareWetSoil50g = '{$TareWetSoil50g}', ";
        $query .= "TareDrySoil50g = '{$TareDrySoil50g}', ";
        $query .= "WaterWw50g = '{$WaterWw50g}', ";
        $query .= "TareMc50g = '{$TareMc50g}', ";
        $query .= "DrySoilWs50g = '{$DrySoilWs50g}', ";
        $query .= "MC50g = '{$MC50g}', ";
        $query .= "AirDriedMassHydrometer = '{$combinedAirDriedMassHydrometer}', ";
        $query .= "DryMassHydrometer = '{$combinedDryMassHydrometer}', ";
        $query .= "MassRetainedAfterHy = '{$combinedMassRetainedAfterHy}', ";
        $query .= "DryMassHySpecimenPassing = '{$combinedDryMassHySpecimenPassing}', ";
        $query .= "FineContentHySpecimen = '{$combinedFineContentHySpecimen}', ";
        $query .= "LiquidLimit = '{$LiquidLimit}', ";
        $query .= "PlasticityIndex = '{$PlasticityIndex}', ";
        $query .= "SG_Result = '{$SG_Result}', ";
        $query .= "HyCalibrationTemp = '{$combinedHyCalibrationTemp}', ";
        $query .= "HyCalibrationRead = '{$combinedHyCalibrationRead}', ";
        $query .= "HyMeasureTemp = '{$combinedHyMeasureTemp}', ";
        $query .= "HyMeasureFluid = '{$combinedHyMeasureFluid}', ";
        $query .= "HyCalibrationTemp50g = '{$combinedHyCalibrationTemp50g}', ";
        $query .= "HyCalibrationRead50g = '{$combinedHyCalibrationRead50g}', ";
        $query .= "HyMeasureTemp50g = '{$combinedHyMeasureTemp50g}', ";
        $query .= "HyMeasureFluid50g = '{$combinedHyMeasureFluid50g}', ";
        $query .= "Date = '{$combinedDates}', ";
        $query .= "Hour = '{$combinedHour}', ";
        $query .= "ReadingTimeT = '{$combinedReadingTimeT}', ";
        $query .= "Temp = '{$combinedTemp}', ";
        $query .= "HyReading = '{$combinedHyReading}', ";
        $query .= "ABdependingHy = '{$combinedABdependingHy}', ";
        $query .= "OffsetReading = '{$combinedOffsetReading}', ";
        $query .= "MassPercentFiner = '{$combinedMassPercentFiner}', ";
        $query .= "EffectiveLength = '{$combinedEffectiveLength}', ";
        $query .= "DMm = '{$combinedDMm}',";
        $query .= "Date50g = '{$combinedDates50g}', ";
        $query .= "Hour50g = '{$combinedHour50g}', ";
        $query .= "ReadingTimeT50g = '{$combinedReadingTimeT50g}', ";
        $query .= "Temp50g = '{$combinedTemp50g}', ";
        $query .= "HyReading50g = '{$combinedHyReading50g}', ";
        $query .= "ABdependingHy50g = '{$combinedABdependingHy50g}', ";
        $query .= "OffsetReading50g = '{$combinedOffsetReading50g}', ";
        $query .= "MassPercentFiner50g = '{$combinedMassPercentFiner50g}', ";
        $query .= "EffectiveLength50g = '{$combinedEffectiveLength50g}', ";
        $query .= "DMm50g = '{$combinedDMm50g}',";
        $query .= "Classification = '{$combinedNm2umDispersed}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('/reviews/double-hydrometer.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('/reviews/double-hydrometer.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/reviews/double-hydrometer.php?id=' . $Search, false);
    }
}
