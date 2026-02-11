<?php
require_once __DIR__ . "/../config/load.php";
page_require_level(2);

require_once __DIR__ . "/../libs/fpdf/fpdf.php";

global $db;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die("ID inválido."); }

$rows = find_by_sql("SELECT * FROM auditorias_lab WHERE id={$id} LIMIT 1");
if (!$rows) { die("Auditoría no encontrada."); }
$audit = $rows[0];

$hallazgos = find_by_sql("
  SELECT *
  FROM auditoria_hallazgos
  WHERE auditoria_id={$id}
  ORDER BY id ASC
");

/* =========================================================
   HELPERS
========================================================= */
function u($txt){ return utf8_decode((string)$txt); }
function s($v){ return trim((string)$v); }

/**
 * Resumen Ejecutivo AUTO usando datos reales del sistema (hallazgos + auditoría)
 * Formato tipo tu ejemplo:
 * "Se identificaron 3 hallazgos. Un (1) NCR major ... permanece abierto y requiere acción correctiva antes del 2026-02-05.
 *  Las observaciones restantes..."
 *
 * NOTA: Si NO tienes due_date, usa Audit_Date + 14 días como fecha objetivo.
 */
function build_exec_summary(array $audit, array $hallazgos): string {
  $total = count($hallazgos);
  if ($total <= 0) return "Se identificaron 0 hallazgos.";

  // Buscar NCR mayor/critico abierto (prioriza Critical)
  $main = null;
  foreach ($hallazgos as $h) {
    $type = s($h['finding_type'] ?? '');
    $sev  = s($h['severity'] ?? '');
    $st   = s($h['status'] ?? '');

    $isCandidate = (strcasecmp($type, 'NCR') === 0)
      && (strcasecmp($st, 'Open') === 0)
      && (strcasecmp($sev, 'Major') === 0 || strcasecmp($sev, 'Critical') === 0);

    if ($isCandidate) {
      if (!$main) $main = $h;
      else {
        $prev = s($main['severity'] ?? '');
        if (strcasecmp($prev,'Critical') !== 0 && strcasecmp($sev,'Critical') === 0) {
          $main = $h;
        }
      }
    }
  }

  // Contar tipos para texto
  $countNCR = 0; $countObs = 0; $countOpp = 0; $countGood = 0;
  foreach ($hallazgos as $h){
    $t = s($h['finding_type'] ?? '');
    if (strcasecmp($t,'NCR')===0) $countNCR++;
    elseif (stripos($t,'Observ')!==false) $countObs++;
    elseif (stripos($t,'Oport')!==false) $countOpp++;
    elseif (stripos($t,'Buena')!==false) $countGood++;
  }

  $txt = "Se identificaron {$total} hallazgos. ";

  if ($main) {
    $sev = s($main['severity'] ?? 'Major');

    // Fecha objetivo: due_date si existe, si no: Audit_Date +14
    $due = s($main['due_date'] ?? '');
    if ($due === '' && !empty($audit['Audit_Date'])) {
      $t = strtotime($audit['Audit_Date']);
      if ($t) $due = date('Y-m-d', strtotime('+14 days', $t));
    }

    // "control documental" si Area o category sugiere documental
    $area = mb_strtolower(s($audit['Area'] ?? ''), 'UTF-8');
    $cat  = mb_strtolower(s($main['category'] ?? ''), 'UTF-8');
    $relDoc = (strpos($area,'document')!==false) || (strpos($cat,'document')!==false);

    $txt .= "Un (1) NCR {$sev}";
    if ($relDoc) $txt .= " relacionado a control documental";
    $txt .= " permanece abierto y requiere acción correctiva";
    if ($due !== '') $txt .= " antes del {$due}";
    $txt .= ". ";
  }

  // texto para el resto
  $rest = $total - 1;
  if ($total > 1) {
    $txt .= "Las observaciones restantes son oportunidades de mejora para estandarización y control preventivo.";
  }

  return trim($txt);
}

/* ==========================
   PDF CLASS (tabla pro)
========================== */
class AuditPDF extends FPDF {

  public array $widths = [];
  public array $aligns = [];
  public float $bottomMarginFixed = 14.0; // ✅ mismo margen que SetAutoPageBreak(true, 14)

  function Header(){
    $logo = __DIR__ . "/../assets/img/Pueblo-Viejo.jpg";
    if (file_exists($logo)) {
      $this->Image($logo, 10, 8, 35);
    }

    $this->SetFont('Arial','B',14);
    $this->Cell(0,8, u('Laboratorio de Mecánica de Suelo PVDJ - Auditoría'), 0, 1, 'R');

    $this->Ln(2);
    $this->Line(10, $this->GetY(), 206, $this->GetY());
    $this->Ln(4);
  }

  function Footer(){
    $this->SetY(-12);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10, u('Página '.$this->PageNo().'/{nb}'), 0, 0, 'C');
  }

  function SetWidths($w){ $this->widths = $w; }
  function SetAligns($a){ $this->aligns = $a; }

  // ✅ NbLines como método (usa CurrentFont, pero lo protegemos)
  function NbLines($w, $txt){
    // Si por alguna razón no hay fuente aún, fuerza una por defecto
    if (empty($this->CurrentFont)) {
      $this->SetFont('Arial','',9);
    }

    $cw = $this->CurrentFont['cw'] ?? [];
    if($w==0) $w = $this->w-$this->rMargin-$this->x;

    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',(string)$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n") $nb--;

    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while($i<$nb){
      $c = $s[$i];
      if($c=="\n"){
        $i++; $sep=-1; $j=$i; $l=0; $nl++; continue;
      }
      if($c==' ') $sep=$i;

      $l += $cw[$c] ?? 500; // fallback ancho
      if($l>$wmax){
        if($sep==-1){
          if($i==$j) $i++;
        } else {
          $i = $sep+1;
        }
        $sep=-1; $j=$i; $l=0; $nl++;
      } else {
        $i++;
      }
    }
    return $nl;
  }

  // ✅ Check page break SIN bMargin (evita error de propiedad protegida)
  function CheckPageBreak($h){
    $pageH = method_exists($this,'GetPageHeight') ? $this->GetPageHeight() : 279.4; // Letter
    $limit = $pageH - $this->bottomMarginFixed;

    if($this->GetY() + $h > $limit){
      $this->AddPage($this->CurOrientation);
    }
  }

  // Fila multicelda por columna (estable)
  function Row($data, $lineH = 6){
    $nb = 0;
    $count = count($data);

    for($i=0; $i<$count; $i++){
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    }
    $h = $lineH * $nb;

    $this->CheckPageBreak($h);

    for($i=0; $i<$count; $i++){
      $w = $this->widths[$i];
      $a = $this->aligns[$i] ?? 'L';

      $x = $this->GetX();
      $y = $this->GetY();

      $this->Rect($x, $y, $w, $h);
      $this->MultiCell($w, $lineH, u((string)$data[$i]), 0, $a);

      $this->SetXY($x + $w, $y);
    }
    $this->Ln($h);
  }

  function SectionTitle($txt){
    $this->SetFont('Arial','B',11);
    $this->SetFillColor(240,240,240);
    $this->Cell(0,7, u($txt), 0, 1, 'L', true);
    $this->Ln(1);
  }
}

/* ==========================
   BUILD PDF
========================== */
$pdf = new AuditPDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 14);
$pdf->AddPage();

$pdf->SetFont('Arial','',10);

// ==========================
// INFORMACIÓN GENERAL
// ==========================
$pdf->SectionTitle("INFORMACIÓN GENERAL");

$code = $audit['Audit_Code'] ?? '';

$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,6, u("Código:"), 0, 0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6, u($code), 0, 1);

$fields = [
  ["Fecha", $audit['Audit_Date'] ?? ""],
  ["Tipo", $audit['Audit_Type'] ?? ""],
  ["Área / Proceso", $audit['Area'] ?? ""],
  ["Severidad Global", $audit['Severity'] ?? ""],
  ["Estado", $audit['Status'] ?? ""],
  ["Auditor", $audit['Auditor'] ?? ""],
  ["Auditado", $audit['Audited'] ?? ""],
];

foreach($fields as $f){
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(40,6, u($f[0].":"), 0, 0);
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,6, u((string)$f[1]), 0, 1);
}

$scope = trim((string)($audit['Scope'] ?? ''));
if($scope !== ''){
  $pdf->Ln(1);
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(0,6, u("Alcance / Scope:"), 0, 1);
  $pdf->SetFont('Arial','',10);
  $pdf->MultiCell(0,5, u($scope), 0);
}

$rel = [];
if (!empty($audit['Related_Sample_ID'])) $rel[] = "Sample: ".$audit['Related_Sample_ID'];
if (!empty($audit['Related_Client']))    $rel[] = "Client: ".$audit['Related_Client'];
if ($rel){
  $pdf->Ln(1);
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(40,6, u("Relacionado:"), 0, 0);
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,6, u(implode(" / ", $rel)), 0, 1);
}

// ==========================
// RESUMEN EJECUTIVO (AUTO + INPUT)
// ==========================
$pdf->Ln(2);
$pdf->SectionTitle("RESUMEN EJECUTIVO");
$pdf->SetFont('Arial','',10);

// AUTO (siempre primero, sin salto extra)
$auto = build_exec_summary($audit, $hallazgos);
$pdf->MultiCell(0,5, u($auto), 0);

// TEXTO DIGITADO (opcional)
$findings = trim((string)($audit['Findings'] ?? ''));
if($findings !== ''){
  $pdf->Ln(1);
  $pdf->MultiCell(0,5, u($findings), 0);
}


// ==========================
// HALLAZGOS
// ==========================
$pdf->Ln(2);
$pdf->SectionTitle("HALLAZGOS");

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(220,220,220);

// Ajuste para Letter (ancho útil ~190mm)
$pdf->SetWidths([10, 24, 32, 20, 16, 94]);
$pdf->SetAligns(['C','L','L','C','C','L']);

$pdf->Cell(10,7,'#',1,0,'C',true);
$pdf->Cell(24,7,u('Tipo'),1,0,'C',true);
$pdf->Cell(32,7,u('Categoría'),1,0,'C',true);
$pdf->Cell(20,7,u('Severidad'),1,0,'C',true);
$pdf->Cell(16,7,u('Estado'),1,0,'C',true);
$pdf->Cell(94,7,u('Descripción'),1,1,'C',true);

$pdf->SetFont('Arial','',9);

if(!$hallazgos){
  $pdf->Cell(0,8, u("No hay hallazgos registrados."), 1, 1, 'C');
} else {
  $n = 1;
  foreach($hallazgos as $h){
    $pdf->Row([
      (string)$n,
      (string)($h['finding_type'] ?? ''),
      (string)($h['category'] ?? ''),
      (string)($h['severity'] ?? ''),   // ✅ aquí SIN °C
      (string)($h['status'] ?? ''),
      (string)($h['description'] ?? ''),
    ], 5);
    $n++;
  }
}


/* ==========================================================
   5. PLAN DE ACCIÓN Y SEGUIMIENTO
   - Fuente de datos: tabla auditoria_acciones (recomendado)
   - Si no existe o está vacía, genera filas “pendientes” desde hallazgos
========================================================== */

// 1) Buscar acciones (si ya tienes/crearás esta tabla)
$acciones = [];
$acciones = find_by_sql("
  SELECT
    a.id,
    a.hallazgo_ref,
    a.accion,
    a.responsable,
    a.fecha_compromiso,
    a.status
  FROM acciones_auditoria a
  WHERE a.auditoria_id = {$id}
  ORDER BY a.id ASC
");

// 2) Si NO hay acciones guardadas aún, generamos un “borrador” desde hallazgos
//    (para que el PDF no salga vacío y puedas ver el formato)
if (!$acciones && $hallazgos) {
  $tmp = [];
  $n = 1;
  foreach ($hallazgos as $h) {
    $tipo = (string)($h['finding_type'] ?? '');
    $pref = 'Hallazgo';
    if ($tipo === 'NCR') $pref = 'NCR';
    elseif (stripos($tipo, 'Observ') !== false) $pref = 'Obs.';
    elseif (stripos($tipo, 'Oport') !== false) $pref = 'Oport.';
    elseif (stripos($tipo, 'Buena') !== false) $pref = 'BP';

    $tmp[] = [
      'hallazgo_ref'     => $pref.' #'.$n,
      'accion'           => '',     // aquí irá lo digitado en el sistema
      'responsable'      => '',
      'fecha_compromiso' => '',
      'status'           => 'Open',
    ];
    $n++;
  }
  $acciones = $tmp;
}

$pdf->Ln(3);
$pdf->SectionTitle("5. Plan de acción y seguimiento");

// HEADER (azul tipo ISO)
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(220,220,220);
$pdf->SetTextColor(0);

// Ancho total Letter útil ~190mm
// # | Hallazgo | Acción | Responsable | Fecha compromiso | Status
$pdf->SetWidths([8, 20, 78, 30, 35, 22]);
$pdf->SetAligns(['C','L','L','L','C','C']);

$pdf->Cell(8,7,'#',1,0,'C',true);
$pdf->Cell(20,7,utf8_decode('Hallazgo'),1,0,'C',true);
$pdf->Cell(78,7,utf8_decode('Acción correctiva / preventiva'),1,0,'C',true);
$pdf->Cell(30,7,utf8_decode('Responsable'),1,0,'C',true);
$pdf->Cell(35,7,utf8_decode('Fecha compromiso'),1,0,'C',true);
$pdf->Cell(22,7,utf8_decode('Status'),1,1,'C',true);

// BODY
$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0);

// Helpers (ajusta si tus nombres son distintos)
function norm_status($st) {
  $allowed = ['Open','In Progress','Closed'];
  return in_array($st, $allowed, true) ? $st : 'Open';
}

function is_good_practice($tipo) {
  $t = mb_strtolower(trim((string)$tipo));
  return in_array($t, [
    'buena practica',
    'buena práctica',
    'good practice',
    'positive finding',
    'fortaleza'
  ], true);
}

// Filtrar: NO imprimir acciones asociadas a Buenas Prácticas
$acciones_print = [];
if (!empty($acciones)) {
  foreach ($acciones as $ac) {
    $tipo = $ac['tipo_hallazgo'] ?? '';
    if (is_good_practice($tipo)) {
      continue; // ✅ Buenas prácticas NO van en Plan de Acción
    }
    $acciones_print[] = $ac;
  }
}

if (empty($acciones_print)) {
  $pdf->Cell(0,8, utf8_decode("No hay acciones correctivas / de mejora registradas para esta auditoría."), 1, 1, 'C');
} else {

  $i = 1;

  foreach ($acciones_print as $ac) {

    // Si estás usando hallazgo_id en vez de hallazgo_ref, ajusta aquí
    $hallazgoRef = trim((string)($ac['hallazgo_ref'] ?? ''));
    if ($hallazgoRef === '') {
      // ✅ No uses $i como si fuera el hallazgo real
      $hallazgoRef = 'Sin referencia de hallazgo';
    }

    $accionTxt   = trim((string)($ac['accion'] ?? ''));
    $respTxt     = trim((string)($ac['responsable'] ?? ''));
    $fechaTxt    = trim((string)($ac['fecha_compromiso'] ?? ''));
    $stTxt       = norm_status(trim((string)($ac['status'] ?? 'Open')));

    // ✅ Si la acción está vacía, NO pongas “pendiente de definir evidencia”
    //    (evidencia es otro campo). Marca como requerido.
    if ($accionTxt === '') {
      $accionTxt = 'PLAN DE ACCIÓN NO DEFINIDO (Requerido)';
      $stTxt = 'Open';
    }

    // (Opcional) defaults elegantes, sin inventar información
    if ($respTxt === '')  $respTxt = 'No asignado';
    if ($fechaTxt === '') $fechaTxt = 'Sin fecha';

    $pdf->Row([
      (string)$i,
      utf8_decode($hallazgoRef),
      utf8_decode($accionTxt),
      utf8_decode($respTxt),
      utf8_decode($fechaTxt),
      utf8_decode($stTxt)
    ], 5);

    $i++;
  }
}

// ==========================
// CIERRE DE AUDITORÍA (FIRMAS / RESPONSABLES)
// ==========================
$pdf->Ln(6);
$pdf->SectionTitle("FIRMAS");

$colW = 95;   // 2 columnas
$lineH = 7;

$prepBy = trim((string)($audit['Auditor'] ?? ''));
$doneTo = trim((string)($audit['Audited'] ?? ''));

// TÍTULOS
$pdf->SetFont('Arial','B',10);
$pdf->Cell($colW, $lineH, u("Auditoría preparada por"), 0, 0, 'L');
$pdf->Cell($colW, $lineH, u("Auditoría realizada a"), 0, 1, 'L');

// ESPACIO PARA FIRMA
$pdf->Ln(1);

// LÍNEAS DE FIRMA
$y = $pdf->GetY();
$x = $pdf->GetX();

// Izquierda
$pdf->Line($x, $y, $x + $colW - 10, $y);
// Derecha
$pdf->Line($x + $colW, $y, $x + 2*$colW - 10, $y);

$pdf->Ln(2);

// NOMBRES
$pdf->SetFont('Arial','',10);
$pdf->Cell($colW, 6, u($prepBy !== '' ? $prepBy : '—'), 0, 0, 'L');
$pdf->Cell($colW, 6, u($doneTo !== '' ? $doneTo : '—'), 0, 1, 'L');

// FECHA DE EMISIÓN
$pdf->Ln(4);
$pdf->SetFont('Arial','I',9);
$pdf->Cell(0,6, u("Fecha de emisión del reporte: ".date('Y-m-d')), 0, 1, 'R');



// Output
$filename = 'Reporte_Auditoria_' . preg_replace('/[^A-Za-z0-9_\-]/','_', (string)$code) . '.pdf';
$pdf->Output('I', $filename);
exit;
