<?php
$user = current_user();

if (isset($_POST['SaveReactivity'])) {
    $method = $_POST['SaveReactivity'];

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
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];
        if ($method === "FM13-007") {
            $TestType = "AR-CF";
        } elseif ($method === "FM13-006") {
            $TestType = "AR-FF";
        } else {
            $TestType = "AR";
        }
        $id = uuid();

        //Moisture Content Compaction
        $TotalWeight = $db->escape($_POST['TotalWeight']);
        $WeigtTest = $db->escape($_POST['WeigtTest']);
        $WeightNo4 = $db->escape($_POST['WeightNo4']);
        $WeightReactiveNo4 = $db->escape($_POST['WeightReactiveNo4']);
        $PercentReactive = $db->escape($_POST['PercentReactive']);
        $AvgParticles = $db->escape($_POST['AvgParticles']);
        $ReactionResult = $db->escape($_POST['ReactionResult']);
        $AcidResult = $db->escape($_POST['AcidResult']);

        $combinedParticlesReactive = "";

        // Reactivity Test Method FM13-007 : 3 & FM13-006 : 5
        $ParticleReactiveForMethod = ($method === "FM13-007") ? 3 : 5;
        for ($i = 1; $i <= $ParticleReactiveForMethod; $i++) {
            ${"ParticlesReactive" . $i} = $db->escape($_POST["Particles$i"]);

            // Concatenar si el campo tiene valor
            if (!empty(${"ParticlesReactive" . $i})) {
                $combinedParticlesReactive .= ($combinedParticlesReactive ? ", " : "") . ${"ParticlesReactive" . $i};
            }
        }


        $sql = "INSERT INTO reactivity (
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
            TotalWeight,
            WeigtTest,
            ParticlesReactive,
            WeightNo4,
            WeightReactiveNo4,
            PercentReactive,
            AvgParticles,
            ReactionResult,
            AcidReactivityResult,
            Technician,
            Test_Start_Date,
            Comments,
            FieldComment";

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
            '$TotalWeight',
            '$WeigtTest',
            '$combinedParticlesReactive',
            '$WeightNo4',
            '$WeightReactiveNo4',
            '$PercentReactive',
            '$AvgParticles',
            '$ReactionResult',
            '$AcidResult',
            '$Technician',
            '$DateTesting',
            '$Comments',
            '$FieldComment'";

        $sql .= ")";

        if ($db->query($sql)) {
            $session->msg('s', "Ensayo agregado con éxito.");
        } else {
            $session->msg('w', 'Lo sentimos, el ensayo no se pudo agregar.');
        }

        // Redirección final según el método
        if ($method === "FM13-007") {
            redirect('/pages/reactivity-coarse.php', false);
        } elseif ($method === "FM13-006") {
            redirect('/pages/reactivity-fine.php', false);
        } else {
            redirect('/pages/reactivity.php', false);
        }
    } else {
        $session->msg("d", $errors);

        // Redirección en caso de errores también según el método
        if ($method === "FM13-007") {
            redirect('/pages/reactivity-coarse.php', false);
        } elseif ($method === "FM13-006") {
            redirect('/pages/reactivity-fine.php', false);
        } else {
            redirect('/pages/reactivity.php', false);
        }
    }
}
