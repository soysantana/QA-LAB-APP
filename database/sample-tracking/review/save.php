<?php

function insertDataFromTable($tableName, $db, $session)
{
    $typeMappings = [
        'AL' => 'AL',
        'BTS' => 'BTS',
        'GS' => ['GS-Coarse', 'GS-Fine', 'GS-CoarseThan', 'GS_CF', 'GS_FF', 'GS_LPF', 'GS_UTF'],
        'LAA' => ['LAA_Coarse_Filter', 'LAA_Coarse_Aggregate'],
        'MC' => ['MC_Oven', 'MC_Constant_Mass', 'MC_Microwave', 'MC_Scale'],
        'PH' => 'PH',
        'PLT' => 'PLT',
        'SND' => 'SND',
        'SG' => ['SG-Coarse', 'SG-Fine'],
        'SP' => 'SP',
        'UCS' => 'UCS',
    ];

    // Aplanar el array de mapeos
    foreach ($typeMappings as $key => $values) {
        if (is_array($values)) {
            foreach ($values as $value) {
                $typeMappings[$value] = $key;
            }
            unset($typeMappings[$key]);
        } else {
            $typeMappings[$values] = $key;
        }
    }

    $query = "SELECT Sample_ID, Sample_Number, Register_By, Test_Type, Registed_Date, id FROM $tableName";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = uuid();
            $mappedType = $typeMappings[$row["Test_Type"]] ?? $row["Test_Type"];
            $tracking = $row["id"];

            // Comprueba si existe una entrada con los mismos datos
            $checkQuery = "SELECT * FROM test_review 
            WHERE Tracking = '$tracking' AND Test_Type = '$mappedType'";
            $existingData = $db->query($checkQuery)->fetch_assoc();

            if ($existingData) {
                // Si hay cambios, actualiza
                if ($existingData['Sample_ID'] != $row["Sample_ID"] || 
                    $existingData['Sample_Number'] != $row["Sample_Number"] || 
                    $existingData['Start_Date'] != $row["Registed_Date"] || 
                    $existingData['Register_By'] != $row["Register_By"] || 
                    $existingData['Status'] != 'Review') {
                    $updateQuery = "UPDATE test_review 
                    SET Sample_ID = '{$row["Sample_ID"]}', Sample_Number = '{$row["Sample_Number"]}', 
                    Test_Type = '$mappedType', Start_Date = '{$row["Registed_Date"]}', 
                    Register_By = '{$row["Register_By"]}', Status = 'Review' 
                    WHERE Tracking = '$tracking'";
                    $session->msg($db->query($updateQuery) ? "s" : "d", $db->error ?: "Actualizado exitosamente");
                } else {
                    $session->msg("w", "No hay cambios para actualizar.");
                }
            } else {
                // Inserta un nuevo registro
                $insertQuery = "INSERT INTO test_review (id, Sample_ID, Sample_Number, Register_By, Test_Type, Start_Date, Status, Tracking)
                                VALUES ('$id', '{$row["Sample_ID"]}', '{$row["Sample_Number"]}', '{$row["Register_By"]}', 
                                '$mappedType', '{$row["Registed_Date"]}', 'Review', '$tracking')";
                $session->msg($db->query($insertQuery) ? "s" : "d", $db->error ?: "Insertado exitosamente");
            }
        }
    }
}

$db->db_connect();

if (isset($_POST["test-review"])) {
    $session = new Session();
    $tables = [
        "atterberg_limit", "brazilian", "grain_size_coarse", "grain_size_coarsethan", "grain_size_fine",
        "grain_size_general", "grain_size_coarse_filter", "grain_size_lpf", "grain_size_upstream_transition_fill",
        "los_angeles_abrasion_coarse_aggregate", "los_angeles_abrasion_coarse_filter", "moisture_constant_mass",
        "moisture_microwave", "moisture_oven", "moisture_scale", "pinhole_test", "point_load", "soundness",
        "specific_gravity", "specific_gravity_coarse", "Specific_gravity_fine", "standard_proctor", "unixial_compressive"
    ];

    foreach ($tables as $table) {
        insertDataFromTable($table, $db, $session);
    }

    redirect("/pages/test-review.php", false);
}

$db->db_disconnect();
?>
