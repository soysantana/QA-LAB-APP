<?php
require_once "../config/load.php";

$accion = $_GET["accion"] ?? "";

/* ================================
    1) OBTENER CLIENTES
================================ */
if ($accion === "clientes") {
    $sql = "SELECT DISTINCT Client FROM lab_test_requisition_form ORDER BY Client ASC";
    echo json_encode(find_by_sql($sql));
    exit;
}

/* ================================
    2) OBTENER ESTRUCTURAS POR CLIENTE
================================ */
if ($accion === "estructuras") {
    $cliente = $db->escape($_GET["cliente"]);
    $sql = "
        SELECT DISTINCT Structure 
        FROM lab_test_requisition_form 
        WHERE Client = '{$cliente}'
        AND Structure IS NOT NULL
        ORDER BY Structure ASC
    ";
    echo json_encode(find_by_sql($sql));
    exit;
}

/* ================================
    3) OBTENER MUESTRAS FILTRADAS
================================ */
if ($accion === "muestras") {

    $cliente = $db->escape($_GET["cliente"]);
    $estructura = $db->escape($_GET["estructura"]);
    $q = $db->escape($_GET["q"] ?? "");

    $sql = "
        SELECT 
            Sample_ID,
            Sample_Number,
            Material_Type
        FROM lab_test_requisition_form
        WHERE Client = '{$cliente}'
        AND Structure = '{$estructura}'
        AND Sample_ID LIKE '%{$q}%'
        ORDER BY Sample_ID ASC
        LIMIT 50
    ";

    echo json_encode(find_by_sql($sql));
    exit;
}

echo json_encode(["error" => "Acción inválida"]);
