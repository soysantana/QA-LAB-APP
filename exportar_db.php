<?php
// ================== CONFIGURACIÓN ==================
define('DB_HOST', 'localhost');
define('DB_USER', 'fmfwfvmy_pvj_lab_user_root');
define('DB_PASS', 'Dominican$8095');
define('DB_NAME', 'fmfwfvmy_pvj2_db');

// ================== CONEXIÓN ==================
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

// Nombre del archivo
$filename = DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';

// ================== CABECERAS DE DESCARGA ==================
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// ================== EXPORTACIÓN ==================
echo "-- Exportación de la base de datos `" . DB_NAME . "`\n";
echo "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Obtener tablas
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Recorrer tablas
foreach ($tables as $table) {
    echo "\n-- ----------------------------\n";
    echo "-- Tabla: `$table`\n";
    echo "-- ----------------------------\n\n";

    // Crear tabla
    $createTable = $mysqli->query("SHOW CREATE TABLE `$table`")->fetch_assoc();
    echo "DROP TABLE IF EXISTS `$table`;\n";
    echo $createTable['Create Table'] . ";\n\n";

    // Datos
    $rows = $mysqli->query("SELECT * FROM `$table`");
    while ($row = $rows->fetch_assoc()) {
        $values = array_map(function ($value) use ($mysqli) {
            return $value === null ? "NULL" : "'" . $mysqli->real_escape_string($value) . "'";
        }, array_values($row));

        echo "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
    }
}

echo "\nSET FOREIGN_KEY_CHECKS=1;\n";

$mysqli->close();
exit;
