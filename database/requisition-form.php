<!-- Save Requisiton -->
<?php
$user = current_user();

if (isset($_POST['requisition-form'])) {
    $req_fields = array(
        'SampleBy',
        'CollectionDate'
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
        $Cviaje = $db->escape($_POST['Cviaje']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];

        $sampleData = [];

        foreach ($_POST as $key => $value) {
            if (preg_match('/^(SampleName|SampleNumber|DepthFrom|DepthTo|MType|SType|North|East|Elev|Comments)_(\d+)$/', $key, $matches)) {
                $field = $matches[1];   // Campo base: SampleName, SampleNumber, etc.
                $index = $matches[2];   // Índice del grupo: 1, 2, 3...

                if (!isset($sampleData[$index])) {
                    $sampleData[$index] = [];
                }

                $sampleData[$index][$field] = $db->escape($value);
            }
        }

        foreach ($_POST as $key => $value) {
            if (preg_match('/^TestType_(\d+)$/', $key, $matches)) {
                $index = $matches[1];

                if (!isset($sampleData[$index])) {
                    $sampleData[$index] = [];
                }

                // Escapar cada test y luego unirlos con coma (u otro separador)
                $escapedTests = array_map(function ($test) use ($db) {
                    return $db->escape($test);
                }, $value);

                // Guardar en una sola cadena separada por comas
                $sampleData[$index]["TestType"] = implode(',', $escapedTests);
            }
        }


        foreach ($sampleData as $index => $data) {
            $id = uuid(); // O usa un ID general si todas son parte de la misma requisición

            $sql = "INSERT INTO lab_test_requisition_form (
        id,
        Project_Name,
        Client,
        Project_Number,
        Package_ID,
        Structure,
        Area,
        Source,
        Sample_Date,
        Truck_Count,
        Sample_By,
        Registed_Date,
        Register_By,
        Sample_ID,
        Sample_Number,
        Depth_From,
        Depth_To,
        Material_Type,
        Sample_Type,
        North,
        East,
        Elev,
        Comment,
        Test_Type
    ) VALUES (
        '$id',
        '$ProjectName',
        '$Client',
        '$ProjectNumber',
        '$PackageID',
        '$Structure',
        '$Area',
        '$Source',
        '$CollectionDate',
        '$Cviaje',
        '$SampleBy',
        '$RegistedDate',
        '$RegisterBy',
        '{$data['SampleName']}',
        '{$data['SampleNumber']}',
        '{$data['DepthFrom']}',
        '{$data['DepthTo']}',
        '{$data['MType']}',
        '{$data['SType']}',
        '{$data['North']}',
        '{$data['East']}',
        '{$data['Elev']}',
        '{$data['Comments']}',
        '{$data['TestType']}'
    )";

            // 👇 Aquí se ejecuta el insert por cada muestra
            $db->query($sql);
        }

        // ✅ Mensaje de éxito después de insertar todo
        $session->msg('s', 'Formulario de requisición guardado correctamente.');
        redirect('/pages/requisition-form.php', false);
    } else {
        $session->msg('d', 'Lo siento, no se pudo agregar ninguna muestra.');
        redirect('/pages/requisition-form.php', false);
    }
}
?>

<!-- Update Requisiton -->
<?php
$Search = $_GET['id'];
if (isset($_POST['update-requisition'])) {
    $req_fields = array(
        'SampleName_1',
        'SampleNumber_1'
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
        $SampleID = $db->escape($_POST['SampleName_1']);
        $SampleNumber = $db->escape($_POST['SampleNumber_1']);
        $DepthFrom = $db->escape($_POST['DepthFrom_1']);
        $DepthTo = $db->escape($_POST['DepthTo_1']);
        $MType = $db->escape($_POST['MType_1']);
        $SType = $db->escape($_POST['SType_1']);
        $North = $db->escape($_POST['North_1']);
        $East = $db->escape($_POST['East_1']);
        $Elev = $db->escape($_POST['Elev_1']);
        $Cviaje = $db->escape($_POST['Cviaje']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        $Comments = $db->escape($_POST['Comments_1']);
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];

        $TestType1 = isset($_POST['TestType_1']) ? implode(',', $_POST['TestType_1']) : '';


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
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType1}' ";
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