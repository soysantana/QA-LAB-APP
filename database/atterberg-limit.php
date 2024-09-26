<!-- Atterberg Limit -->
<?php
 $user = current_user();

 if (isset($_POST['atterberg-limit'])) {
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
        $NatMc = $db->escape($_POST['NatMc']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        $TestType = "AL";
        $id = uuid();
        // Liquid
        $Blows1 = $db->escape($_POST['Blows1']);
        $Blows2 = $db->escape($_POST['Blows2']);
        $Blows3 = $db->escape($_POST['Blows3']);
        $LLContainer1 = $db->escape($_POST['LLContainer1']);
        $LLContainer2 = $db->escape($_POST['LLContainer2']);
        $LLContainer3 = $db->escape($_POST['LLContainer3']);
        $LLWetSoil1 = $db->escape($_POST['LLWetSoil1']);
        $LLWetSoil2 = $db->escape($_POST['LLWetSoil2']);
        $LLWetSoil3 = $db->escape($_POST['LLWetSoil3']);
        $LLDrySoilTare1 = $db->escape($_POST['LLDrySoilTare1']);
        $LLDrySoilTare2 = $db->escape($_POST['LLDrySoilTare2']);
        $LLDrySoilTare3 = $db->escape($_POST['LLDrySoilTare3']);
        $LLWater1 = $db->escape($_POST['LLWater1']);
        $LLWater2 = $db->escape($_POST['LLWater2']);
        $LLWater3 = $db->escape($_POST['LLWater3']);
        $LLTare1 = $db->escape($_POST['LLTare1']);
        $LLTare2 = $db->escape($_POST['LLTare2']);
        $LLTare3 = $db->escape($_POST['LLTare3']);
        $LLWtDrySoil1 = $db->escape($_POST['LLWtDrySoil1']);
        $LLWtDrySoil2 = $db->escape($_POST['LLWtDrySoil2']);
        $LLWtDrySoil3 = $db->escape($_POST['LLWtDrySoil3']);
        $LLMCPorce1 = $db->escape($_POST['LLMCPorce1']);
        $LLMCPorce2 = $db->escape($_POST['LLMCPorce2']);
        $LLMCPorce3 = $db->escape($_POST['LLMCPorce3']);
        // Plastic
        $PLContainer1 = $db->escape($_POST['PLContainer1']);
        $PLContainer2 = $db->escape($_POST['PLContainer2']);
        $PLContainer3 = $db->escape($_POST['PLContainer3']);
        $PLWetSoil1 = $db->escape($_POST['PLWetSoil1']);
        $PLWetSoil2 = $db->escape($_POST['PLWetSoil2']);
        $PLWetSoil3 = $db->escape($_POST['PLWetSoil3']);
        $PLDrySoilTare1 = $db->escape($_POST['PLDrySoilTare1']);
        $PLDrySoilTare2 = $db->escape($_POST['PLDrySoilTare2']);
        $PLDrySoilTare3 = $db->escape($_POST['PLDrySoilTare3']);
        $PLWater1 = $db->escape($_POST['PLWater1']);
        $PLWater2 = $db->escape($_POST['PLWater2']);
        $PLWater3 = $db->escape($_POST['PLWater3']);
        $PLTare1 = $db->escape($_POST['PLTare1']);
        $PLTare2 = $db->escape($_POST['PLTare2']);
        $PLTare3 = $db->escape($_POST['PLTare3']);
        $PLWtDrySoil1 = $db->escape($_POST['PLWtDrySoil1']);
        $PLWtDrySoil2 = $db->escape($_POST['PLWtDrySoil2']);
        $PLWtDrySoil3 = $db->escape($_POST['PLWtDrySoil3']);
        $PLMCPorce1 = $db->escape($_POST['PLMCPorce1']);
        $PLMCPorce2 = $db->escape($_POST['PLMCPorce2']);
        $PLMCPorce3 = $db->escape($_POST['PLMCPorce3']);
        $PLAvgMcPorce = $db->escape($_POST['PLAvgMcPorce']);
        // Summary
        $LLPorce = $db->escape($_POST['LLPorce']);
        $PLPorce = $db->escape($_POST['PLPorce']);
        $PLIndexPorce = $db->escape($_POST['PLIndexPorce']);
        $LLIndexPorce = $db->escape($_POST['LLIndexPorce']);
        $classifysoil = $db->escape($_POST['classifysoil']);
        $PlotLimit = $db->escape($_POST['PlotLimit']);
        $PlotPlasticity = $db->escape($_POST['PlotPlasticity']);
        $PlotLimit64 = str_replace('data:image/png;base64,', '', $PlotLimit);
        $PlotPlasticity64 = str_replace('data:image/png;base64,', '', $PlotPlasticity);
        $Rsquared = $db->escape($_POST['Rsquared']);
        

        $sql = "INSERT INTO atterberg_limit (
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
            Nat_Mc,
            LL_Blows_1,
            LL_Blows_2,
            LL_Blows_3,
            LL_Container_1,
            LL_Container_2,
            LL_Container_3,
            LL_Wet_Soil_1,
            LL_Wet_Soil_2,
            LL_Wet_Soil_3,
            LL_Dry_Soil_Tare_1,
            LL_Dry_Soil_Tare_2,
            LL_Dry_Soil_Tare_3,
            LL_Water_1,
            LL_Water_2,
            LL_Water_3,
            LL_Tare_1,
            LL_Tare_2,
            LL_Tare_3,
            LL_Wt_Dry_Soil_1,
            LL_Wt_Dry_Soil_2,
            LL_Wt_Dry_Soil_3,
            LL_MC_Porce_1,
            LL_MC_Porce_2,
            LL_MC_Porce_3,
            PL_Container_1,
            PL_Container_2,
            PL_Container_3,
            PL_Wet_Soil_1,
            PL_Wet_Soil_2,
            PL_Wet_Soil_3,
            PL_Dry_Soil_Tare_1,
            PL_Dry_Soil_Tare_2,
            PL_Dry_Soil_Tare_3,
            PL_Water_1,
            PL_Water_2,
            PL_Water_3,
            PL_Tare_1,
            PL_Tare_2,
            PL_Tare_3,
            PL_Wt_Dry_Soil_1,
            PL_Wt_Dry_Soil_2,
            PL_Wt_Dry_Soil_3,
            PL_MC_Porce_1,
            PL_MC_Porce_2,
            PL_MC_Porce_3,
            PL_Avg_Mc_Porce,
            Liquid_Limit_Porce,
            Plastic_Limit_Porce,
            Plasticity_Index_Porce,
            Liquidity_Index_Porce,
            Classification,
            Liquid_Limit_Plot,
            Plasticity_Chart,
            Rsquared
            )
        VALUES (
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
            '$NatMc',
            '$Blows1',
            '$Blows2',
            '$Blows3',
            '$LLContainer1',
            '$LLContainer2',
            '$LLContainer3',
            '$LLWetSoil1',
            '$LLWetSoil2',
            '$LLWetSoil3',
            '$LLDrySoilTare1',
            '$LLDrySoilTare2',
            '$LLDrySoilTare3',
            '$LLWater1',
            '$LLWater2',
            '$LLWater3',
            '$LLTare1',
            '$LLTare2',
            '$LLTare3',
            '$LLWtDrySoil1',
            '$LLWtDrySoil2',
            '$LLWtDrySoil3',
            '$LLMCPorce1',
            '$LLMCPorce2',
            '$LLMCPorce3',
            '$PLContainer1',
            '$PLContainer2',
            '$PLContainer3',
            '$PLWetSoil1',
            '$PLWetSoil2',
            '$PLWetSoil3',
            '$PLDrySoilTare1',
            '$PLDrySoilTare2',
            '$PLDrySoilTare3',
            '$PLWater1',
            '$PLWater2',
            '$PLWater3',
            '$PLTare1',
            '$PLTare2',
            '$PLTare3',
            '$PLWtDrySoil1',
            '$PLWtDrySoil2',
            '$PLWtDrySoil3',
            '$PLMCPorce1',
            '$PLMCPorce2',
            '$PLMCPorce3',
            '$PLAvgMcPorce',
            '$LLPorce',
            '$PLPorce',
            '$PLIndexPorce',
            '$LLIndexPorce',
            '$classifysoil',
            '$PlotLimit64',
            '$PlotPlasticity64',
            '$Rsquared'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/atterberg-limit.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/atterberg-limit.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/atterberg-limit.php', false);
    }
 }
?>

<!-- Update -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-atterberg-limit'])) {
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
        $NatMc = $db->escape($_POST['NatMc']);
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "AL";
        // Liquid
        $Blows1 = $db->escape($_POST['Blows1']);
        $Blows2 = $db->escape($_POST['Blows2']);
        $Blows3 = $db->escape($_POST['Blows3']);
        $LLContainer1 = $db->escape($_POST['LLContainer1']);
        $LLContainer2 = $db->escape($_POST['LLContainer2']);
        $LLContainer3 = $db->escape($_POST['LLContainer3']);
        $LLWetSoil1 = $db->escape($_POST['LLWetSoil1']);
        $LLWetSoil2 = $db->escape($_POST['LLWetSoil2']);
        $LLWetSoil3 = $db->escape($_POST['LLWetSoil3']);
        $LLDrySoilTare1 = $db->escape($_POST['LLDrySoilTare1']);
        $LLDrySoilTare2 = $db->escape($_POST['LLDrySoilTare2']);
        $LLDrySoilTare3 = $db->escape($_POST['LLDrySoilTare3']);
        $LLWater1 = $db->escape($_POST['LLWater1']);
        $LLWater2 = $db->escape($_POST['LLWater2']);
        $LLWater3 = $db->escape($_POST['LLWater3']);
        $LLTare1 = $db->escape($_POST['LLTare1']);
        $LLTare2 = $db->escape($_POST['LLTare2']);
        $LLTare3 = $db->escape($_POST['LLTare3']);
        $LLWtDrySoil1 = $db->escape($_POST['LLWtDrySoil1']);
        $LLWtDrySoil2 = $db->escape($_POST['LLWtDrySoil2']);
        $LLWtDrySoil3 = $db->escape($_POST['LLWtDrySoil3']);
        $LLMCPorce1 = $db->escape($_POST['LLMCPorce1']);
        $LLMCPorce2 = $db->escape($_POST['LLMCPorce2']);
        $LLMCPorce3 = $db->escape($_POST['LLMCPorce3']);
        // Plastic
        $PLContainer1 = $db->escape($_POST['PLContainer1']);
        $PLContainer2 = $db->escape($_POST['PLContainer2']);
        $PLContainer3 = $db->escape($_POST['PLContainer3']);
        $PLWetSoil1 = $db->escape($_POST['PLWetSoil1']);
        $PLWetSoil2 = $db->escape($_POST['PLWetSoil2']);
        $PLWetSoil3 = $db->escape($_POST['PLWetSoil3']);
        $PLDrySoilTare1 = $db->escape($_POST['PLDrySoilTare1']);
        $PLDrySoilTare2 = $db->escape($_POST['PLDrySoilTare2']);
        $PLDrySoilTare3 = $db->escape($_POST['PLDrySoilTare3']);
        $PLWater1 = $db->escape($_POST['PLWater1']);
        $PLWater2 = $db->escape($_POST['PLWater2']);
        $PLWater3 = $db->escape($_POST['PLWater3']);
        $PLTare1 = $db->escape($_POST['PLTare1']);
        $PLTare2 = $db->escape($_POST['PLTare2']);
        $PLTare3 = $db->escape($_POST['PLTare3']);
        $PLWtDrySoil1 = $db->escape($_POST['PLWtDrySoil1']);
        $PLWtDrySoil2 = $db->escape($_POST['PLWtDrySoil2']);
        $PLWtDrySoil3 = $db->escape($_POST['PLWtDrySoil3']);
        $PLMCPorce1 = $db->escape($_POST['PLMCPorce1']);
        $PLMCPorce2 = $db->escape($_POST['PLMCPorce2']);
        $PLMCPorce3 = $db->escape($_POST['PLMCPorce3']);
        $PLAvgMcPorce = $db->escape($_POST['PLAvgMcPorce']);
        // Summary
        $LLPorce = $db->escape($_POST['LLPorce']);
        $PLPorce = $db->escape($_POST['PLPorce']);
        $PLIndexPorce = $db->escape($_POST['PLIndexPorce']);
        $LLIndexPorce = $db->escape($_POST['LLIndexPorce']);
        $classifysoil = $db->escape($_POST['classifysoil']);
        $PlotLimit = $db->escape($_POST['PlotLimit']);
        $PlotPlasticity = $db->escape($_POST['PlotPlasticity']);
        $Rsquared = $db->escape($_POST['Rsquared']);
        $PlotLimit64 = str_replace('data:image/png;base64,', '', $PlotLimit);
        $PlotPlasticity64 = str_replace('data:image/png;base64,', '', $PlotPlasticity);

        $query = "UPDATE atterberg_limit SET ";
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
        $query .= "Nat_Mc = '{$NatMc}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        // Liquid
        $query .= "LL_Blows_1 = '{$Blows1}', ";
        $query .= "LL_Blows_2 = '{$Blows2}', ";
        $query .= "LL_Blows_3 = '{$Blows3}', ";
        $query .= "LL_Container_1 = '{$LLContainer1}', ";
        $query .= "LL_Container_2 = '{$LLContainer2}', ";
        $query .= "LL_Container_3 = '{$LLContainer3}', ";
        $query .= "LL_Wet_Soil_1 = '{$LLWetSoil1}', ";
        $query .= "LL_Wet_Soil_2 = '{$LLWetSoil2}', ";
        $query .= "LL_Wet_Soil_3 = '{$LLWetSoil3}', ";
        $query .= "LL_Dry_Soil_Tare_1 = '{$LLDrySoilTare1}', ";
        $query .= "LL_Dry_Soil_Tare_2 = '{$LLDrySoilTare2}', ";
        $query .= "LL_Dry_Soil_Tare_3 = '{$LLDrySoilTare3}', ";
        $query .= "LL_Water_1 = '{$LLWater1}', ";
        $query .= "LL_Water_2 = '{$LLWater2}', ";
        $query .= "LL_Water_3 = '{$LLWater3}', ";
        $query .= "LL_Tare_1 = '{$LLTare1}', ";
        $query .= "LL_Tare_2 = '{$LLTare2}', ";
        $query .= "LL_Tare_3 = '{$LLTare3}', ";
        $query .= "LL_Wt_Dry_Soil_1 = '{$LLWtDrySoil1}', ";
        $query .= "LL_Wt_Dry_Soil_2 = '{$LLWtDrySoil2}', ";
        $query .= "LL_Wt_Dry_Soil_3 = '{$LLWtDrySoil3}', ";
        $query .= "LL_MC_Porce_1 = '{$LLMCPorce1}', ";
        $query .= "LL_MC_Porce_2 = '{$LLMCPorce2}', ";
        $query .= "LL_MC_Porce_3 = '{$LLMCPorce3}', ";
        // Plastic
        $query .= "PL_Container_1 = '{$PLContainer1}', ";
        $query .= "PL_Container_2 = '{$PLContainer2}', ";
        $query .= "PL_Container_3 = '{$PLContainer3}', ";
        $query .= "PL_Wet_Soil_1 = '{$PLWetSoil1}', ";
        $query .= "PL_Wet_Soil_2 = '{$PLWetSoil2}', ";
        $query .= "PL_Wet_Soil_3 = '{$PLWetSoil3}', ";
        $query .= "PL_Dry_Soil_Tare_1 = '{$PLDrySoilTare1}', ";
        $query .= "PL_Dry_Soil_Tare_2 = '{$PLDrySoilTare2}', ";
        $query .= "PL_Dry_Soil_Tare_3 = '{$PLDrySoilTare3}', ";
        $query .= "PL_Water_1 = '{$PLWater1}', ";
        $query .= "PL_Water_2 = '{$PLWater2}', ";
        $query .= "PL_Water_3 = '{$PLWater3}', ";
        $query .= "PL_Tare_1 = '{$PLTare1}', ";
        $query .= "PL_Tare_2 = '{$PLTare2}', ";
        $query .= "PL_Tare_3 = '{$PLTare3}', ";
        $query .= "PL_Wt_Dry_Soil_1 = '{$PLWtDrySoil1}', ";
        $query .= "PL_Wt_Dry_Soil_2 = '{$PLWtDrySoil2}', ";
        $query .= "PL_Wt_Dry_Soil_3 = '{$PLWtDrySoil3}', ";
        $query .= "PL_MC_Porce_1 = '{$PLMCPorce1}', ";
        $query .= "PL_MC_Porce_2 = '{$PLMCPorce2}', ";
        $query .= "PL_MC_Porce_3 = '{$PLMCPorce3}', ";
        $query .= "PL_Avg_Mc_Porce = '{$PLAvgMcPorce}', ";
        // Sumary
        $query .= "Liquid_Limit_Porce = '{$LLPorce}', ";
        $query .= "Plastic_Limit_Porce = '{$PLPorce}', ";
        $query .= "Plasticity_Index_Porce = '{$PLIndexPorce}', ";
        $query .= "Liquidity_Index_Porce = '{$LLIndexPorce}', ";
        $query .= "Classification = '{$classifysoil}',";
        $query .= "Liquid_Limit_Plot = '{$PlotLimit64}',";
        $query .= "Plasticity_Chart = '{$PlotPlasticity64}',";
        $query .= "Rsquared = '{$Rsquared}'";

        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Sample has been updated');
            redirect('../reviews/atterberg-limit.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No changes were made');
            redirect('../reviews/atterberg-limit.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../reviews/atterberg-limit.php?id=' . $Search, false);
    }
 }
?>

<!-- Repeat -->
<?php
 if (isset($_POST["repeat-atterberg-limit"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM atterberg_limit WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/atterberg-limit.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>

<!-- Reviewed -->
<?php
 if (isset($_POST["reviewed-atterberg-limit"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM atterberg_limit WHERE id = '{$Search}' LIMIT 1"
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
                redirect("../reviews/atterberg-limit.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
 }
?>