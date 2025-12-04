<?php
@ob_clean();
header('Content-Type: application/json');

require_once('../config/load.php'); 

try {

    // Leer JSON
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "No JSON received."]);
        exit;
    }

    // Campos requeridos
    $required = [
        "Sample_ID",
        "Sample_Number",
        "Structure",
        "Area",
        "Source",
        "Material_Type",
        "Test_Type",
        "Test_Condition",
        "Noconformidad",
        "Report_Date"
    ];

    foreach ($required as $r) {
        if (!isset($input[$r])) {
            echo json_encode(["status" => "error", "message" => "Missing field: $r"]);
            exit;
        }
    }

    // Escapar todo
    $Sample_ID      = $db->escape($input["Sample_ID"]);
    $Sample_Number  = $db->escape($input["Sample_Number"]);
    $Structure      = $db->escape($input["Structure"]);
    $Area           = $db->escape($input["Area"]);
    $Source         = $db->escape($input["Source"]);
    $Material_Type  = $db->escape($input["Material_Type"]);
    $Test_Type      = $db->escape($input["Test_Type"]);
    $Test_Condition = $db->escape($input["Test_Condition"]);
    $Noconformidad  = $db->escape($input["Noconformidad"]);
    $Report_Date    = $db->escape($input["Report_Date"]);

    // Usuario actual
    $user = current_user();
    $UserName = $db->escape($user["name"]);

    // ➤ NUEVO: Comentario generado automáticamente
    $Comments = "At {$Area} - From {$Source}";
    $Comments = $db->escape($Comments);

    // SQL INSERT corregido + Comments añadido
    $sql = "
        INSERT INTO ensayos_reporte (
            Sample_ID,
            Sample_Number,
            Structure,
            Material_Type,
            Test_Type,
            Test_Condition,
            Comments,
            Report_Date,
            Noconformidad
        )
        VALUES (
            '{$Sample_ID}',
            '{$Sample_Number}',
            '{$Structure}',
            '{$Material_Type}',
            '{$Test_Type}',
            '{$Test_Condition}',
            '{$Comments}',
            '{$Report_Date}',
            '{$Noconformidad}'
        )
    ";

    if ($db->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Review inserted successfully"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "DB Insert Error"]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}
