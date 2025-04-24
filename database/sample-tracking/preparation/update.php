<?php
 $Search = $_POST['technicianId'];
 if (isset($_POST['update_technician'])) {
    $req_fields = array(
        'technicianName',
    );
    validate_fields($req_fields);

    if (empty($errors)) {
        $technicianName = $db->escape($_POST['technicianName']);

        $query = "UPDATE test_preparation SET ";
        $query .= "Technician = '{$technicianName}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Tecnico actualizado con exito');
            redirect('/pages/test-preparation.php', false);
        } else {
            $session->msg('w', 'Hubo un error, no se realizaron cambios');
            redirect('/pages/test-preparation.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/test-preparation.php', false);
    }
 }
?>