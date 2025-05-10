<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['UpdateReactivity'])) {
    $method = $_POST['UpdateReactivity'];

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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
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

        $query = "UPDATE reactivity SET ";
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
        $query .= "TotalWeight = '{$TotalWeight}', ";
        $query .= "WeigtTest = '{$WeigtTest}', ";
        $query .= "ParticlesReactive = '{$combinedParticlesReactive}', ";
        $query .= "WeightNo4 = '{$WeightNo4}', ";
        $query .= "WeightReactiveNo4 = '{$WeightReactiveNo4}', ";
        $query .= "PercentReactive = '{$PercentReactive}', ";
        $query .= "AvgParticles = '{$AvgParticles}', ";
        $query .= "ReactionResult = '{$ReactionResult}', ";
        $query .= "AcidReactivityResult = '{$AcidResult}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', "La muestra ha sido actualizada.");
        } else {
            $session->msg('w', 'Lo sentimos, la muestra no se pudo actualizar.');
        }

        // Redirección final según el método
        if ($method === "FM13-007") {
            redirect('/reviews/reactivity-coarse.php?id=' . $Search, false);
        } elseif ($method === "FM13-006") {
            redirect('/reviews/reactivity-fine.php?id=' . $Search, false);
        } else {
            redirect('/pages/reactivity-menu.php', false);
        }
    } else {
        $session->msg("d", $errors);

        // Redirección en caso de errores también según el método
        if ($method === "FM13-007") {
            redirect('/reviews/reactivity-coarse.php?id=' . $Search, false);
        } elseif ($method === "FM13-006") {
            redirect('/reviews/reactivity-fine.php?id=' . $Search, false);
        } else {
            redirect('/pages/reactivity-menu.php', false);
        }
    }
}
