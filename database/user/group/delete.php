<?php
 if (isset($_POST['delete_group']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('user_groups', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/views-group.php');
 }
?>