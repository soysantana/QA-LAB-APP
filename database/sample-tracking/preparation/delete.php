<?php
 if (isset($_POST['delete-preparation']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('test_preparation', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/test-preparation.php');
 }
?>