<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update-gs-coarse'])) {
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
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "GS_CF";

        $Container = $db->escape($_POST['Container']);
        $WetSoil = $db->escape($_POST['WetSoil']);
        $DrySoilTare = $db->escape($_POST['DrySoilTare']);
        $Tare = $db->escape($_POST['Tare']);
        $DrySoil = $db->escape($_POST['DrySoil']);
        $Washed = $db->escape($_POST['Washed']);
        $WashPan = $db->escape($_POST['WashPan']);

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
        $ClassificationUSCS1 = $db->escape($_POST['ClassificationUSCS1']);
        $ClassificationUSCS2 = $db->escape($_POST['ClassificationUSCS2']);

        $PanWtRen = $db->escape($_POST['PanWtRen']);
        $PanRet = $db->escape($_POST['PanRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $TotalWeight = $db->escape($_POST['WeigtTest']);
        $WeigtTest = $db->escape($_POST['WeigtTest']);
        $Particles1 = $db->escape($_POST['Particles1']);
        $Particles2 = $db->escape($_POST['Particles2']);
        $Particles3 = $db->escape($_POST['Particles3']);
        $WeightNo4 = $db->escape($_POST['Particles4']);
        $WeightReactiveNo4 = $db->escape($_POST['Particles5']);
        $PercentReactive = $db->escape($_POST['Particles5']);
        $AvgParticles = $db->escape($_POST['AvgParticles']);
        $ReactionResult = $db->escape($_POST['ReactionResult']);
        $AcidResult = $db->escape($_POST['AcidResult']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        $inputValues = array();
        for ($i = 1; $i <= 18; $i++) {
            $inputValues["WtRet" . $i] = $db->escape($_POST["WtRet$i"]);
            $inputValues["Ret" . $i] = $db->escape($_POST["Ret$i"]);
            $inputValues["CumRet" . $i] = $db->escape($_POST["CumRet$i"]);
            $inputValues["Pass" . $i] = $db->escape($_POST["Pass$i"]);
            $inputValues["Specs" . $i] = $db->escape($_POST["Specs$i"]);
        }

        $query = "UPDATE grain_size_coarse SET ";
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
        $query .= "ClassificationUSCS1 = '{$ClassificationUSCS1}', ";
        $query .= "ClassificationUSCS2 = '{$ClassificationUSCS2}', ";
        $query .= "PanWtRen = '{$PanWtRen}', ";
        $query .= "PanRet = '{$PanRet}', ";
        $query .= "TotalWtRet = '{$TotalWtRet}', ";
        $query .= "TotalRet = '{$TotalRet}', ";
        $query .= "TotalCumRet = '{$TotalCumRet}', ";
        $query .= "TotalPass = '{$TotalPass}', ";
        $query .= "Total_Sample_Weight = '{$TotalWeight}', ";
        $query .= "Weight_Used_For_The_Test = '{$WeigtTest}', ";
        $query .= "A_Particles_Reactive = '{$Particles1}', ";
        $query .= "B_Particles_Reactive = '{$Particles2}', ";
        $query .= "C_Particles_Reactive = '{$Particles3}', ";
        $query .= "Weight_Mat_Ret_No_4 = '{$WeightNo4}', ";
        $query .= "Weight_Reactive_Part_Ret_No_4 = '{$WeightReactiveNo4}', ";
        $query .= "Percent_Reactive_Particles = '{$PercentReactive}', ";
        $query .= "Average_Particles_Reactive = '{$AvgParticles}', ";
        $query .= "Reaction_Strength_Result = '{$ReactionResult}', ";
        $query .= "Acid_Reactivity_Test_Result = '{$AcidResult}', ";
        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('/reviews/grain-size-coarse-filter.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('/reviews/grain-size-coarse-filter.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/reviews/grain-size-coarse-filter.php?id=' . $Search, false);
    }
}
