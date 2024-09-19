<!-- Save Requisiton -->
<?php
 $user = current_user();

 if (isset($_POST['requisition-form'])) {
    $req_fields = array(
        'SampleName',
        'CollectionDate'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $id = uuid();
        $ProjectName = $db->escape($_POST['ProjectName']);
        $Client = $db->escape($_POST['Client']);
        $ProjectNumber = $db->escape($_POST['ProjectNumber']);
        $PackageID = $db->escape($_POST['PackageID']);
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
        $Cviaje = $db->escape($_POST['Cviaje']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        $Comments = $db->escape($_POST['Comments']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];

        for ($i = 1; $i <= 20; $i++) {
            ${"TestType" . $i} = $db->escape($_POST["TestType$i"]);
        }
        

        $sql = "INSERT INTO lab_test_requisition_form (
            id,
            Project_Name,
            Client,
            Project_Number,
            Package_ID,
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
            Comment,
            Sample_Date,
            Truck_Count,
            Sample_By,
            Registed_Date,
            Register_By,
            Test_Type1,
            Test_Type2,
            Test_Type3,
            Test_Type4,
            Test_Type5,
            Test_Type6,
            Test_Type7,
            Test_Type8,
            Test_Type9,
            Test_Type10,
            Test_Type11,
            Test_Type12,
            Test_Type13,
            Test_Type14,
            Test_Type15,
            Test_Type16,
            Test_Type17,
            Test_Type18,
            Test_Type19,
            Test_Type20
        )
        VALUES (
            '$id',
            '$ProjectName',
            '$Client',
            '$ProjectNumber',
            '$PackageID',
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
            '$Comments',
            '$CollectionDate',
            '$Cviaje',
            '$SampleBy',
            '$RegistedDate',
            '$RegisterBy',
            '$TestType1',
            '$TestType2',
            '$TestType3',
            '$TestType4',
            '$TestType5',
            '$TestType6',
            '$TestType7',
            '$TestType8',
            '$TestType9',
            '$TestType10',
            '$TestType11',
            '$TestType12',
            '$TestType13',
            '$TestType14',
            '$TestType15',
            '$TestType16',
            '$TestType17',
            '$TestType18',
            '$TestType19',
            '$TestType20'
        )";

        if ($db->query($sql)) {
            $session->msg('s', 'Formulario de requesicion se guardo correctamente.');
            redirect('/pages/requisition-form.php', false);
        } else {
            $session->msg('d', 'Lo siento, no se pudo agregar el Formulario de requesicion.');
            redirect('/pages/requisition-form.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/requisition-form.php', false);
    }
 }
?>

<!-- Update Requisiton -->
<?php
 $Search = $_GET['id'];
 if (isset($_POST['update-requisition'])) {
    $req_fields = array(
        'SampleName',
        'SampleNumber'
    );
    validate_fields($req_fields);

    if (empty($errors)) {
        $ProjectName = $db->escape($_POST['ProjectName']);
        $Client = $db->escape($_POST['Client']);
        $ProjectNumber = $db->escape($_POST['ProjectNumber']);
        $PackageID = $db->escape($_POST['PackageID']);
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
        $Cviaje = $db->escape($_POST['Cviaje']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        $Comments = $db->escape($_POST['Comments']);

        for ($i = 1; $i <= 20; $i++) {
            ${"TestType" . $i} = $db->escape($_POST["TestType$i"]);
        }

        $query = "UPDATE lab_test_requisition_form SET ";
        $query .= "Project_Name = '{$ProjectName}',";
        $query .= "Client = '{$Client}', ";
        $query .= "Project_Number = '{$ProjectNumber}', ";
        $query .= "Package_ID = '{$PackageID}', ";
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
        $query .= "Truck_Count = '{$Cviaje}', ";
        $query .= "Sample_By = '{$SampleBy}', ";
        $query .= "Comment = '{$Comments}', ";
        $query .= "Test_Type1 = '{$TestType1}', ";
        $query .= "Test_Type2 = '{$TestType2}', ";
        $query .= "Test_Type3 = '{$TestType3}', ";
        $query .= "Test_Type4 = '{$TestType4}', ";
        $query .= "Test_Type5 = '{$TestType5}', ";
        $query .= "Test_Type6 = '{$TestType6}', ";
        $query .= "Test_Type7 = '{$TestType7}', ";
        $query .= "Test_Type8 = '{$TestType8}', ";
        $query .= "Test_Type9 = '{$TestType9}', ";
        $query .= "Test_Type10 = '{$TestType10}', ";
        $query .= "Test_Type11 = '{$TestType11}', ";
        $query .= "Test_Type12 = '{$TestType12}', ";
        $query .= "Test_Type13 = '{$TestType13}', ";
        $query .= "Test_Type14 = '{$TestType14}', ";
        $query .= "Test_Type15 = '{$TestType15}', ";
        $query .= "Test_Type16 = '{$TestType16}', ";
        $query .= "Test_Type17 = '{$TestType17}', ";
        $query .= "Test_Type18 = '{$TestType18}', ";
        $query .= "Test_Type19 = '{$TestType19}', ";
        $query .= "Test_Type20 = '{$TestType20}' ";

        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'El Formulario de requisicion ha sido actualizada.');
            redirect('/pages/requisition-form-view.php', false);
        } else {
            $session->msg('w', 'No se hicieron cambios en el Formulario de requisicion');
            redirect('/pages/requisition-form-view.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/requisition-form-view.php', false);
    }
 }
?>

<!-- Delete Requisiton -->
<?php 
 page_require_level(2);
 if (isset($_POST['delete-requisition'])) { 
    $Search = $_GET['id'];

    // Asume que tienes una función delete_by_id definida que elimina registros de la tabla 'calendar'
    $ID = delete_by_id('lab_test_requisition_form', $Search);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    // Redirige a la página de planificación semanal después de la operación
    redirect('/pages/requisition-form-view.php');
 }
?>