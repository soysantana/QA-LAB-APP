<?php
 if (isset($_POST['delete_user']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('users', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/users-group.php');
 }
?>