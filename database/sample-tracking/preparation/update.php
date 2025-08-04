<?php
if (isset($_POST['update_multiple'])) {
    $technicians = $_POST['Technician'];  // ['uuid1' => 'nombre1', ...]
    $start_dates = $_POST['Start_Date'];  // ['uuid1' => '2024-08-05', ...]

    if (is_array($technicians)) {
        $errores = [];
        $actualizados = 0;

        foreach ($technicians as $id => $name) {
            $technicianName = $db->escape(trim($name));
            $uuid = $db->escape(trim($id));
            $startDate = isset($start_dates[$id]) ? $db->escape(trim($start_dates[$id])) : null;

            // Validación simple
            if (empty($technicianName)) {
                $errores[] = "El nombre del técnico para el ID $uuid está vacío.";
                continue;
            }

            if (empty($startDate)) {
                $errores[] = "La fecha de inicio para el ID $uuid está vacía.";
                continue;
            }

            $query = "UPDATE test_preparation 
                      SET Technician = '{$technicianName}', Start_Date = '{$startDate}' 
                      WHERE id = '{$uuid}'";
            $result = $db->query($query);

            if ($result && $db->affected_rows() >= 0) {
                $actualizados++;
            }
        }

        if ($actualizados > 0) {
            $session->msg('s', "$actualizados registro(s) actualizados con éxito.");
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
?>
