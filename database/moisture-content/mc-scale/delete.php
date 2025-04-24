<?php
 if (isset($_POST['delete_mc_scale']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('moisture_scale', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>