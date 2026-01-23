<?php
require_once __DIR__ . "/../config/load.php";
page_require_level(2);

$user = current_user();
global $db, $session;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('../pages/auditorias_list.php', false);
  exit;
}

/* =========================================================
   HELPERS (evitar "Cannot redeclare")
========================================================= */
if (!function_exists('AUD_P')) {
  function AUD_P(string $key, $default='') {
    return $_POST[$key] ?? $default;
  }
}
if (!function_exists('AUD_PS')) {
  function AUD_PS(string $key, $default=''): string {
    // remove_junk() viene del core (load.php)
    return remove_junk(trim((string)AUD_P($key, $default)));
  }
}
if (!function_exists('AUD_PI')) {
  function AUD_PI(string $key, int $default=0): int {
    return (int)AUD_P($key, $default);
  }
}
if (!function_exists('AUD_FAIL')) {
  function AUD_FAIL(string $msg, string $redirectTo): void {
    global $session;
    $session->msg('d', $msg);
    redirect($redirectTo, false);
    exit;
  }
}

/* =========================================================
   DATOS AUDITORIA (HEADER)
========================================================= */
$id                = AUD_PI('id', 0);
$Audit_Code        = AUD_PS('Audit_Code');
$Audit_Date        = AUD_PS('Audit_Date');
$Audit_Type        = AUD_PS('Audit_Type');
$Area              = AUD_PS('Area');
$Scope             = AUD_PS('Scope');
$Findings          = AUD_PS('Findings');
$Severity          = AUD_PS('Severity', 'Minor');
$Status            = AUD_PS('Status', 'Open');
$Auditor           = AUD_PS('Auditor');
$Audited           = AUD_PS('Audited');
$Related_Sample_ID = AUD_PS('Related_Sample_ID');
$Related_Client    = AUD_PS('Related_Client');

if ($Audit_Code === '' || $Audit_Date === '' || $Audit_Type === '' || $Area === '' || $Auditor === '') {
  AUD_FAIL(
    'Faltan campos requeridos en la auditoría (Código, Fecha, Tipo, Área, Auditor).',
    '../pages/auditorias_form.php' . ($id ? '?id=' . $id : '')
  );
}

$created_by = $user['name'] ?? 'system';

/* =========================================================
   GUARDAR AUDITORIA
========================================================= */
if ($id > 0) {

  $sql = "UPDATE auditorias_lab SET
            Audit_Code='{$db->escape($Audit_Code)}',
            Audit_Date='{$db->escape($Audit_Date)}',
            Audit_Type='{$db->escape($Audit_Type)}',
            Area='{$db->escape($Area)}',
            Scope=" . ($Scope !== '' ? "'{$db->escape($Scope)}'" : "NULL") . ",
            Findings='{$db->escape($Findings)}',
            Severity=" . ($Severity !== '' ? "'{$db->escape($Severity)}'" : "NULL") . ",
            Status=" . ($Status !== '' ? "'{$db->escape($Status)}'" : "NULL") . ",
            Auditor='{$db->escape($Auditor)}',
            Audited=" . ($Audited !== '' ? "'{$db->escape($Audited)}'" : "NULL") . ",
            Related_Sample_ID=" . ($Related_Sample_ID !== '' ? "'{$db->escape($Related_Sample_ID)}'" : "NULL") . ",
            Related_Client=" . ($Related_Client !== '' ? "'{$db->escape($Related_Client)}'" : "NULL") . ",
            Updated_At=NOW()
          WHERE id={$id}
          LIMIT 1";

  if (!$db->query($sql)) {
    // Si tu clase $db tiene método de error, úsalo. Si no, deja el mensaje simple.
    AUD_FAIL('Error actualizando la auditoría.', '../pages/auditorias_form.php?id=' . $id);
  }

} else {

  $sql = "INSERT INTO auditorias_lab
          (Audit_Code, Audit_Date, Audit_Type, Area, Scope, Findings, Severity, Status, Auditor, Audited, Related_Sample_ID, Related_Client, Created_By, Created_At, Updated_At)
          VALUES
          (
            '{$db->escape($Audit_Code)}',
            '{$db->escape($Audit_Date)}',
            '{$db->escape($Audit_Type)}',
            '{$db->escape($Area)}',
            " . ($Scope !== '' ? "'{$db->escape($Scope)}'" : "NULL") . ",
            '{$db->escape($Findings)}',
            " . ($Severity !== '' ? "'{$db->escape($Severity)}'" : "NULL") . ",
            " . ($Status !== '' ? "'{$db->escape($Status)}'" : "NULL") . ",
            '{$db->escape($Auditor)}',
            " . ($Audited !== '' ? "'{$db->escape($Audited)}'" : "NULL") . ",
            " . ($Related_Sample_ID !== '' ? "'{$db->escape($Related_Sample_ID)}'" : "NULL") . ",
            " . ($Related_Client !== '' ? "'{$db->escape($Related_Client)}'" : "NULL") . ",
            '{$db->escape($created_by)}',
            NOW(),
            NOW()
          )";

  if (!$db->query($sql)) {
    AUD_FAIL('No se pudo crear la auditoría.', '../pages/auditorias_form.php');
  }

  $id = (int)$db->insert_id();
  if ($id <= 0) {
    AUD_FAIL('No se pudo obtener el ID insertado.', '../pages/auditorias_form.php');
  }
}

/* =========================================================
   HALLAZGOS (ARRAYS)
========================================================= */
$finding_ids   = $_POST['finding_id'] ?? [];
$finding_types = $_POST['finding_type'] ?? [];
$categories    = $_POST['category'] ?? [];
$severities    = $_POST['severity_item'] ?? [];
$statuses      = $_POST['status_item'] ?? [];
$descriptions  = $_POST['description'] ?? [];

$max = max(
  count($finding_ids),
  count($finding_types),
  count($categories),
  count($severities),
  count($statuses),
  count($descriptions)
);

$kept_ids = [];

for ($i = 0; $i < $max; $i++) {

  $fid  = isset($finding_ids[$i]) ? (int)$finding_ids[$i] : 0;
  $ft   = isset($finding_types[$i]) ? remove_junk(trim((string)$finding_types[$i])) : 'NCR';
  $cat  = isset($categories[$i]) ? remove_junk(trim((string)$categories[$i])) : '';
  $sev  = isset($severities[$i]) ? remove_junk(trim((string)$severities[$i])) : 'Minor';
  $st   = isset($statuses[$i]) ? remove_junk(trim((string)$statuses[$i])) : 'Open';
  $desc = isset($descriptions[$i]) ? remove_junk(trim((string)$descriptions[$i])) : '';

  // Si está totalmente vacío, saltar
  if ($desc === '' && $cat === '' && $ft === '') continue;

  // Normalización
  $allowedTypes = ['NCR','Observación','Oportunidad','Buena práctica'];
  if (!in_array($ft, $allowedTypes, true)) $ft = 'NCR';

  $allowedSev = ['Minor','Major','Critical'];
  if (!in_array($sev, $allowedSev, true)) $sev = 'Minor';

  $allowedSt = ['Open','Closed'];
  if (!in_array($st, $allowedSt, true)) $st = 'Open';

  if ($fid > 0) {

    $sql = "UPDATE auditoria_hallazgos SET
              finding_type='{$db->escape($ft)}',
              category=" . ($cat !== '' ? "'{$db->escape($cat)}'" : "NULL") . ",
              severity='{$db->escape($sev)}',
              status='{$db->escape($st)}',
              description='{$db->escape($desc)}',
              updated_at=NOW()
            WHERE id={$fid} AND auditoria_id={$id}
            LIMIT 1";

    if ($db->query($sql)) {
      $kept_ids[] = $fid;
    }

  } else {

    // Evitar insertar basura
    if ($desc === '') continue;

    $sql = "INSERT INTO auditoria_hallazgos
            (auditoria_id, finding_type, category, severity, status, description, created_at, updated_at)
            VALUES
            (
              {$id},
              '{$db->escape($ft)}',
              " . ($cat !== '' ? "'{$db->escape($cat)}'" : "NULL") . ",
              '{$db->escape($sev)}',
              '{$db->escape($st)}',
              '{$db->escape($desc)}',
              NOW(),
              NOW()
            )";

    if ($db->query($sql)) {
      $newFindingId = (int)$db->insert_id();
      if ($newFindingId > 0) $kept_ids[] = $newFindingId;
    }
  }
}

/* =========================================================
   ELIMINAR HALLAZGOS BORRADOS (solo los de esta auditoría)
========================================================= */
if (count($kept_ids) > 0) {
  $ids = implode(',', array_map('intval', $kept_ids));
  $db->query("DELETE FROM auditoria_hallazgos WHERE auditoria_id={$id} AND id NOT IN ({$ids})");
} else {
  $db->query("DELETE FROM auditoria_hallazgos WHERE auditoria_id={$id}");
}

$session->msg('s', 'Auditoría guardada correctamente.');
redirect('../pages/auditorias_form.php?id=' . $id, false);
exit;
