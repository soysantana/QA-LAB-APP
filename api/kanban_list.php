<?php
// /api/kanban_list.php
declare(strict_types=1);

require_once('../config/load.php');

// Desactivar salida basura y forzar JSON
@ini_set('display_errors', '0');
@ob_clean();
header('Content-Type: application/json; charset=utf-8');

function json_error(string $msg, int $code = 200): never {
    http_response_code($code);
    echo json_encode([
        'ok'    => false,
        'error' => $msg,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_ok(array $data): never {
    echo json_encode([
        'ok' => true,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ⛔ IMPORTANTE: NADA DE HEADER/FOOTER AQUÍ
// SOLO JSON, NINGÚN HTML

/* =========================
   Seguridad básica
   ========================= */
global $session;
if (!$session->isUserLoggedIn(true)) {
    json_error('UNAUTHORIZED', 401);
}

/* =========================
   Helpers locales
   ========================= */
function N(string $v): string {
    return strtoupper(trim($v));
}

$SLA = [
    'Registrado'  => 24,
    'Preparación' => 48,
    'Realización' => 72,
    'Entrega'     => 24,
];

function sla_for_status(string $s): int {
    global $SLA;
    return (int)($SLA[$s] ?? 48);
}

/* =========================
   Leer filtros (q, test)
   ========================= */
$q    = isset($_GET['q'])    ? trim((string)$_GET['q'])    : '';
$test = isset($_GET['test']) ? trim((string)$_GET['test']) : '';

/* =========================
   Construir WHERE
   ========================= */
$where = [];
global $db;

if ($q !== '') {
    $like = '%' . $db->escape($q) . '%';
    $where[] = sprintf(
        "(w.Sample_ID LIKE '%s' OR w.Sample_Number LIKE '%s' OR w.Test_Type LIKE '%s')",
        $like, $like, $like
    );
}

if ($test !== '') {
    $tt = N($test);
    $where[] = sprintf(
        "UPPER(TRIM(w.Test_Type)) = '%s'",
        $db->escape($tt)
    );
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* =========================
   Consulta principal
   ========================= */

$sql = "
    SELECT
      w.id,
      w.Sample_ID,
      w.Sample_Number,
      w.Test_Type,
      w.Status,
      w.Process_Started,
      w.Updated_By,
      w.Updated_At,
      w.Sub_Stage,
      TIMESTAMPDIFF(HOUR, w.Process_Started, NOW()) AS Dwell_Hours
    FROM test_workflow AS w
    {$whereSql}
    ORDER BY w.Updated_At DESC
    LIMIT 500
";

$rows = find_by_sql($sql);

/* =========================
   Armar estructura para el Kanban
   ========================= */

// Estructura base para cada columna
$data = [
    'Registrado'  => [],
    'Preparación' => [],
    'Realización' => [],
    'Entrega'     => [],
];

foreach ($rows as $r) {
    $status = $r['Status'] ?? '';
    if (!isset($data[$status])) {
        // Si llega un estado raro, lo ignoramos
        continue;
    }

    $since  = $r['Process_Started'] ?? $r['Updated_At'] ?? '';
    $dwell  = (int)($r['Dwell_Hours'] ?? 0);
    $sla    = sla_for_status($status);
    $alert  = $dwell >= $sla;

    $data[$status][] = [
        'id'           => (string)($r['id'] ?? ''),
        'Sample_ID'    => (string)($r['Sample_ID'] ?? ''),
        'Sample_Number'=> (string)($r['Sample_Number'] ?? ''),
        'Test_Type'    => (string)($r['Test_Type'] ?? ''),
        'Status'       => (string)$status,
        'Since'        => (string)$since,
        'Updated_By'   => (string)($r['Updated_By'] ?? ''),
        'Dwell_Hours'  => $dwell,
        'SLA_Hours'    => $sla,
        'Alert'        => $alert,
        'Sub_Stage'    => (string)($r['Sub_Stage'] ?? ''),
    ];
}

/* =========================
   Responder JSON
   ========================= */
json_ok($data);
