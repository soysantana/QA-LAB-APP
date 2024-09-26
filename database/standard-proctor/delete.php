<?php
 if (isset($_POST['delete_sp']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('standard_proctor', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>
