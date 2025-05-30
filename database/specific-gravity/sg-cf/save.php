<?php
$user = current_user();

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
        $SMethods = $db->escape($_POST['SMethods']);
        $PMethods = $db->escape($_POST['PMethods']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $FieldComment = $db->escape($_POST['FieldComment']);
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
