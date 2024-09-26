<!-- Specific Gravity -->
<?php
 $user = current_user();

 if (isset($_POST['specific-gravity'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SG";
        $id = uuid();

        $PycnUsed = $db->escape($_POST['PycnUsed']);
        $PycnNumber = $db->escape($_POST['PycnNumber']);
        $TestTemp = $db->escape($_POST['TestTemp']);
        $MassDryPycn = $db->escape($_POST['MassDryPycn']);
        $VolumePycn = $db->escape($_POST['VolumePycn']);
        $DensityWaterTemp = $db->escape($_POST['DensityWaterTemp']);
        $PycnWaterTeDensityWaterTempmp = $db->escape($_POST['PycnWaterTemp']);
        $WeightTare = $db->escape($_POST['WeightTare']);
        $WeightSoil = $db->escape($_POST['WeightSoil']);
        $WeightPycnSoilWaterMpws = $db->escape($_POST['WeightPycnSoilWaterMpws']);
        $TestTempAfter = $db->escape($_POST['TestTempAfter']);
        $DensityWaterTempAfter = $db->escape($_POST['DensityWaterTempAfter']);
        $PycnWaterTempAfter = $db->escape($_POST['PycnWaterTempAfter']);
        $SgSoilTemp = $db->escape($_POST['SgSoilTemp']);
        $TempCoefficent = $db->escape($_POST['TempCoefficent']);
        $SgSolid = $db->escape($_POST['SgSolid']);

        
        $sql = "INSERT INTO specific_gravity (
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
            Methods,
            Pycnometer_Used,
            Pycnometer_Number,
            Test_Temperatur,
            Average_Calibrated_Mass_Dry_Pycnometer_Mp,
            Average_Calibrated_Volume_Pycnometer_Vp,
            Density_Water_Test_Temperature,
            Calibration_Weight_Pynometer_Temperature_Mpw,
            Weight__Dry_Soil_Tare,
            Weight_Dry_Soil_Ms,
            Weight_Pycnometer_Soil_Water_Mpws,
            Test_Temperatur_After,
            Density_Water_Test_Temperature_After,
            Calibration_Weight_Pynometer_Temp_After,
            Specific_Gravity_Soil_Solid_Test_Temp_Gt,
            Temperature_Coefficent_K,
            Specific_Gravity_Soil_Solid";

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
            '$TestMethod',
            '$PycnUsed',
            '$PycnNumber',
            '$TestTemp',
            '$MassDryPycn',
            '$VolumePycn',
            '$DensityWaterTemp',
            '$PycnWaterTeDensityWaterTempmp',
            '$WeightTare',
            '$WeightSoil',
            '$WeightPycnSoilWaterMpws',
            '$TestTempAfter',
            '$DensityWaterTempAfter',
            '$PycnWaterTempAfter',
            '$SgSoilTemp',
            '$TempCoefficent',
            '$SgSolid'";
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/specific-gravity.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/specific-gravity.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/specific-gravity.php', false);
    }
 }
?>

<!-- Update SG -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-sg'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SG";

        $PycnUsed = $db->escape($_POST['PycnUsed']);
        $PycnNumber = $db->escape($_POST['PycnNumber']);
        $TestTemp = $db->escape($_POST['TestTemp']);
        $MassDryPycn = $db->escape($_POST['MassDryPycn']);
        $VolumePycn = $db->escape($_POST['VolumePycn']);
        $DensityWaterTemp = $db->escape($_POST['DensityWaterTemp']);
        $PycnWaterTeDensityWaterTempmp = $db->escape($_POST['PycnWaterTemp']);
        $WeightTare = $db->escape($_POST['WeightTare']);
        $WeightSoil = $db->escape($_POST['WeightSoil']);
        $WeightPycnSoilWaterMpws = $db->escape($_POST['WeightPycnSoilWaterMpws']);
        $TestTempAfter = $db->escape($_POST['TestTempAfter']);
        $DensityWaterTempAfter = $db->escape($_POST['DensityWaterTempAfter']);
        $PycnWaterTempAfter = $db->escape($_POST['PycnWaterTempAfter']);
        $SgSoilTemp = $db->escape($_POST['SgSoilTemp']);
        $TempCoefficent = $db->escape($_POST['TempCoefficent']);
        $SgSolid = $db->escape($_POST['SgSolid']);

        $query = "UPDATE specific_gravity SET ";
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
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Pycnometer_Used = '{$PycnUsed}', ";
        $query .= "Pycnometer_Number = '{$PycnNumber}', ";
        $query .= "Test_Temperatur = '{$TestTemp}', ";
        $query .= "Average_Calibrated_Mass_Dry_Pycnometer_Mp = '{$MassDryPycn}', ";
        $query .= "Average_Calibrated_Volume_Pycnometer_Vp = '{$VolumePycn}', ";
        $query .= "Density_Water_Test_Temperature = '{$DensityWaterTemp}', ";
        $query .= "Calibration_Weight_Pynometer_Temperature_Mpw = '{$PycnWaterTeDensityWaterTempmp}', ";
        $query .= "Weight__Dry_Soil_Tare = '{$WeightTare}', ";
        $query .= "Weight_Dry_Soil_Ms = '{$WeightSoil}', ";
        $query .= "Weight_Pycnometer_Soil_Water_Mpws = '{$WeightPycnSoilWaterMpws}', ";
        $query .= "Test_Temperatur_After = '{$TestTempAfter}', ";
        $query .= "Density_Water_Test_Temperature_After = '{$DensityWaterTempAfter}', ";
        $query .= "Calibration_Weight_Pynometer_Temp_After = '{$PycnWaterTempAfter}', ";
        $query .= "Specific_Gravity_Soil_Solid_Test_Temp_Gt = '{$SgSoilTemp}', ";
        $query .= "Temperature_Coefficent_K = '{$TempCoefficent}', ";
        $query .= "Specific_Gravity_Soil_Solid = '{$SgSolid}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/specific-gravity.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/specific-gravity.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/specific-gravity.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat SG -->
<?php
 if (isset($_POST["repeat-sg"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to repeat");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed SG -->
<?php
 if (isset($_POST["reviewed-sg"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Reviewed_By,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$ReviewedBy',
                    '$RegisBy',
                    '$TestType',
                    '$ID',
                    'Reviewed'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to reviewd");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Delete SG -->
<?php
 if (isset($_POST['delete_sg']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('specific_gravity', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>

<!-- Specific Gravity Coarse -->
<?php
 if (isset($_POST['specific-gravity-coarse'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "SG-Coarse";
        $id = uuid();

        $SpecificGravityOD = $db->escape($_POST['SpecificGravityOD']);
        $SpecificGravitySSD = $db->escape($_POST['SpecificGravitySSD']);
        $ApparentSpecificGravity = $db->escape($_POST['ApparentSpecificGravity']);
        $PercentAbsortion = $db->escape($_POST['PercentAbsortion']);

        for ($i = 1; $i <= 10; $i++) {
            ${"OvenDry" . $i} = $db->escape($_POST["OvenDry$i"]);
            ${"SurfaceDry" . $i} = $db->escape($_POST["SurfaceDry$i"]);
            ${"SampImmers" . $i} = $db->escape($_POST["SampImmers$i"]);
        }


        
        $sql = "INSERT INTO specific_gravity_coarse (
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
            Methods,
            Specific_Gravity_OD,
            Specific_Gravity_SSD,
            Apparent_Specific_Gravity,
            Percent_Absortion";
            
            // Add the dynamically generated fields to the query
            for ($i = 1; $i <= 10; $i++) {
                $sql .= ", Oven_Dry_$i, Surface_Dry_$i, Samp_Immers_$i";
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
            '$TestMethod',
            '$SpecificGravityOD',
            '$SpecificGravitySSD',
            '$ApparentSpecificGravity',
            '$PercentAbsortion'";

            // Add the dynamically generated values to the query
            for ($i = 1; $i <= 10; $i++) {
                $sql .= ", '${"OvenDry$i"}', '${"SurfaceDry$i"}', '${"SampImmers$i"}'";
            }
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/specific-gravity-coarse-aggregates.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/specific-gravity-coarse-aggregates.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/specific-gravity-coarse-aggregates.php', false);
    }
 }
?>

<!-- Update SG Coarse -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-sg-coarse'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SG-Coarse";

        $SpecificGravityOD = $db->escape($_POST['SpecificGravityOD']);
        $SpecificGravitySSD = $db->escape($_POST['SpecificGravitySSD']);
        $ApparentSpecificGravity = $db->escape($_POST['ApparentSpecificGravity']);
        $PercentAbsortion = $db->escape($_POST['PercentAbsortion']);

        $inputValues = array();
        for ($i = 1; $i <= 10; $i++) {
            $inputValues["Oven_Dry_" . $i] = $db->escape($_POST["OvenDry$i"]);
            $inputValues["Surface_Dry_" . $i] = $db->escape($_POST["SurfaceDry$i"]);
            $inputValues["Samp_Immers_" . $i] = $db->escape($_POST["SampImmers$i"]);
        }

        $query = "UPDATE specific_gravity_coarse SET ";
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
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Specific_Gravity_OD = '{$SpecificGravityOD}', ";
        $query .= "Specific_Gravity_SSD = '{$SpecificGravitySSD}', ";
        $query .= "Apparent_Specific_Gravity = '{$ApparentSpecificGravity}', ";
        $query .= "Percent_Absortion = '{$PercentAbsortion}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/specific-gravity-coarse-aggregates.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat SG Coarse -->
<?php
 if (isset($_POST["repeat-sg-coarse"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity_coarse WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to repeat");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity-coarse-aggregates.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed SG Coarse -->
<?php
 if (isset($_POST["reviewed-sg-coarse"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity_coarse WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Reviewed_By,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$ReviewedBy',
                    '$RegisBy',
                    '$TestType',
                    '$ID',
                    'Reviewed'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to reviewd");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity-coarse-aggregates.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Delete SG Coarse -->
<?php
 if (isset($_POST['delete_sg_coarse']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('specific_gravity_coarse', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>

<!-- Specific Gravity Fine -->
<?php
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
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
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
            Technician,
            Test_Start_Date,
            Comments,
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
            '$Technician',
            '$DateTesting',
            '$Comments',
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
?>

<!-- Update SG Fine -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-sg-fine'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SG-Fine";

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

        $query = "UPDATE specific_gravity_fine SET ";
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
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Specific_Gravity_OD = '{$SpecificGravityOD}', ";
        $query .= "Specific_Gravity_SSD = '{$SpecificGravitySSD}', ";
        $query .= "Apparent_Specific_Gravity = '{$ApparentSpecificGravity}', ";
        $query .= "Percent_Absortion = '{$PercentAbsortion}', ";
        $query .= "Pycnometer_Number = '{$PycnoNumber}', ";
        $query .= "Weight_Pycnometer = '{$WeightPycno}', ";
        $query .= "Weight_Dry_Soil_Tare = '{$WeightDryTare}', ";
        $query .= "Weight_Dry_Soil = '{$WeightDry}', ";
        $query .= "Weight_Saturated_Surface_Dry_Soil_Air = '{$WeightSurfaceAir}', ";
        $query .= "Temperature_Sample = '{$TempSample}', ";
        $query .= "Weight_Pycnometer_Soil_Water = '{$WeightPycnoWater}', ";
        $query .= "Calibration_Weight_Pycnometer_Desired_Temperature = '{$CalibrationPycno}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/specific-gravity-fine-aggregate.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/specific-gravity-fine-aggregate.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/specific-gravity-fine-aggregate.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat SG Fine -->
<?php
 if (isset($_POST["repeat-sg-fine"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity_fine WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to repeat");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity-fine-aggregate.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed SG Fine -->
<?php
 if (isset($_POST["reviewed-sg-fine"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM specific_gravity_fine WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_Name = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Reviewed_By,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$ReviewedBy',
                    '$RegisBy',
                    '$TestType',
                    '$ID',
                    'Reviewed'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "essay sent to reviewd");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "A record already exists");
                redirect("../reviews/specific-gravity-fine-aggregate.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Delete SG Fine -->
<?php
 if (isset($_POST['delete_sg_fine']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('specific_gravity_fine', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>