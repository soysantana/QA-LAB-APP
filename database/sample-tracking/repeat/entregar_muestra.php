<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send-delivery'])) {
    $nombre     = $db->escape($_POST['Sample_Name']);
    $numero     = $db->escape($_POST['Sample_Number']);
    $tipo       = $db->escape($_POST['Test_Type']);
    $registrado = $db->escape($_POST['Register_By']);
    $fecha_ini  = $db->escape($_POST['Start_Date']);

    $uuid = uniqid('td_', true);
    $fecha_registro = date('Y-m-d H:i:s');
    $estado = 'Delivery';

    // Verificar si ya existe una entrega con esa muestra
    $check_query = "SELECT id FROM test_delivery 
                    WHERE Sample_Name = '{$nombre}' 
                      AND Sample_Number = '{$numero}' 
                      AND Test_Type = '{$tipo}' 
                    LIMIT 1";

    $check_result = $db->query($check_query);

    if ($db->num_rows($check_result) > 0) {
        // Ya existe → Hacer UPDATE
        $update_query = "UPDATE test_delivery SET
                            Technician = '{$registrado}',
                            Start_Date = '{$fecha_ini}',
                            Register_By = '{$registrado}',
                            Register_Date = '{$fecha_registro}',
                            Status = '{$estado}'
                         WHERE Sample_Name = '{$nombre}' 
                           AND Sample_Number = '{$numero}' 
                           AND Test_Type = '{$tipo}'";

        $exec = $db->query($update_query);
    } else {
        // No existe → Hacer INSERT
        $insert_query = "INSERT INTO test_delivery (
                            id, Sample_Name, Sample_Number, Test_Type, Technician,
                            Start_Date, Register_By, Register_Date, Status
                         ) VALUES (
                            '{$uuid}', '{$nombre}', '{$numero}', '{$tipo}', '{$registrado}',
                            '{$fecha_ini}', '{$registrado}', '{$fecha_registro}', '{$estado}'
                         )";
        $exec = $db->query($insert_query);
    }

    if ($exec) {
        // Eliminar de test_repeat
        $delete_query = "DELETE FROM test_repeat 
                         WHERE Sample_Name = '{$nombre}' 
                           AND Sample_Number = '{$numero}' 
                           AND Test_Type = '{$tipo}'";
        $delete_result = $db->query($delete_query);

        if ($delete_result) {
            $session->msg("s", "✅ Muestra entregada y sincronizada correctamente.");
           redirect('/pages/test-repeat.php', false);

            exit;
        } else {
            $session->msg("w", "⚠️ Actualizado, pero no eliminado de test_repeat.");
            redirect('/pages/test-repeat.php', false);

            exit;
        }
    } else {
        $session->msg("d", "❌ Error al insertar o actualizar la entrega.");
        redirect('/pages/test-repeat.php', false);

        exit;
    }
} else {
    $session->msg("d", "❌ Solicitud inválida.");
  redirect('/pages/test-repeat.php', false);

    exit;
}

