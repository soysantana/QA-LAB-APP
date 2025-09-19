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
$sampleId = $_GET['sample_id'] ?? '';

if (isset($_POST['update-requisition'])) {

    if (empty($errors)) {
        $ProjectName    = $db->escape($_POST['ProjectName']);
        $Client         = $db->escape($_POST['Client']);
        $ProjectNumber  = $db->escape($_POST['ProjectNumber']);
        $PackageID      = $db->escape($_POST['PackageID']);
        $Structure      = $db->escape($_POST['Structure']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $Cviaje         = $db->escape($_POST['Cviaje']);
        $SampleBy       = $db->escape($_POST['SampleBy']);
        $ModifiedDate   = make_date();
        $ModifiedBy     = $user['name'];

        $totalAffected = 0;

        // Actualizar datos generales
        $generalUpdate = "
            UPDATE lab_test_requisition_form SET
                Project_Name   = '{$ProjectName}',
                Client         = '{$Client}',
                Project_Number = '{$ProjectNumber}',
                Package_ID     = '{$PackageID}',
                Structure      = '{$Structure}',
                Sample_Date    = '{$CollectionDate}',
                Truck_Count    = '{$Cviaje}',
                Sample_By      = '{$SampleBy}',
                Modified_Date  = '{$ModifiedDate}',
                Modified_By    = '{$ModifiedBy}'
            WHERE Sample_ID = '{$sampleId}'
        ";
        $resultGeneral = $db->query($generalUpdate);
        $totalAffected += $db->affected_rows();

        // Recorrer y actualizar las muestras
        $i = 0;
        while (isset($_POST["SampleNumber_{$i}"])) {
            $SampleNumber = $db->escape($_POST["SampleNumber_{$i}"]);
            $Area         = $db->escape($_POST["Area_{$i}"] ?? '');
            $Source       = $db->escape($_POST["Source_{$i}"] ?? '');
            $DepthFrom    = $db->escape($_POST["DepthFrom_{$i}"] ?? '');
            $DepthTo      = $db->escape($_POST["DepthTo_{$i}"] ?? '');
            $Comments     = $db->escape($_POST["Comments_{$i}"] ?? '');
            $MType        = $db->escape($_POST["MType_{$i}"] ?? '');
            $SType        = $db->escape($_POST["SType_{$i}"] ?? '');
            $North        = $db->escape($_POST["North_{$i}"] ?? '');
            $East         = $db->escape($_POST["East_{$i}"] ?? '');
            $Elev         = $db->escape($_POST["Elev_{$i}"] ?? '');
            $TestTypeArr  = $_POST["TestType_{$i}"] ?? [];
            $TestType     = implode(',', $TestTypeArr);

            $query = "
                UPDATE lab_test_requisition_form SET
                    Area          = '{$Area}',
                    Source        = '{$Source}',
                    Depth_From    = '{$DepthFrom}',
                    Depth_To      = '{$DepthTo}',
                    Material_Type = '{$MType}',
                    Sample_Type   = '{$SType}',
                    North         = '{$North}',
                    East          = '{$East}',
                    Elev          = '{$Elev}',
                    Comment       = '{$Comments}',
                    Test_Type     = '{$TestType}',
                    Modified_Date = '{$ModifiedDate}',
                    Modified_By   = '{$ModifiedBy}'
                WHERE Sample_ID = '{$sampleId}' AND Sample_Number = '{$SampleNumber}'
            ";
            $resultSample = $db->query($query);
            $totalAffected += $db->affected_rows();

            $i++;
        }

        // Revisar si hubo cambios
        if ($totalAffected > 0) {
            $session->msg('s', 'El paquete ha sido actualizado.');
        } else {
            $session->msg('w', 'No se realizaron cambios en el paquete.');
        }

        redirect('/pages/requisition-form-view.php', false);
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