<!-- Standard Proctor -->
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
            redirect('../pages/standard-proctor.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/standard-proctor.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/standard-proctor.php', false);
    }
 }
?>

<!-- Update -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-sp'])) {
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
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SP";

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

        $inputValues = array();
        for ($i = 1; $i <= 6; $i++) {
            $inputValues["WetSoilMod" . $i] = $db->escape($_POST["WetSoilMod$i"]);
            $inputValues["WtMold" . $i] = $db->escape($_POST["WtMold$i"]);
            $inputValues["WtSoil" . $i] = $db->escape($_POST["WtSoil$i"]);
            $inputValues["VolMold" . $i] = $db->escape($_POST["VolMold$i"]);
            $inputValues["WetDensity" . $i] = $db->escape($_POST["WetDensity$i"]);
            $inputValues["DryDensity" . $i] = $db->escape($_POST["DryDensity$i"]);
            $inputValues["DensyCorrected" . $i] = $db->escape($_POST["DensyCorrected$i"]);
            $inputValues["Container" . $i] = $db->escape($_POST["Container$i"]);
            $inputValues["WetSoilTare" . $i] = $db->escape($_POST["WetSoilTare$i"]);
            $inputValues["WetDryTare" . $i] = $db->escape($_POST["WetDryTare$i"]);
            $inputValues["WtWater" . $i] = $db->escape($_POST["WtWater$i"]);
            $inputValues["Tare" . $i] = $db->escape($_POST["Tare$i"]);
            $inputValues["DrySoil" . $i] = $db->escape($_POST["DrySoil$i"]);
            $inputValues["MoisturePorce" . $i] = $db->escape($_POST["MoisturePorce$i"]);
            $inputValues["MCcorrected" . $i] = $db->escape($_POST["MCcorrected$i"]);
        }

        $query = "UPDATE standard_proctor SET ";
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
        $query .= "Split_Method = '{$SMethods}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "Nat_Mc = '{$NatMc}', ";
        $query .= "Spec_Gravity = '{$SpecGravity}', ";
        $query .= "Max_Dry_Density_kgm3 = '{$MaxDryDensity}', ";
        $query .= "Optimun_MC_Porce = '{$OptimumMoisture}', ";
        $query .= "Corrected_Dry_Unit_Weigt = '{$CorrectedDryUnitWeigt}', ";
        $query .= "Corrected_Water_Content_Finer = '{$CorrectedWaterContentFiner}', ";
        $query .= "Wc_Porce = '{$WcPorce}', ";
        $query .= "YDF_Porce = '{$Ydf}', ";
        $query .= "PC_Porce = '{$PcPorce}', ";
        $query .= "PF_Porce = '{$PfPorce}', ";
        $query .= "GM_Porce = '{$Gm}', ";
        $query .= "YDT_Porce = '{$Ydt}', ";
        $query .= "Yw_KnM3 = '{$YwKnm}', ";
        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/standard-proctor.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/standard-proctor.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/standard-proctor.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat -->
<?php
 if (isset($_POST["repeat-sp"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM standard_proctor WHERE id = '{$Search}' LIMIT 1"
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
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    Sample_Name,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
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
                redirect("../reviews/standard-proctor.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed -->
<?php
 if (isset($_POST["reviewed-sp"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM standard_proctor WHERE id = '{$Search}' LIMIT 1"
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
                $RegistedDate = make_date();
                $ReviewedBy = $user["name"];

                $sql = "INSERT INTO test_reviewed (
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
                redirect("../reviews/standard-proctor.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>