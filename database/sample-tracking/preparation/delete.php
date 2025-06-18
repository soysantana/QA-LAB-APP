<?php
if (isset($_POST['delete_multiple']) && !empty($_POST['selected_samples'])) {
    $selected_ids = $_POST['selected_samples'];

    foreach ($selected_ids as $id) {
        $escaped_id = $db->escape($id);
        delete_by_id('test_preparation', $escaped_id);
    }

    $session->msg("s", "Registros eliminados correctamente.");
    redirect('/pages/test-preparation.php');
}
