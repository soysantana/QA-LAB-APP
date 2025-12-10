<?php
// /api/sync_test_repeat_to_workflow.php
declare(strict_types=1);
require_once('../config/load.php');


@ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

function respond($ok, $msg){
    echo json_encode(['ok'=>$ok, 'msg'=>$msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// 1) Verificar si ya se ejecutó antes (flag en settings)
$flag = find_by_sql("SELECT value FROM system_settings WHERE name='repeat_sync_done' LIMIT 1");

if ($flag && $flag[0]['value'] == '1') {
    respond(true, "SYNC_ALREADY_DONE");
}

// 2) Buscar repeticiones
$rows = find_by_sql("
    SELECT *
    FROM test_repeat
");

if (!$rows || count($rows) == 0) {
    // marcar igual para no intentar de nuevo
    $db->query("INSERT INTO system_settings (name,value) VALUES ('repeat_sync_done','1')
                ON DUPLICATE KEY UPDATE value='1'");
    respond(true, "NO_REPEAT_DATA");
}

$countInserted = 0;

foreach ($rows as $r) {

    // ya existe en workflow?
    $exists = find_by_sql("
        SELECT id
        FROM test_workflow
        WHERE Sample_ID='{$db->escape($r['Sample_ID'])}'
          AND Sample_Number='{$db->escape($r['Sample_Number'])}'
          AND Test_Type='{$db->escape($r['Test_Type'])}'
        LIMIT 1
    ");

    if ($exists) continue;

    // insertar en workflow
    $id = bin2hex(random_bytes(16));

    $db->query("
        INSERT INTO test_workflow
        (id, Sample_ID, Sample_Number, Test_Type, Status, sub_stage, Process_Started, Updated_At, Updated_By)
        VALUES
        (
            '{$id}',
            '{$db->escape($r['Sample_ID'])}',
            '{$db->escape($r['Sample_Number'])}',
            '{$db->escape($r['Test_Type'])}',
            'Repetición',
            'RE1',
            NOW(),
            NOW(),
            '{$db->escape($r['Register_By'])}'
        )
    ");

    $countInserted++;
}

// 3) Marcar como completado
$db->query("
    INSERT INTO system_settings (name,value)
    VALUES ('repeat_sync_done','1')
    ON DUPLICATE KEY UPDATE value='1'
");

respond(true, "SYNC_OK: {$countInserted} inserted");
