<?php
 $user = current_user();

 if (isset($_POST['gs_utf'])) {
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
        $TestType = "GS_UTF";
        $id = uuid();

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

        $PanWtRen = $db->escape($_POST['PanWtRen']);
        $PanRet = $db->escape($_POST['PanRet']);
        $TotalWtRet = $db->escape($_POST['TotalWtRet']);
        $TotalRet = $db->escape($_POST['TotalRet']);
        $TotalCumRet = $db->escape($_POST['TotalCumRet']);
        $TotalPass = $db->escape($_POST['TotalPass']);

        $TotalWeight = $db->escape($_POST['TotalWeight']);
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

        for ($i = 1; $i <= 13; $i++) {
            ${"WtRet" . $i} = $db->escape($_POST["WtRet$i"]);
            ${"Ret" . $i} = $db->escape($_POST["Ret$i"]);
            ${"CumRet" . $i} = $db->escape($_POST["CumRet$i"]);
            ${"Pass" . $i} = $db->escape($_POST["Pass$i"]);
        }
        
        $sql = "INSERT INTO grain_size_upstream_transition_fill (
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
            PanWtRen,
            PanRet,
            TotalWtRet,
            TotalRet,
            TotalCumRet,
            TotalPass,
            Total_Sample_Weight,
            Weight_Used_For_The_Test,
            A_Particles_Reactive,
            B_Particles_Reactive,
            C_Particles_Reactive,
            Weight_Mat_Ret_No_4,
            Weight_Reactive_Part_Ret_No_4,
            Percent_Reactive_Particles,
            Average_Particles_Reactive,
            Reaction_Strength_Result,
            Acid_Reactivity_Test_Result,
            Graph";
        
        // Add the dynamically generated fields to the query
        for ($i = 1; $i <= 13; $i++) {
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
            '$PMethods',
            '$SMethods',
            '$TestMethod',
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
            '$PanWtRen',
            '$PanRet',
            '$TotalWtRet',
            '$TotalRet',
            '$TotalCumRet',
            '$TotalPass',
            '$TotalWeight',
            '$WeigtTest',
            '$Particles1',
            '$Particles2',
            '$Particles3',
            '$WeightNo4',
            '$WeightReactiveNo4',
            '$PercentReactive',
            '$AvgParticles',
            '$ReactionResult',
            '$AcidResult',
            '$Graph64'";
        
        // Add the dynamically generated values to the query
        for ($i = 1; $i <= 13; $i++) {
            $sql .= ", '${"WtRet$i"}', '${"Ret$i"}', '${"CumRet$i"}', '${"Pass$i"}'";
        }
        
        $sql .= ")";        

        if ($db->query($sql)) {
            $session->msg('s', "Essay added successfully.");
            redirect('../pages/grain-size-upstream-transition-fill.php', false);
        } else {
            $session->msg('d', 'Sorry, the essay could not be added.');
            redirect('../pages/grain-size-upstream-transition-fill.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/grain-size-upstream-transition-fill.php', false);
    }
 }
?>