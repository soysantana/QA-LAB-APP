<?php
require_once('../../config/load.php');
page_require_level(2);

if (isset($_POST['package_id'])) {
    $packageId = $db->escape($_POST['package_id']);
    $sampleName = $db->escape($_POST['sample_name']);

    // DELETE por Package_ID
    $query = "DELETE FROM lab_test_requisition_form WHERE Package_ID = '{$packageId}'";
    $result = $db->query($query); // usar query directo, no find_by_sql

    if ($result && $db->affected_rows() > 0) {
        $session->msg("s", "Se borraron {$db->affected_rows()} registros del paquete #{$sampleName}.");
    } else {
        $session->msg("d", "No se encontró el paquete #{$sampleName}.");
    }

    redirect('/pages/requisition-form-view.php');
}
