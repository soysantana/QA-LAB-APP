<?php
require_once('../config/load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sample_id = $_POST['sample_id'] ?? '';
    $sample_number = $_POST['sample_number'] ?? '';
    $dias = $_POST['dias'] ?? '';
    $estado = $_POST['estado'] ?? 'Realizado';
    $fecha = date('Y-m-d');

    if ($sample_id && in_array($dias, [3,7,14,28]) && in_array($estado, ['Realizado', 'No solicitado'])) {
        // Verificar si ya existe
        $query = "SELECT id FROM estado_ensayo_concreto WHERE sample_id = '{$sample_id}' AND dias = {$dias}";
        $result = $db->query($query);
        
        if ($result->num_rows == 0) {
            // Insertar nuevo
            $insert = "INSERT INTO estado_ensayo_concreto (sample_id, Sample_Number, dias, estado, fecha_registro)
                       VALUES ('{$sample_id}', '{$sample_number}', {$dias}, '{$estado}', '{$fecha}')";
            $db->query($insert);
        }
    }
}

header('Location: ../pages/control_ensayo_concreto.php');
exit;
