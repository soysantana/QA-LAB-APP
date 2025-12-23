<?php
// ================== CONFIGURACIÓN ==================
define('DB_HOST', 'localhost');
define('DB_USER', 'fmfwfvmy_pvj_lab_user_root');
define('DB_PASS', 'Dominican$8095');
define('DB_NAME', 'fmfwfvmy_pvj2_db');

// ================== CONEXIÓN ==================
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

// ================== ARCHIVO ==================
$filename = DB_NAME . '_FULL_' . date('Y-m-d_H-i-s') . '.sql';

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// ================== CABECERA ==================
echo "-- ==============================================\n";
echo "-- Backup COMPLETO de la base de datos\n";
echo "-- Base de datos: " . DB_NAME . "\n";
echo "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "-- ==============================================\n\n";

echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
echo "SET AUTOCOMMIT = 0;\n";
echo "START TRANSACTION;\n";
echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

// ================== DATABASE ==================
echo "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` ";
echo "DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
echo "USE `" . DB_NAME . "`;\n\n";

// ================== TABLAS ==================
$tables = [];
$result = $mysqli->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
      AND TABLE_TYPE = 'BASE TABLE'
");

while ($row = $result->fetch_assoc()) {
    $tables[] = $row['TABLE_NAME'];
}

foreach ($tables as $table) {

    echo "-- ----------------------------------------------\n";
    echo "-- Tabla `$table`\n";
    echo "-- ----------------------------------------------\n\n";

    $createTable = $mysqli
        ->query("SHOW CREATE TABLE `" . DB_NAME . "`.`$table`")
        ->fetch_assoc();

    echo "DROP TABLE IF EXISTS `$table`;\n";
    echo $createTable['Create Table'] . ";\n\n";

    $rows = $mysqli->query("SELECT * FROM `" . DB_NAME . "`.`$table`");
    while ($row = $rows->fetch_assoc()) {
        $values = array_map(function ($value) use ($mysqli) {
            return $value === null ? "NULL" : "'" . $mysqli->real_escape_string($value) . "'";
        }, array_values($row));

        echo "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
    }
    echo "\n";
}

// ================== TRIGGERS ==================
echo "-- ==============================================\n";
echo "-- TRIGGERS\n";
echo "-- ==============================================\n\n";

$triggers = $mysqli->query("
    SELECT TRIGGER_NAME 
    FROM INFORMATION_SCHEMA.TRIGGERS 
    WHERE TRIGGER_SCHEMA = '" . DB_NAME . "'
");

while ($trigger = $triggers->fetch_assoc()) {

    $triggerName = $trigger['TRIGGER_NAME'];

    $result = $mysqli->query("SHOW CREATE TRIGGER `" . DB_NAME . "`.`$triggerName`");
    $row = $result->fetch_assoc();

    echo "DROP TRIGGER IF EXISTS `$triggerName`;\n";
    echo "DELIMITER $$\n";
    echo $row['SQL Original Statement'] . " $$\n";
    echo "DELIMITER ;\n\n";
}

// ================== FINAL ==================
echo "COMMIT;\n";
echo "SET FOREIGN_KEY_CHECKS = 1;\n";

$mysqli->close();
exit;
