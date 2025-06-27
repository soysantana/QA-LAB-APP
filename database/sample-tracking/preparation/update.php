<?php
if (isset($_POST['update_multiple'])) {
    $technicians = $_POST['Technician']; // Ej: ['uuid1' => 'nombre1', 'uuid2' => 'nombre2', ...]

    if (is_array($technicians)) {
        $errores = [];
        $actualizados = 0;

        foreach ($technicians as $id => $name) {
            $technicianName = $db->escape(trim($name));
            $uuid = $db->escape(trim($id)); // Importante: escapamos el UUID

            // Validación simple
            if (empty($technicianName)) {
                $errores[] = "El nombre del técnico para el ID $uuid está vacío.";
                continue;
            }

            $query = "UPDATE test_preparation SET Technician = '{$technicianName}' WHERE id = '{$uuid}'";
            $result = $db->query($query);

            if ($result && $db->affected_rows() === 1) {
                $actualizados++;
            }
        }

        if ($actualizados > 0) {
            $session->msg('s', "$actualizados técnico actualizado con éxito.");
        } else {
            $session->msg('w', 'No se realizaron cambios.');
        }

        if (!empty($errores)) {
            $session->msg('d', implode("<br>", $errores));
        }

        redirect('/pages/test-preparation.php', false);
    } else {
        $session->msg("d", "No se recibieron datos válidos.");
        redirect('/pages/test-preparation.php', false);
    }
}
