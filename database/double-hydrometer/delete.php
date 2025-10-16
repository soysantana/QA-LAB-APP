<?php
if (isset($_POST['Delete']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('double_hydrometer', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
