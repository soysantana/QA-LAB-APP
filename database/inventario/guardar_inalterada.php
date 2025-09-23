<?php
require_once('../../config/load.php');
page_require_level(3);

// Helper: genera expresión SQL para DECIMAL que convierte '' -> NULL y coma -> punto
function dec_sql($db, $post_key) {
    $val = isset($_POST[$post_key]) ? $db->escape($_POST[$post_key]) : '';
    // NULLIF(...,'') hace NULL cuando venga vacío; REPLACE cambia coma por punto.
    return "NULLIF(REPLACE('{$val}', ',', '.'), '')";
}

// Recibir datos del formulario (texto)
$requisition_id = $db->escape($_POST['requisition_id'] ?? '');
$sample_id      = $db->escape($_POST['Sample_ID'] ?? '');
$sample_number  = $db->escape($_POST['Sample_Number'] ?? '');
$sample_type    = $db->escape($_POST['Sample_Type'] ?? '');
$store_in       = $db->escape($_POST['Store_In'] ?? '');
$comment        = $db->escape($_POST['Comment'] ?? '');

// Números (como expresiones SQL)
$depth_from_sql   = dec_sql($db, 'Depth_From');
$depth_to_sql     = dec_sql($db, 'Depth_To');
$sample_length_sql= dec_sql($db, 'Sample_Length');
$sample_weight_sql= dec_sql($db, 'Sample_Weight');

// Fecha: NULL si viene vacía. Ajusta el formato según tu input (aquí %Y-%m-%d)
$sample_date_raw = $_POST['Sample_Date'] ?? '';
if (trim($sample_date_raw) === '') {
    $sample_date_sql = "NULL";
} else {
    $sample_date_sql = "STR_TO_DATE('".$db->escape($sample_date_raw)."', '%Y-%m-%d')";
}

// Verificar si ya existe
$check = $db->fetch_assoc($db->query(
    "SELECT id FROM inalteratedsample WHERE requisition_id = '{$requisition_id}'"
));

if ($check) {
    // Actualizar
    $sql = "
        UPDATE inalteratedsample
           SET sample_length = {$sample_length_sql},
               sample_weight = {$sample_weight_sql},
               store_in      = '{$store_in}',
               comment       = '{$comment}',
               sample_date   = {$sample_date_sql},
               depth_from    = {$depth_from_sql},
               depth_to      = {$depth_to_sql}
         WHERE requisition_id = '{$requisition_id}'
    ";
} else {
    // Insertar
    $sql = "
        INSERT INTO inalteratedsample (
            requisition_id, sample_id, sample_number, sample_type,
            depth_from, depth_to, sample_length, sample_weight,
            store_in, comment, sample_date
        ) VALUES (
            '{$requisition_id}', '{$sample_id}', '{$sample_number}', '{$sample_type}',
            {$depth_from_sql}, {$depth_to_sql}, {$sample_length_sql}, {$sample_weight_sql},
            '{$store_in}', '{$comment}', {$sample_date_sql}
        )
    ";
}

if ($db->query($sql)) {
    $session->msg("s", "Información de muestra inalterada guardada correctamente.");
} else {
    $session->msg("d", "Error al guardar la muestra.");
}

redirect('../../pages/inventario_inalterada.php');
