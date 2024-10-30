<?php

page_require_level(2);

 if (isset($_POST['delete-delivery']) && isset($_GET['id'])) {
    $delete = $_GET['id'];

    $ID = delete_by_id('test_delivery', $delete);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    redirect('/pages/test-delivery.php');
 }
?>