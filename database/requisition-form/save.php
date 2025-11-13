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
        $ProjectName    = $db->escape($_POST['ProjectName']);
        $Client         = $db->escape($_POST['Client']);
        $ProjectNumber  = $db->escape($_POST['ProjectNumber']);
        $PackageID      = make_ticket_code();
        $Structure      = $db->escape($_POST['Structure']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $Cviaje         = $db->escape($_POST['Cviaje']);
        $SampleBy       = $db->escape($_POST['SampleBy']);
        $RegistedDate   = make_date();
        $RegisterBy     = $user['name'];

        $sampleData = [];

        // ===== 1) Armar arreglo por muestra: SampleName, SampleNumber, Area, etc. =====
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

        // ===== 2) TestType por muestra (lista separada por comas) =====
        foreach ($_POST as $key => $value) {
            if (preg_match('/^TestType_(\d+)$/', $key, $matches)) {
                $index = (int)$matches[1]; // número de muestra

                // Escapar valores
                $escapedTests = array_map(function ($test) use ($db) {
                    return $db->escape($test);
                }, (array)$value);

                // Guardar como string separado por coma
                $sampleData[$index]['TestType'] = implode(',', $escapedTests);
            }
        }

        // ===== 3) Insert por cada muestra =====
        foreach ($sampleData as $index => $data) {
            // Asegurar que haya claves aunque vengan vacías (evita "Undefined index")
            $data = array_merge([
                'SampleName'   => '',
                'SampleNumber' => '',
                'Area'         => '',
                'Source'       => '',
                'DepthFrom'    => '',
                'DepthTo'      => '',
                'MType'        => '',
                'SType'        => '',
                'North'        => '',
                'East'         => '',
                'Elev'         => '',
                'Comments'     => '',
                'TestType'     => ''
            ], $data);

            $id = uuid();

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
                '{$id}',
                '{$ProjectName}',
                '{$Client}',
                '{$ProjectNumber}',
                '{$PackageID}',
                '{$Structure}',
                '{$CollectionDate}',
                '{$Cviaje}',
                '{$SampleBy}',
                '{$RegistedDate}',
                '{$RegisterBy}',
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

            // Ejecuta insert en requisición por cada muestra
            $db->query($sql);

            // ===== 4) Insertar/actualizar en test_workflow una fila por cada ensayo de la muestra =====
            // Cada Test_Type viene como "SP,GS,HY", etc.
            $tests = array_filter(array_map('trim', explode(',', $data['TestType'])));

            foreach ($tests as $tRaw) {
                if ($tRaw === '') continue;

                $wfId = uuid();
                $t    = $db->escape($tRaw);

                // NOTA:
                // - La tabla test_workflow debe tener UNIQUE KEY uniq_test (Sample_ID, Sample_Number, Test_Type)
                // - ON DUPLICATE KEY evita el fatal error si ya existe esa combinación:
                //   solo actualiza Updated_By y Updated_At.
                $sqlWf = "INSERT INTO test_workflow (
                            id,
                            Sample_ID,
                            Sample_Number,
                            Test_Type,
                            Status,
                            Process_Started,
                            Updated_By,
                            Updated_At
                          ) VALUES (
                            '{$wfId}',
                            '{$data['SampleName']}',
                            '{$data['SampleNumber']}',
                            '{$t}',
                            'Registrado',
                            '{$RegistedDate}',
                            '{$RegisterBy}',
                            NOW()
                          )
                          ON DUPLICATE KEY UPDATE
                            Updated_By = VALUES(Updated_By),
                            Updated_At = VALUES(Updated_At)";

                $db->query($sqlWf);
            }
            // ===== FIN test_workflow =====
        }

        // ===== 5) Mensaje de éxito =====
        $session->msg('s', 'Formulario de requisición guardado correctamente.');
        redirect('/pages/requisition-form.php', false);
    } else {
        $session->msg('d', 'Lo siento, no se pudo agregar ninguna muestra.');
        redirect('/pages/requisition-form.php', false);
    }
}
?>
