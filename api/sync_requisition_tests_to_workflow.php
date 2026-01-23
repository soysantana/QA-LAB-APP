<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(3);
header('Content-Type: application/json; charset=utf-8');

$db = $GLOBALS['db'];

function norm_test(string $v): string {
    $v = str_replace("\xC2\xA0", " ", $v);
    $v = strtoupper(trim($v));
    $v = preg_replace('/\s+/', '', $v);
    $v = preg_replace('/[\-\_\.\/]+/', '', $v);
    return $v;
}

function split_tests(string $tt): array {
    $tt = str_replace("\xC2\xA0", " ", $tt);
    $tt = trim($tt);
    if ($tt === '') return [];

    $parts = preg_split('/[,\n;\r\/]+/', $tt);
    $out = [];

    foreach ($parts as $p) {
        $p = norm_test($p);
        if ($p !== '') $out[] = $p;
    }
    return array_values(array_unique($out));
}

try {

    // 1) Leer requisiciones
    $rows = find_by_sql("
        SELECT Sample_ID, Sample_Number, Test_Type
        FROM lab_test_requisition_form
        WHERE Sample_ID IS NOT NULL AND Sample_ID <> ''
    ");

    $inserted = 0;

    foreach ($rows as $r) {

        $sid = trim((string)$r['Sample_ID']);
        $sno = trim((string)$r['Sample_Number']);
        $raw = (string)$r['Test_Type'];

        if ($sid === '' || $raw === '') continue;

        $tests = split_tests($raw);
        if (!$tests) continue;

        foreach ($tests as $tt) {

            $sidE = $db->escape($sid);
            $snoE = $db->escape($sno);
            $ttE  = $db->escape($tt);

            // 2) Ver si ya existe en workflow
            $ex = find_by_sql("
                SELECT id FROM test_workflow
                WHERE Sample_ID='{$sidE}'
                  AND Sample_Number='{$snoE}'
                  AND Test_Type='{$ttE}'
                LIMIT 1
            ");
            if ($ex) continue;

            // 3) Insertar NUEVO ensayo en workflow
            $db->query("
                INSERT INTO test_workflow
                  (Sample_ID, Sample_Number, Test_Type, Status, sub_stage, Process_Started, Updated_At, Updated_By)
                VALUES
                  ('{$sidE}','{$snoE}','{$ttE}','Registrado','',NOW(),NOW(),'SYNC')
            ");
            $inserted++;
        }
    }

    echo json_encode(['ok'=>true,'inserted'=>$inserted]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
