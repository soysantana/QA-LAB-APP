<?php
require_once "../config/load.php";
page_require_level(2);

$user = current_user();
global $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: auditorias_list.php");
  exit;
}

// Campos obligatorios mínimos de la cabecera
$req_fields = ['Audit_Code','Audit_Date','Audit_Type','Area','Severity','Status','Auditor'];
foreach ($req_fields as $f) {
  if (!isset($_POST[$f]) || trim($_POST[$f]) === '') {
    die("Missing required field: {$f}");
  }
}

$id = (int)($_POST['id'] ?? 0);

// Escapar datos de cabecera
$Audit_Code        = $db->escape($_POST['Audit_Code']);
$Audit_Date        = $db->escape($_POST['Audit_Date']);
$Audit_Type        = $db->escape($_POST['Audit_Type']);
$Area              = $db->escape($_POST['Area']);
$Scope             = $db->escape($_POST['Scope'] ?? '');
$Severity          = $db->escape($_POST['Severity']);
$Status            = $db->escape($_POST['Status']);
$Auditor           = $db->escape($_POST['Auditor']);
$Audited           = $db->escape($_POST['Audited'] ?? '');
$Related_Sample_ID = $db->escape($_POST['Related_Sample_ID'] ?? '');
$Related_Client    = $db->escape($_POST['Related_Client'] ?? '');
$Findings          = $db->escape($_POST['Findings'] ?? ''); // ahora resumen ejecutivo
$Created_By        = $db->escape($user['name'] ?? 'system');

if ($id > 0) {
  // UPDATE cabecera
  $sql = "
    UPDATE auditorias_lab
    SET
      Audit_Code        = '{$Audit_Code}',
      Audit_Date        = '{$Audit_Date}',
      Audit_Type        = '{$Audit_Type}',
      Area              = '{$Area}',
      Scope             = '{$Scope}',
      Severity          = '{$Severity}',
      Status            = '{$Status}',
      Auditor           = '{$Auditor}',
      Audited           = '{$Audited}',
      Related_Sample_ID = '{$Related_Sample_ID}',
      Related_Client    = '{$Related_Client}',
      Findings          = '{$Findings}'
    WHERE id = {$id}
    LIMIT 1
  ";
  $result = $db->query($sql);
  $audit_id = $id;
} else {
  // INSERT cabecera
  $sql = "
    INSERT INTO auditorias_lab (
      Audit_Code, Audit_Date, Audit_Type, Area, Scope,
      Findings, Severity, Status, Auditor, Audited,
      Related_Sample_ID, Related_Client, Created_By
    ) VALUES (
      '{$Audit_Code}', '{$Audit_Date}', '{$Audit_Type}', '{$Area}', '{$Scope}',
      '{$Findings}', '{$Severity}', '{$Status}', '{$Auditor}', '{$Audited}',
      '{$Related_Sample_ID}', '{$Related_Client}', '{$Created_By}'
    )
  ";
  $result = $db->query($sql);
  $audit_id = $db->insert_id;
}

if (!$result) {
  die("Error al guardar la auditoría: ".$db->error);
}

/* =========================
   GUARDAR HALLAZGOS
========================= */

// Limpiamos hallazgos existentes y reinsertamos (más simple para ti)
$db->query("DELETE FROM auditoria_hallazgos WHERE auditoria_id = {$audit_id}");

if (!empty($_POST['finding_type']) && is_array($_POST['finding_type'])) {

  $finding_type_arr = $_POST['finding_type'];
  $category_arr     = $_POST['category']        ?? [];
  $severity_arr     = $_POST['severity_item']   ?? [];
  $status_arr       = $_POST['status_item']     ?? [];
  $desc_arr         = $_POST['description']     ?? [];

  $total = count($finding_type_arr);

  for ($i = 0; $i < $total; $i++) {
    $ftype = trim((string)$finding_type_arr[$i] ?? '');
    $desc  = trim((string)$desc_arr[$i] ?? '');

    if ($ftype === '' || $desc === '') {
      // ignorar hallazgos vacíos
      continue;
    }

    $cat   = trim((string)($category_arr[$i]   ?? ''));
    $sev   = trim((string)($severity_arr[$i]   ?? 'Minor'));
    $stat  = trim((string)($status_arr[$i]     ?? 'Open'));

    $ftype_esc = $db->escape($ftype);
    $cat_esc   = $db->escape($cat);
    $sev_esc   = $db->escape($sev);
    $stat_esc  = $db->escape($stat);
    $desc_esc  = $db->escape($desc);

    $sqlH = "
      INSERT INTO auditoria_hallazgos (
        auditoria_id, finding_type, category, severity, status, description
      ) VALUES (
        {$audit_id}, '{$ftype_esc}', '{$cat_esc}', '{$sev_esc}', '{$stat_esc}', '{$desc_esc}'
      )
    ";
    $db->query($sqlH);
  }
}

header("Location: auditorias_list.php");
exit;
