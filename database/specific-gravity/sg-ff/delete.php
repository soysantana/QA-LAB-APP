<?php
if (isset($_POST['delete_sg_fine']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('specific_gravity_fine', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/essay.php');
}
