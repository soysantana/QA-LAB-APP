


<!-- Review -->
<?php

 function insertDataFromTable($tableName, $db, $session)
 {
    $typeMappings = [
        'AL' => 'AL',
        'BTS' => 'BTS',
        'GS' => [
            'GS-Coarse',
            'GS-Fine',
            'GS-CoarseThan',
            'GS_CF',
            'GS_FF',
            'GS_LPF',
            'GS_UTF'
        ],
        'LAA' => [
            'LAA_Coarse_Filter',
            'LAA_Coarse_Aggregate'
        ],
        'MC' => [
            'MC_Oven',
            'MC_Constant_Mass',
            'MC_Microwave',
            'MC_Scale'
        ],
        'PH' => 'PH',
        'PLT' => 'PLT',
        'SND' => 'SND',
        'SG' => [
            'SG-Coarse',
            'SG-Fine'
        ],
        'SP' => 'SP',
        'UCS' => 'UCS',
    ];
    
    // Aplanar el array para los mapeos
    foreach ($typeMappings as $key => $values) {
        if (is_array($values)) {
            foreach ($values as $value) {
                $typeMappings[$value] = $key;
            }
            unset($typeMappings[$key]); // Elimina la entrada original
        } else {
            $typeMappings[$values] = $key; // Mapea directamente
        }
    }
    

    $query = "SELECT Sample_ID, Sample_Number, Register_By, Test_Type, Registed_Date, id FROM $tableName";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = uuid();
            $Sname = $row["Sample_ID"];
            $Snumber = $row["Sample_Number"];
            $Reby = $row["Register_By"];
            $Tstype = $row["Test_Type"];
            $Redate = make_date();
            $Tracking = $row["id"];

            // Utiliza el mapeo para reducir letras en Test_Type
            $mappedType = isset($typeMappings[$Tstype]) ? $typeMappings[$Tstype] : $Tstype;

            $checkQuery = "SELECT * FROM test_review 
            WHERE Tracking = '$Tracking'
            AND Test_Type = '$mappedType'";

            $checkResult = $db->query($checkQuery);

            if ($checkResult && $checkResult->num_rows > 0) {
                // Comparar los datos y actualizar si es necesario
                $existingData = $checkResult->fetch_assoc();
                if ($existingData['Sample_Name'] != $Sname || $existingData['Sample_Number'] != $Snumber || 
                $existingData['Start_Date'] != $Redate || $existingData['Register_By'] != $Reby || $existingData['Status'] != 'Review') {
                    // Hay cambios, actualiza la entrada
                    $updateQuery = "UPDATE test_review 
                    SET Sample_Name = '$Sname', Sample_Number = '$Snumber', Test_Type = '$mappedType', Start_Date = '$Redate', Register_By = '$Reby', Status = 'Review' 
                    WHERE Tracking = '$Tracking'";

                    if ($db->query($updateQuery) === true) {
                        $session->msg("s", "Actualizado exitosamente");
                    } else {
                        $session->msg("d", "Error al actualizar." . $db->error);
                    }
                } else {
                    $session->msg("w", "No hay cambios para actualizar.");
                }
            } else {
                // No existe la entrada, insertar una nueva
                $insertQuery = "INSERT INTO test_review (id, Sample_Name, Sample_Number, Register_By, Test_Type, Start_Date, Status, Tracking)
                                VALUES ('$id', '$Sname', '$Snumber', '$Reby', '$mappedType', '$Redate', 'Review', '$Tracking')";

                if ($db->query($insertQuery) === true) {
                    $session->msg("s", "Insertado exitosamente");
                } else {
                    $session->msg("d", "Error al insertar." . $db->error);
                }
            }
        }
    } else {
    }
 }

 $db->db_connect();

 if (isset($_POST["test-review"])) {
    $session = new Session();
    $tables = [
        "atterberg_limit",
        "brazilian",
        "grain_size_coarse",
        "grain_size_coarsethan",
        "grain_size_fine",
        "grain_size_general",
        "grain_size_coarse_filter",
        "grain_size_fine_filter",
        "grain_size_lpf",
        "grain_size_upstream_transition_fill",
        "los_angeles_abrasion_coarse_aggregate",
        "los_angeles_abrasion_coarse_filter",
        "moisture_constant_mass",
        "moisture_microwave",
        "moisture_oven",
        "moisture_scale",
        "pinhole_test",
        "point_load",
        "soundness",
        "specific_gravity",
        "specific_gravity_coarse",
        "Specific_gravity_fine",
        "standard_proctor",
        "unixial_compressive",
    ];

    foreach ($tables as $table) {
        insertDataFromTable($table, $db, $session);
    }

    redirect("/pages/test-review.php", false);
 }

 $db->db_disconnect();
?>