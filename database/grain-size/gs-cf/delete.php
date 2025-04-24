<?php
 if (isset($_POST['delete_gs_coarse']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('grain_size_coarse', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
 }
?>
