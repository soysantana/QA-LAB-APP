<!-- PH -->
<?php
$user = current_user();

if (isset($_POST['Pinhole'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "PH";
        $id = uuid();

        $mcBefore = $db->escape($_POST['mcBefore']);
        $sgEM = $db->escape($_POST['sgEM']);
        $maxDryDensity = $db->escape($_POST['maxDryDensity']);
        $optimumMC = $db->escape($_POST['optimumMC']);
        $welSoilMold = $db->escape($_POST['welSoilMold']);
        $wtMold = $db->escape($_POST['wtMold']);
        $wtWetSoil = $db->escape($_POST['wtWetSoil']);
        $longitudSpecimen = $db->escape($_POST['longitudSpecimen']);
        $volSpecimen = $db->escape($_POST['volSpecimen']);
        $wetDensity = $db->escape($_POST['wetDensity']);
        $dryDensityGCM3 = $db->escape($_POST['dryDensityGCM3']);
        $porceCompaction = $db->escape($_POST['porceCompaction']);
        $mcAfter = $db->escape($_POST['mcAfter']);
        $wirePuchDiameter = $db->escape($_POST['wirePuchDiameter']);

        $nameBefore = $db->escape($_POST['nameBefore']);
        $tempBefore = $db->escape($_POST['tempBefore']);
        $wetSoil1 = $db->escape($_POST['wetSoil1']);
        $drySoil1 = $db->escape($_POST['drySoil1']);
        $water1 = $db->escape($_POST['water1']);
        $tare1 = $db->escape($_POST['tare1']);
        $drySoilWs1 = $db->escape($_POST['drySoilWs1']);
        $mc1 = $db->escape($_POST['mc1']);

        $nameAfter = $db->escape($_POST['nameAfter']);
        $tempAfter = $db->escape($_POST['tempAfter']);
        $wetSoil2 = $db->escape($_POST['wetSoil2']);
        $drySoil2 = $db->escape($_POST['drySoil2']);
        $water2 = $db->escape($_POST['water2']);
        $tare2 = $db->escape($_POST['tare2']);
        $drySoilWs2 = $db->escape($_POST['drySoilWs2']);
        $mc2 = $db->escape($_POST['mc2']);

        $Hole_Size_After = $db->escape($_POST['Hole_Size_After']);
        $Dispersive_Classification = $db->escape($_POST['Dispersive_Classification']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        for ($i = 1; $i <= 22; $i++) {
            ${"ML_" . $i} = $db->escape($_POST["ML_$i"]);
            ${"Seg_" . $i} = $db->escape($_POST["Seg_$i"]);
            ${"Flow_Rate_" . $i} = $db->escape($_POST["Flow_Rate_$i"]);
            ${"From_Side_" . $i} = $db->escape($_POST["From_Side_$i"]);
            ${"From_Top_" . $i} = $db->escape($_POST["From_Top_$i"]);
            ${"Observation_" . $i} = $db->escape($_POST["Observation_$i"]);
        }

        $sql = "INSERT INTO pinhole_test (
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
            Methods,
            MC_Before_Test,
            Specific_Gravity,
            Max_Dry_Density,
            Optimum_MC,
            Wet_Soil_Mold,
            Wet_Mold,
            Wet_Soil,
            Specimen_Length,
            Vol_Specimen,
            Wet_Density,
            Dry_Density,
            Porce_Compaction,
            MC_After_Test,
            Wire_Punch_Diameter,
            Tare_Name_MC_Before,
            Oven_Temp_MC_Before,
            Tare_Wet_Soil_MC_Before,
            Tare_Dry_Soil_MC_Before,
            Water_MC_Before,
            Tare_MC_Before,
            Dry_Soil_MC_Before,
            Porce_MC_Before,
            Tare_Name_MC_After,
            Oven_Temp_MC_After,
            Tare_Wet_Soil_MC_After,
            Tare_Dry_Soil_MC_After,
            Water_MC_After,
            Tare_MC_After,
            Dry_Soil_MC_After,
            Porce_MC_After,
            Hole_Size_After,
            Dispersive_Classification,
            Graph";

        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 22; $i++) {
            $sql .= ", ML_$i, Seg_$i, Flow_Rate_$i, From_Side_$i, From_Top_$i, Observation_$i";
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
            '$TestMethod',
            '$mcBefore',
            '$sgEM',
            '$maxDryDensity',
            '$optimumMC',
            '$welSoilMold',
            '$wtMold',
            '$wtWetSoil',
            '$longitudSpecimen',
            '$volSpecimen',
            '$wetDensity',
            '$dryDensityGCM3',
            '$porceCompaction',
            '$mcAfter',
            '$wirePuchDiameter',
            '$nameBefore',
            '$tempBefore',
            '$wetSoil1',
            '$drySoil1',
            '$water1',
            '$tare1',
            '$drySoilWs1',
            '$mc1',
            '$nameAfter',
            '$tempAfter',
            '$wetSoil2',
            '$drySoil2',
            '$water2',
            '$tare2',
            '$drySoilWs2',
            '$mc2',
            '$Hole_Size_After',
            '$Dispersive_Classification',
            '$Graph64'";

        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 22; $i++) {
            $sql .= ", '${"ML_$i"}', '${"Seg_$i"}', '${"Flow_Rate_$i"}', '${"From_Side_$i"}', '${"From_Top_$i"}', '${"Observation_$i"}'";
        }

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/pinhole-test.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/pinhole-test.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/pinhole-test.php', false);
    }
}
?>

<!-- Update PH -->
<?php
$Search = $_GET['id'];
if (isset($_POST['Update_PH'])) {
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
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "PH";

        $mcBefore = $db->escape($_POST['mcBefore']);
        $sgEM = $db->escape($_POST['sgEM']);
        $maxDryDensity = $db->escape($_POST['maxDryDensity']);
        $optimumMC = $db->escape($_POST['optimumMC']);
        $welSoilMold = $db->escape($_POST['welSoilMold']);
        $wtMold = $db->escape($_POST['wtMold']);
        $wtWetSoil = $db->escape($_POST['wtWetSoil']);
        $longitudSpecimen = $db->escape($_POST['longitudSpecimen']);
        $volSpecimen = $db->escape($_POST['volSpecimen']);
        $wetDensity = $db->escape($_POST['wetDensity']);
        $dryDensityGCM3 = $db->escape($_POST['dryDensityGCM3']);
        $porceCompaction = $db->escape($_POST['porceCompaction']);
        $mcAfter = $db->escape($_POST['mcAfter']);
        $wirePuchDiameter = $db->escape($_POST['wirePuchDiameter']);

        $nameBefore = $db->escape($_POST['nameBefore']);
        $tempBefore = $db->escape($_POST['tempBefore']);
        $wetSoil1 = $db->escape($_POST['wetSoil1']);
        $drySoil1 = $db->escape($_POST['drySoil1']);
        $water1 = $db->escape($_POST['water1']);
        $tare1 = $db->escape($_POST['tare1']);
        $drySoilWs1 = $db->escape($_POST['drySoilWs1']);
        $mc1 = $db->escape($_POST['mc1']);

        $nameAfter = $db->escape($_POST['nameAfter']);
        $tempAfter = $db->escape($_POST['tempAfter']);
        $wetSoil2 = $db->escape($_POST['wetSoil2']);
        $drySoil2 = $db->escape($_POST['drySoil2']);
        $water2 = $db->escape($_POST['water2']);
        $tare2 = $db->escape($_POST['tare2']);
        $drySoilWs2 = $db->escape($_POST['drySoilWs2']);
        $mc2 = $db->escape($_POST['mc2']);

        $Hole_Size_After = $db->escape($_POST['Hole_Size_After']);
        $Dispersive_Classification = $db->escape($_POST['Dispersive_Classification']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        $inputValues = array();
        for ($i = 1; $i <= 22; $i++) {
            $inputValues["ML_" . $i] = $db->escape($_POST["ML_$i"]);
            $inputValues["Seg_" . $i] = $db->escape($_POST["Seg_$i"]);
            $inputValues["Flow_Rate_" . $i] = $db->escape($_POST["Flow_Rate_$i"]);
            $inputValues["From_Side_" . $i] = $db->escape($_POST["From_Side_$i"]);
            $inputValues["From_Top_" . $i] = $db->escape($_POST["From_Top_$i"]);
            $inputValues["Observation_" . $i] = $db->escape($_POST["Observation_$i"]);
        }

        $query = "UPDATE pinhole_test SET ";
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
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";

        $query .= "MC_Before_Test = '{$mcBefore}', ";
        $query .= "Specific_Gravity = '{$sgEM}', ";
        $query .= "Max_Dry_Density = '{$maxDryDensity}', ";
        $query .= "Optimum_MC = '{$optimumMC}', ";
        $query .= "Wet_Soil_Mold = '{$welSoilMold}', ";
        $query .= "Wet_Mold = '{$wtMold}', ";
        $query .= "Wet_Soil = '{$wtWetSoil}', ";
        $query .= "Specimen_Length = '{$longitudSpecimen}', ";
        $query .= "Vol_Specimen = '{$volSpecimen}', ";
        $query .= "Wet_Density = '{$wetDensity}', ";
        $query .= "Dry_Density = '{$dryDensityGCM3}', ";
        $query .= "Porce_Compaction = '{$porceCompaction}', ";
        $query .= "MC_After_Test = '{$mcAfter}', ";
        $query .= "Wire_Punch_Diameter = '{$wirePuchDiameter}', ";

        $query .= "Tare_Name_MC_Before = '{$nameBefore}', ";
        $query .= "Oven_Temp_MC_Before = '{$tempBefore}', ";
        $query .= "Tare_Wet_Soil_MC_Before = '{$wetSoil1}', ";
        $query .= "Tare_Dry_Soil_MC_Before = '{$drySoil1}', ";
        $query .= "Water_MC_Before = '{$water1}', ";
        $query .= "Tare_MC_Before = '{$tare1}', ";
        $query .= "Dry_Soil_MC_Before = '{$drySoilWs1}', ";
        $query .= "Porce_MC_Before = '{$mc1}', ";

        $query .= "Tare_Name_MC_After = '{$nameAfter}', ";
        $query .= "Oven_Temp_MC_After = '{$tempAfter}', ";
        $query .= "Tare_Wet_Soil_MC_After = '{$wetSoil2}', ";
        $query .= "Tare_Dry_Soil_MC_After = '{$drySoil2}', ";
        $query .= "Water_MC_After = '{$water2}', ";
        $query .= "Tare_MC_After = '{$tare2}', ";
        $query .= "Dry_Soil_MC_After = '{$drySoilWs2}', ";
        $query .= "Porce_MC_After = '{$mc2}', ";

        $query .= "Hole_Size_After = '{$Hole_Size_After}', ";
        $query .= "Dispersive_Classification = '{$Dispersive_Classification}', ";

        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/pinhole-test.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/pinhole-test.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/pinhole-test.php?id=' . $Search, false);
    }
}
?>

<!-- Repeat PH -->
<?php
if (isset($_POST["Repeat_PH"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM pinhole_test WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_ID = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_ID,
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
                redirect("../reviews/pinhole-test.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Reviewed PH -->
<?php
if (isset($_POST["Reviewed_PH"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM pinhole_test WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];
            $RegisBy = $search_data[0]["Register_By"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_reviewed WHERE Sample_ID = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Register_By = '{$RegisBy}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
                    id,
                    Sample_ID,
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
                redirect("../reviews/pinhole-test.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
?>

<!-- Delete PH -->
<?php
if (isset($_POST['delete_ph']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('pinhole_test', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
?>