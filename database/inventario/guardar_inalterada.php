<?php
require_once('../../config/load.php');
page_require_level(3);

// Recibir datos del formulario
$requisition_id = $db->escape($_POST['requisition_id']);
$sample_id = $db->escape($_POST['Sample_ID']);
$sample_number = $db->escape($_POST['Sample_Number']);
$sample_type = $db->escape($_POST['Sample_Type']);
$depth_from = $db->escape($_POST['Depth_From']);
$depth_to = $db->escape($_POST['Depth_To']);
$sample_length = $db->escape($_POST['Sample_Length']);
$sample_weight = $db->escape($_POST['Sample_Weight']);
$store_in = $db->escape($_POST['Store_In']);
$comment = $db->escape($_POST['Comment']);
$sample_date = $db->escape($_POST['Sample_Date']);

// Verificar si ya existe
$check = $db->fetch_assoc($db->query("SELECT id FROM inalteratedsample WHERE requisition_id = '{$requisition_id}'"));

if ($check) {
    // Actualizar
    $sql = "UPDATE inalteratedsample SET 
                sample_length = '{$sample_length}',
                sample_weight = '{$sample_weight}',
                store_in = '{$store_in}',
                comment = '{$comment}',
                sample_date = '{$sample_date}'
            WHERE requisition_id = '{$requisition_id}'";
} else {
    // Insertar
    $sql = "INSERT INTO inalteratedsample (
                requisition_id, sample_id, sample_number, sample_type,
                depth_from, depth_to, sample_length, sample_weight,
                store_in, comment, sample_date
            ) VALUES (
                '{$requisition_id}', '{$sample_id}', '{$sample_number}', '{$sample_type}',
                '{$depth_from}', '{$depth_to}', '{$sample_length}', '{$sample_weight}',
                '{$store_in}', '{$comment}', '{$sample_date}'
            )";
}

if ($db->query($sql)) {
    $session->msg("s", "Información de muestra inalterada guardada correctamente.");
} else {
    $session->msg("d", "Error al guardar la muestra.");
}

redirect('../../pages/inventario_inalterada.php');
