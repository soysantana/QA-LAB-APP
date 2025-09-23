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
        $PackageID = make_ticket_code();
        $Structure = $db->escape($_POST['Structure']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $Cviaje = $db->escape($_POST['Cviaje']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        $RegistedDate = make_date();
        $RegisterBy = $user['name'];

        $sampleData = [];

        foreach ($_POST as $key => $value) {
            if (preg_match('/^(SampleName|SampleNumber|Area|Source|DepthFrom|DepthTo|MType|SType|North|East|Elev|Comments)_(\d+)$/', $key, $matches)) {
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
        Sample_Date,
        Truck_Count,
        Sample_By,
        Registed_Date,
        Register_By,
        Sample_ID,
        Sample_Number,
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
        Test_Type
    ) VALUES (
        '$id',
        '$ProjectName',
        '$Client',
        '$ProjectNumber',
        '$PackageID',
        '$Structure',
        '$CollectionDate',
        '$Cviaje',
        '$SampleBy',
        '$RegistedDate',
        '$RegisterBy',
        '{$data['SampleName']}',
        '{$data['SampleNumber']}',
        '{$data['Area']}',
        '{$data['Source']}',
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

            // Aquí se ejecuta el insert por cada muestra
            $db->query($sql);
        }

        // Mensaje de éxito después de insertar todo
        $session->msg('s', 'Formulario de requisición guardado correctamente.');
        redirect('/pages/requisition-form.php', false);
    } else {
        $session->msg('d', 'Lo siento, no se pudo agregar ninguna muestra.');
        redirect('/pages/requisition-form.php', false);
    }
}
?>