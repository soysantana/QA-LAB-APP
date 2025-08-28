<?php
$user = current_user();

if (isset($_POST['specific-gravity-fine'])) {
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
        $SMethods = $db->escape($_POST['SMethods']);
        $PMethods = $db->escape($_POST['PMethods']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $FieldComment = $db->escape($_POST['FieldComment']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SG-Fine";
        $id = uuid();

        $SpecificGravityOD = $db->escape($_POST['SpecificGravityOD']);
        $SpecificGravitySSD = $db->escape($_POST['SpecificGravitySSD']);
        $ApparentSpecificGravity = $db->escape($_POST['ApparentSpecificGravity']);
        $PercentAbsortion = $db->escape($_POST['PercentAbsortion']);

        $PycnoNumber = $db->escape($_POST['PycnoNumber']);
        $WeightPycno = $db->escape($_POST['WeightPycno']);
        $WeightDryTare = $db->escape($_POST['WeightDryTare']);
        $WeightDry = $db->escape($_POST['WeightDry']);
        $WeightSurfaceAir = $db->escape($_POST['WeightSurfaceAir']);
        $TempSample = $db->escape($_POST['TempSample']);
        $WeightPycnoWater = $db->escape($_POST['WeightPycnoWater']);
        $CalibrationPycno = $db->escape($_POST['CalibrationPycno']);

        // --- Verificar si ya existe el Sample_Number en esta prueba ---
        $baseSampleNumber = $SampleNumber;
        $sqlCheck = "SELECT Sample_Number 
             FROM specific_gravity_fine
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

        $sql = "INSERT INTO specific_gravity_fine (
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
            SMethods,
            PMethods,
            Technician,
            Test_Start_Date,
            Comments,
            FieldComment,
            Methods,
            Specific_Gravity_OD,
            Specific_Gravity_SSD,
            Apparent_Specific_Gravity,
            Percent_Absortion,
            Pycnometer_Number,
            Weight_Pycnometer,
            Weight_Dry_Soil_Tare,
            Weight_Dry_Soil,
            Weight_Saturated_Surface_Dry_Soil_Air,
            Temperature_Sample,
            Weight_Pycnometer_Soil_Water,
            Calibration_Weight_Pycnometer_Desired_Temperature";

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
            '$SMethods',
            '$PMethods',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$FieldComment',
            '$TestMethod',
            '$SpecificGravityOD',
            '$SpecificGravitySSD',
            '$ApparentSpecificGravity',
            '$PercentAbsortion',
            '$PycnoNumber',
            '$WeightPycno',
            '$WeightDryTare',
            '$WeightDry',
            '$WeightSurfaceAir',
            '$TempSample',
            '$WeightPycnoWater',
            '$CalibrationPycno'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/specific-gravity-fine-aggregate.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/specific-gravity-fine-aggregate.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/specific-gravity-fine-aggregate.php', false);
    }
}
