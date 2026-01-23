<?php
$page_title = "Auditoría del Laboratorio";
require_once "../config/load.php";
page_require_level(2);
include_once('../components/header.php');

$user = current_user();
global $db;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$audit = null;
$hallazgos = [];
$acciones = []; // ✅ NUEVO


if ($id > 0) {
  $rows = find_by_sql("SELECT * FROM auditorias_lab WHERE id = {$id} LIMIT 1");
  if ($rows) {
  $audit = $rows[0];

  // Traer hallazgos asociados
  $hallazgos = find_by_sql("
    SELECT *
    FROM auditoria_hallazgos
    WHERE auditoria_id = {$id}
    ORDER BY id ASC
  ");

  // ✅ Traer acciones asociadas (PLAN DE ACCIÓN)
  $acciones = find_by_sql("
    SELECT *
    FROM acciones_auditoria
    WHERE auditoria_id = {$id}
    ORDER BY id ASC
  ");
}

}

if (!$audit) {

  $year = date('Y');

  $row = find_by_sql("
    SELECT MAX(CAST(SUBSTRING_INDEX(Audit_Code, '-', -1) AS UNSIGNED)) AS maxnum
    FROM auditorias_lab
    WHERE Audit_Code LIKE 'Lab-Aud-{$year}-%'
  ");

  $next = (int)($row[0]['maxnum'] ?? 0) + 1;

  $code = sprintf("Lab-Aud-%s-%03d", $year, $next);
}

?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1><?php echo $audit ? "Editar Auditoría" : "Nueva Auditoría"; ?></h1>
    <p class="text-muted">
      Registro estructurado de auditorías al proceso del laboratorio (registro, preparación, ensayos, reporte y doc control).
    </p>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Información General</h5>

      <form method="post" action="../database/auditorias_save.php" id="formAuditoria">

        <input type="hidden" name="id" value="<?php echo $audit['id'] ?? ''; ?>">

        <!-- BLOQUE 1: DATOS BÁSICOS -->
        <div class="row g-3 mb-3">

          <div class="col-md-3">
            <label class="form-label">Código de Auditoría</label>
            <input type="text" name="Audit_Code" class="form-control"
                   required
                   value="<?php echo htmlentities($audit['Audit_Code'] ?? $code); ?>">
            <small class="text-muted">Ej: Lab-Aud26-001.</small>
          </div>

          <div class="col-md-3">
            <label class="form-label">Fecha de Auditoría</label>
            <input type="date" name="Audit_Date" class="form-control"
                   required
                   value="<?php echo htmlentities($audit['Audit_Date'] ?? date('Y-m-d')); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Tipo de Auditoría</label>
            <select name="Audit_Type" class="form-select" required>
              <?php
                $tipoActual = $audit['Audit_Type'] ?? 'Interna';
                $tipos = ['Interna','Externa','Aprobacion','Seguimiento','Certificación'];
                foreach ($tipos as $t):
              ?>
                <option value="<?php echo $t; ?>"
                  <?php if($tipoActual==$t) echo 'selected'; ?>>
                  <?php echo $t; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Estado de la Auditoría</label>
            <select name="Status" class="form-select" required>
              <?php
                $stAct = $audit['Status'] ?? 'Open';
                $sts = ['Open','In Progress','Closed'];
                foreach ($sts as $s):
              ?>
                <option value="<?php echo $s; ?>"
                  <?php if($stAct==$s) echo 'selected'; ?>>
                  <?php echo $s; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Open = levantada; Closed = todas las acciones cerradas.</small>
          </div>

        </div>

        <!-- BLOQUE 2: ALCANCE Y RESPONSABLES -->
        <div class="row g-3 mb-3">

          <div class="col-md-4">
            <label class="form-label">Área / Proceso Auditado</label>
            <input type="text" name="Area" class="form-control"
                   list="areas_sugeridas"
                   required
                   placeholder="Granulometría, Proctor & Densidades, Concreto..."
                   value="<?php echo htmlentities($audit['Area'] ?? ''); ?>">

            <datalist id="areas_sugeridas">
              <option value="Recepción y Registro de Muestras">
              <option value="Cuarteo de material">
              <option value="Preparación de Muestras">
              <option value="Castillo de Arena">
              <option value="Contenido de humedad">
              <option value="Granulometría">
              <option value="Atterberg / Clasificación">
              <option value="Proctor">
              <option value="Gravedad Especifica">
              <option value="Reactividad Acida">
              <option value="Sanidad/ Sulfatos">
              <option value="Abrasion/ Maq. Los Angeles">
              <option value="Concreto / Cilindros">
              <option value="Hidrómetro / Finos">
              <option value="Control Documental">
              <option value="Gestión de Equipos y Calibraciones">
                <option value="Otros">
            </datalist>
          </div>

          <div class="col-md-4">
            <label class="form-label">Alcance / Scope</label>
            <input type="text" name="Scope" class="form-control"
                   placeholder="Ej: Desde el registro de la muestra hasta la emisión del reporte."
                   value="<?php echo htmlentities($audit['Scope'] ?? ''); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Severidad Global</label>
            <select name="Severity" class="form-select" required>
              <?php
                $sevAct = $audit['Severity'] ?? 'Minor';
                $sevs = ['Minor','Major','Critical'];
                foreach ($sevs as $s):
              ?>
                <option value="<?php echo $s; ?>"
                  <?php if($sevAct==$s) echo 'selected'; ?>>
                  <?php echo $s; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Valoración general según los hallazgos.</small>
          </div>

        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Auditor</label>
            <input type="text" name="Auditor" class="form-control"
                   required
                   value="<?php echo htmlentities($audit['Auditor'] ?? $user['name']); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Auditado / Área Auditada</label>
            <input type="text" name="Audited" class="form-control"
                   placeholder="Técnicos turno A, Doc Control, Equipo de concreto..."
                   value="<?php echo htmlentities($audit['Audited'] ?? ''); ?>">
          </div>

          <div class="col-md-2">
            <label class="form-label">Sample ID (opcional)</label>
            <input type="text" name="Related_Sample_ID" class="form-control"
                   placeholder="LLD-258-0078"
                   value="<?php echo htmlentities($audit['Related_Sample_ID'] ?? ''); ?>">
          </div>

          <div class="col-md-2">
            <label class="form-label">Cliente (opcional)</label>
            <input type="text" name="Related_Client" class="form-control"
                   placeholder="BGC, CQA, IDC..."
                   value="<?php echo htmlentities($audit['Related_Client'] ?? ''); ?>">
          </div>
        </div>

   <!-- BLOQUE 3: HALLAZGOS ESTRUCTURADOS -->
<hr>
<div class="d-flex align-items-center justify-content-between mt-3">
  <div>
    <h5 class="mb-0">Hallazgos de la Auditoría</h5>
    <div class="text-muted small">
      Agrega NCR, observaciones, oportunidades o buenas prácticas con control por severidad y estado.
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-primary btn-sm" id="addHallazgoBtn">
      <i class="bi bi-plus-circle"></i> Agregar hallazgo
    </button>
  </div>
</div>

<!-- RESUMEN -->
<div class="row g-2 mt-2" id="summaryCards">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="text-muted small">Total</div>
        <div class="fs-5 fw-bold" id="sumTotal">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="text-muted small">Open</div>
        <div class="fs-5 fw-bold" id="sumOpen">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="text-muted small">Closed</div>
        <div class="fs-5 fw-bold" id="sumClosed">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="text-muted small">NCR</div>
        <div class="fs-5 fw-bold" id="sumNCR">0</div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3" id="hallazgosContainer">
  <div class="accordion" id="hallazgosAccordion">

    <?php if (!empty($hallazgos)): ?>
      <?php foreach ($hallazgos as $idx => $h): ?>
        <?php
          $hid = (int)($h['id'] ?? 0);
          $ft  = $h['finding_type'] ?? 'NCR';
          $sev = $h['severity'] ?? 'Minor';
          $st  = $h['status'] ?? 'Open';

          $desc = $h['description'] ?? '';
          $cat  = $h['category'] ?? '';
        ?>

        <div class="accordion-item hallazgo-item border-0 shadow-sm mb-2"
             data-finding-id="<?php echo $hid; ?>">
          <h2 class="accordion-header" id="heading-<?php echo $idx; ?>">
            <button class="accordion-button collapsed d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-<?php echo $idx; ?>"
                    aria-expanded="false"
                    aria-controls="collapse-<?php echo $idx; ?>">

              <span class="badge bg-secondary me-1">#<?php echo ($idx+1); ?></span>

              <span class="me-2 fw-semibold">
                <?php echo htmlentities($ft); ?>
              </span>

              <span class="badge me-1 jsBadgeSeverity"><?php echo htmlentities($sev); ?></span>
              <span class="badge jsBadgeStatus"><?php echo htmlentities($st); ?></span>

              <span class="ms-auto text-muted small jsHeaderText">
                <?php echo htmlentities(mb_strimwidth(trim($desc), 0, 70, '...')); ?>
              </span>

            </button>
          </h2>

          <div id="collapse-<?php echo $idx; ?>"
               class="accordion-collapse collapse"
               aria-labelledby="heading-<?php echo $idx; ?>"
               data-bs-parent="#hallazgosAccordion">

            <div class="accordion-body">
              <!-- IMPORTANTE: para editar vs insertar -->
              <input type="hidden" name="finding_id[]" value="<?php echo $hid; ?>">

              <div class="row g-2 align-items-end">
                <div class="col-md-3">
                  <label class="form-label form-label-sm">Tipo</label>
                  <select name="finding_type[]" class="form-select form-select-sm jsType">
                    <?php
                      $tiposH = ['NCR','Observación','Oportunidad','Buena práctica'];
                      foreach ($tiposH as $tH):
                    ?>
                      <option value="<?php echo $tH; ?>"
                        <?php if($ft==$tH) echo 'selected'; ?>>
                        <?php echo $tH; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-3">
                  <label class="form-label form-label-sm">Categoría</label>
                  <input type="text" name="category[]" class="form-control form-control-sm jsCategory"
                        list="areas_sugeridas"
                        value="<?php echo htmlentities($cat); ?>"
                        placeholder="Granulometría, Doc Control...">
                </div>

                <div class="col-md-2">
                  <label class="form-label form-label-sm">Severidad</label>
                  <select name="severity_item[]" class="form-select form-select-sm jsSeverity">
                    <?php
                      $sevsH = ['Minor','Major','Critical'];
                      foreach ($sevsH as $sH):
                    ?>
                      <option value="<?php echo $sH; ?>"
                        <?php if($sev==$sH) echo 'selected'; ?>>
                        <?php echo $sH; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <label class="form-label form-label-sm">Estado</label>
                  <select name="status_item[]" class="form-select form-select-sm jsStatus">
                    <option value="Open"   <?php if($st=='Open') echo 'selected'; ?>>Open</option>
                    <option value="Closed" <?php if($st=='Closed') echo 'selected'; ?>>Closed</option>
                  </select>
                </div>

                <div class="col-md-2 text-end">
                  <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary jsMoveUp" title="Subir">
                      <i class="bi bi-arrow-up"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary jsMoveDown" title="Bajar">
                      <i class="bi bi-arrow-down"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary jsDuplicate" title="Duplicar">
                      <i class="bi bi-files"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger jsRemove" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>

                <div class="col-md-6 mt-2">
                  <label class="form-label form-label-sm">Plantillas</label>
                  <select class="form-select form-select-sm plantillaSelect">
                    <option value="">-- Seleccionar --</option>
                    <option value="Muestra sin trazabilidad completa en el registro (falta Sample_ID o número de requisición).">
                      Falta trazabilidad de muestra
                    </option>
                    <option value="Procedimiento no seguido según procedimiento vigente para este ensayo.">
                      Procedimiento no seguido
                    </option>
                    <option value="Formato de reporte incompleto o con campos críticos en blanco.">
                      Reporte incompleto
                    </option>
                    <option value="Equipo utilizado con calibración vencida o sin evidencia de calibración.">
                      Calibración de equipo
                    </option>
                    <option value="Buena práctica detectada: documentación clara y completa en el registro de muestras.">
                      Buena práctica en registro
                    </option>
                  </select>
                </div>

                <div class="col-12 mt-2">
                  <label class="form-label form-label-sm">Descripción breve del hallazgo</label>
                  <textarea name="description[]" rows="3"
                            class="form-control form-control-sm jsDesc"><?php echo htmlentities($desc); ?></textarea>
                  <div class="text-muted small mt-1">
                    Tip: describe <b>qué pasó</b>, <b>evidencia</b>, <b>impacto</b> y <b>acción sugerida</b>.
                  </div>
                </div>

              </div>

            </div>
          </div>
        </div>

      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>
<hr>
<h5 class="mt-3">Plan de acción y seguimiento</h5>
<p class="text-muted small">Acciones correctivas/preventivas ligadas a esta auditoría.</p>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th style="width:40px;">#</th>
        <th style="width:90px;">Hallazgo</th>
        <th>Acción correctiva / preventiva</th>
        <th style="width:140px;">Responsable</th>
        <th style="width:140px;">Fecha compromiso</th>
        <th style="width:120px;">Status</th>
        <th style="width:90px;">Acción</th>
      </tr>
    </thead>
    <tbody id="accionesBody">
      <?php if (!empty($acciones ?? [])): ?>
        <?php foreach (($acciones ?? []) as $i => $ac): ?>
          <tr>
            <td class="text-center"><?php echo $i+1; ?></td>

            <td>
              <input type="hidden" name="accion_id[]" value="<?php echo (int)$ac['id']; ?>">
              <input type="hidden" name="accion_finding_id[]" value="<?php echo (int)($ac['finding_id'] ?? 0); ?>">

              <input type="text" name="hallazgo_ref[]" class="form-control form-control-sm"
                     value="<?php echo htmlentities($ac['hallazgo_ref'] ?? ''); ?>"
                     placeholder="NCR #1">
            </td>

            <td>
              <textarea name="accion[]" rows="2" class="form-control form-control-sm" required><?php
                echo htmlentities($ac['accion'] ?? '');
              ?></textarea>
            </td>

            <td>
              <input type="text" name="responsable[]" class="form-control form-control-sm"
                     value="<?php echo htmlentities($ac['responsable'] ?? ''); ?>"
                     placeholder="Doc Control">
            </td>

            <td>
              <input type="date" name="fecha_compromiso[]" class="form-control form-control-sm"
                     value="<?php echo htmlentities($ac['fecha_compromiso'] ?? ''); ?>">
            </td>

            <td>
              <select name="accion_status[]" class="form-select form-select-sm">
                <?php $st = $ac['status'] ?? 'Open'; ?>
                <option value="Open"        <?php if($st=='Open') echo 'selected'; ?>>Open</option>
                <option value="In Progress" <?php if($st=='In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Closed"      <?php if($st=='Closed') echo 'selected'; ?>>Closed</option>
              </select>
            </td>

            <td class="text-center">
              <button type="button" class="btn btn-sm btn-outline-danger jsRemoveAcc">Quitar</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <button type="button" class="btn btn-sm btn-outline-primary" id="addAccBtn">
    + Agregar acción
  </button>
</div>

<!-- BLOQUE 4: RESUMEN EJECUTIVO -->
<hr>
<h5 class="mt-3">Resumen Ejecutivo (opcional)</h5>
<p class="text-muted small">
  Si quieres, escribe aquí un resumen general de la auditoría para reportes mensuales/anuales.
</p>
<div class="mb-3">
  <textarea name="Findings" class="form-control" rows="3"
            placeholder="Resumen breve de la auditoría (no los detalles, eso está arriba como hallazgos estructurados)."><?php
    echo htmlentities($audit['Findings'] ?? '');
  ?></textarea>
</div>
 <div class="mt-3">
          <a href="../pages/auditorias_list.php" class="btn btn-secondary">
            ← Volver al listado
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Auditoría
          </button>
        </div>

      </form>

    </div>
  </div>

</main>
<script>
(function(){

  /* =========================================================
     TEMPLATE LIB (por Ensayo + Categoría ISO)
     Puedes seguir ampliándola cuando quieras.
  ========================================================= */
  const TEMPLATE_LIB = {
    General: {
      Trazabilidad: [
        "No se evidencia trazabilidad completa de la muestra (Sample_ID/No. requisición/fecha/ubicación incompletos).",
        "Inconsistencia entre requisición y registro (Sample_ID, Sample_Number o Client no coinciden).",
        "Etiquetado de muestra incompleto/ilegible; riesgo de identificación incorrecta.",
        "Cadena de custodia incompleta o sin evidencia documental.",
        "Registro no permite reconstruir el historial del ensayo (quién, cuándo, con qué equipo)."
      ],
      "Método": [
        "No se evidenció cumplimiento del procedimiento vigente para el ensayo (método/versión no referenciado).",
        "Desviación del método sin justificación técnica ni aprobación.",
        "No se documentaron controles de calidad requeridos (réplicas/verificaciones)."
      ],
      "Equipo": [
        "Equipo utilizado sin evidencia de calibración vigente / certificado disponible.",
        "No se registró verificación intermedia/diaria requerida del equipo.",
        "Equipo sin identificación (ID/serial) en el registro del ensayo."
      ],
      "Cálculo": [
        "Cálculos no verificables (faltan datos intermedios o fórmula aplicada).",
        "Resultados con inconsistencias (sumatorias/%/unidades) que requieren verificación.",
        "No hay evidencia de revisión de cálculos (doble chequeo)."
      ],
      "Reporte": [
        "Reporte con campos críticos en blanco (condición, método, firma, fecha, unidades).",
        "Reporte emitido sin evidencia de revisión técnica/aprobación.",
        "Datos en reporte no coinciden con registros del ensayo (posible error de transcripción)."
      ],
      "Competencia": [
        "No se evidenció competencia/entrenamiento documentado del personal para este ensayo.",
        "Técnico asignado no registrado o firma no disponible."
      ],
      "Ambiente": [
        "Condiciones ambientales relevantes no registradas (cuando aplica: temperatura/humedad).",
        "Área con riesgo de contaminación cruzada; limpieza/segregación insuficiente."
      ],
      "Seguridad": [
        "Manejo de residuos/químicos sin contención o registro adecuado; requiere acción correctiva.",
        "PPE/seguridad no evidenciada en actividad crítica; riesgo de seguridad."
      ],
      "Buenas": [
        "Buena práctica: registros completos, legibles y trazables; evidencia documental adecuada.",
        "Buena práctica: cumplimiento estricto del método y orden del área de trabajo."
      ],
      "Muestra": [
        "La muestra presenta alteración/contaminación; no se evidencia segregación adecuada.",
        "No se documenta condición de la muestra (seca/húmeda) previo al ensayo."
      ],
      "Preparación": [
        "Secado no documentado (tiempo/temperatura); riesgo de humedad residual.",
        "Pérdida de finos durante transferencia; requiere investigar o repetir."
      ]
    },

    "Preparación": {
      Trazabilidad: [
        "No se documentó el proceso de cuarteo/submuestra; representatividad no demostrada.",
        "Submuestras sin identificación individual (A/B/C) o sin relación clara con Sample_ID."
      ],
      "Preparación": [
        "Contaminación cruzada entre muestras; limpieza/segregación insuficiente.",
        "Registro de preparación incompleto (pesos, fecha, técnico, equipo)."
      ],
      "Equipo": [
        "Balanza utilizada sin verificación vigente / sin registro de verificación.",
        "Horno sin registro de temperatura (cuando aplica)."
      ]
    },

    "Contenido de humedad": {
      "Método": [
        "No se documentó condición inicial de la muestra; método de secado no evidenciado.",
        "Tiempo/temperatura de secado no registrados; no se evidencia criterio de masa constante."
      ],
      "Equipo": [
        "Balanza sin evidencia de verificación vigente.",
        "Horno sin control/registro de temperatura."
      ],
      "Cálculo": [
        "Cálculo de humedad no verificable (pesos incompletos/cápsulas sin identificación)."
      ],
      "Reporte": [
        "Registros incompletos: cápsula, peso húmedo, peso seco, fecha, técnico."
      ]
    },

    "Granulometría": {
      "Método": [
        "Lavado de finos incompleto; material fino retenido afecta % pasante.",
        "Tiempo/agitación/criterio del método para lavado de finos no documentado.",
        "No se documentó condición de la muestra (seca/húmeda) antes del tamizado."
      ],
      "Equipo": [
        "Tamices sucios/dañados o sin control de condición.",
        "Shaker/agitador: tiempo de agitación no documentado o verificación no evidenciada.",
        "Balanza sin verificación vigente."
      ],
      "Cálculo": [
        "Sumatoria de pesos no cuadra con masa inicial; requiere investigación.",
        "Error en % retenido/% pasante acumulado; requiere recalcular."
      ],
      "Reporte": [
        "No se registró masa inicial, fracciones, ni evidencia de control de pérdida.",
        "Clasificación/resultado no coincide con datos de granulometría."
      ]
    },

    "Atterberg": {
      "Método": [
        "No se evidenció cumplimiento de criterios del método (LL/PL).",
        "Datos insuficientes para regresión o consistencia fuera de criterio."
      ],
      "Equipo": [
        "Dispositivo/rodillo sin evidencia de verificación/condición adecuada.",
        "Balanza sin verificación vigente."
      ],
      "Cálculo": [
        "Repetibilidad fuera de límites (SR); requiere repetir/verificar.",
        "PI inconsistente con LL/PL; revisar transcripción/cálculo."
      ],
      "Reporte": [
        "Faltan cápsulas/fechas/técnico/equipo en registros."
      ]
    },

    "Proctor": {
      "Método": [
        "Energía de compactación no documentada (capas/golpes) o no coincide con método.",
        "Corrección por sobre-tamaño no aplicada/documentada (si aplica)."
      ],
      "Equipo": [
        "Molde/martillo sin evidencia de verificación (volumen/peso/altura).",
        "Balanza/hornillo sin verificación vigente."
      ],
      "Cálculo": [
        "Curva inconsistente; revisar puntos/ajuste/OMC/MDD.",
        "Cálculo de densidad húmeda/seca o humedad incorrecto."
      ],
      "Reporte": [
        "Faltan datos críticos: volumen del molde, masas, humedad, método, técnico."
      ]
    },

    "Gravedad Especifica": {
      "Método": [
        "Procedimiento no documentado (temperatura, desaireación, tiempos).",
        "No se evidencia corrección por temperatura (si aplica)."
      ],
      "Equipo": [
        "Picnómetro/frasco sin control; balanza sin verificación vigente."
      ],
      "Cálculo": [
        "Cálculo de Gs no verificable (pesos incompletos)."
      ],
      "Reporte": [
        "Registros incompletos: temperaturas/pesos/técnico/equipo."
      ]
    },

    "Castillo de Arena": {
      "Método": [
        "Parámetros del ensayo no documentados (tiempo/condición/criterio interno).",
        "No se evidencia repetibilidad o control interno."
      ],
      "Reporte": [
        "Registro incompleto (muestra, condición, resultado, observaciones)."
      ]
    },

    "Reactividad Acida": {
      "Seguridad": [
        "Gestión de residuos/ácidos sin contención/etiquetado adecuado.",
        "SDS no disponible o no evidenciada durante la actividad."
      ],
      "Método": [
        "Reactivos/tiempos/temperatura no documentados.",
        "No se evidencia control de calidad o blanco (si aplica)."
      ],
      "Equipo": [
        "Equipos de medición sin verificación/calibración vigente (pH/ balanza)."
      ],
      "Reporte": [
        "Registros incompletos: reactivos, lote, tiempos, técnico."
      ]
    },

    "Sanidad/ Sulfatos": {
      "Seguridad": [
        "Almacenamiento/disposición de soluciones sin registro o sin contención adecuada."
      ],
      "Método": [
        "Ciclos/tiempos de inmersión/secado no documentados.",
        "Concentración de solución no documentada."
      ],
      "Equipo": [
        "Balanza sin verificación vigente; hornos sin registro."
      ],
      "Cálculo": [
        "Cálculo de pérdida (%) no verificable por datos incompletos."
      ],
      "Reporte": [
        "Registros incompletos: ciclos, pesos por ciclo, observaciones."
      ]
    },

    "Abrasion/ Maq. Los Angeles": {
      "Método": [
        "Carga/gradación inicial no documentada según método.",
        "Revoluciones/tiempo no documentados."
      ],
      "Equipo": [
        "Máquina sin evidencia de calibración/verificación; conteo de revoluciones no evidenciado."
      ],
      "Cálculo": [
        "Pérdida (%) inconsistente; revisar pesos y tamiz final."
      ],
      "Reporte": [
        "Registros incompletos: masa inicial/final, revoluciones, técnico."
      ]
    },

    "Hidrómetro / Finos": {
      "Método": [
        "Dispersante/tiempos de lectura no documentados.",
        "Correcciones (temperatura/menisco) no aplicadas o no documentadas."
      ],
      "Equipo": [
        "Hidrómetro/termómetro sin verificación; cilindro sin control."
      ],
      "Cálculo": [
        "Curva combinada (tamices + hidrómetro) inconsistente; revisar empalme y % finos."
      ],
      "Reporte": [
        "Faltan lecturas/tiempos/temperatura en registros."
      ]
    },

    "Concreto / Cilindros": {
      "Método": [
        "Curado/identificación de cilindros no conforme; trazabilidad incompleta.",
        "Velocidad de carga fuera de criterio o no documentada."
      ],
      "Equipo": [
        "Máquina de compresión sin calibración vigente / certificado no disponible."
      ],
      "Cálculo": [
        "Resistencia calculada inconsistente con carga/área; revisar unidades."
      ],
      "Reporte": [
        "Faltan datos: edad, dimensiones, carga máxima, modo de falla, técnico."
      ]
    },

    "Gestión de Equipos y Calibraciones": {
      "Equipo": [
        "Calibración vencida; requiere retirar de servicio o justificar con verificación intermedia.",
        "Certificado de calibración no disponible o no trazable (lab/fecha/serie).",
        "Registro de mantenimiento preventivo incompleto."
      ],
      "Reporte": [
        "Matriz de equipos/calibraciones no actualizada."
      ],
      "Buenas": [
        "Buena práctica: control documental de calibración al día y verificaciones registradas."
      ]
    },

    "Control Documental": {
      "Reporte": [
        "Reporte sin firma de revisión/aprobación según flujo establecido.",
        "Campos críticos incompletos: método, fecha, condición, unidades, observaciones.",
        "Control de versiones del formato no documentado."
      ],
      "Trazabilidad": [
        "No se evidencia enlace entre reporte y requisición/registro/ensayo (IDs)."
      ]
    },

    "Seguridad": {
      "Seguridad": [
        "Gestión de residuos sin contención/etiquetado adecuado; requiere acción correctiva.",
        "Uso de químicos sin registro o sin hoja de seguridad accesible (SDS).",
        "PPE no evidenciado en actividad crítica; riesgo de seguridad."
      ],
      "Buenas": [
        "Buena práctica: residuos controlados, etiquetados y disposición documentada."
      ]
    },

    "Buenas": {
      "Buenas": [
        "Buena práctica: trazabilidad completa desde requisición hasta reporte final.",
        "Buena práctica: registros legibles, completos y verificables.",
        "Buena práctica: orden/limpieza y segregación de muestras adecuada.",
        "Buena práctica: verificación de equipos documentada y vigente."
      ]
    }
  };

  // ===========================
  // Helpers UI / Badges
  // ===========================
  function sevClass(sev){
    if(sev === 'Critical') return 'bg-danger';
    if(sev === 'Major') return 'bg-warning text-dark';
    return 'bg-success';
  }
  function statusClass(st){
    return (st === 'Closed') ? 'bg-secondary' : 'bg-primary';
  }

  // ===========================
// IDs únicos para Accordion (Bootstrap)
// ===========================
function uid(){
  return 'u' + Date.now().toString(36) + Math.random().toString(36).slice(2,7);
}

function resetAccordionIds(card, key){
  const heading  = card.querySelector('.accordion-header');
  const btn      = card.querySelector('.accordion-button');
  const collapse = card.querySelector('.accordion-collapse');

  const hid = `heading-${key}`;
  const cid = `collapse-${key}`;

  if (heading) heading.id = hid;

  if (btn) {
    btn.setAttribute('data-bs-target', `#${cid}`);
    btn.setAttribute('aria-controls', cid);
  }

  if (collapse) {
    collapse.id = cid;
    collapse.setAttribute('aria-labelledby', hid);
    // mantener comportamiento accordion
    collapse.setAttribute('data-bs-parent', '#hallazgosAccordion');
  }
}


  function renumber(){
    const items = document.querySelectorAll('.hallazgo-item');
    items.forEach((item, i) => {
      const badge = item.querySelector('.accordion-button .badge.bg-secondary');
      if(badge) badge.textContent = '#' + (i+1);
    });
  }

  function updateHeaderText(card){
    const desc = card.querySelector('.jsDesc');
    const header = card.querySelector('.jsHeaderText');
    if(desc && header){
      const text = (desc.value || '').trim();
      header.textContent = text ? (text.length > 70 ? text.slice(0,70)+'...' : text) : '— sin descripción —';
    }
  }

  function applyBadges(card){
    const sevSel = card.querySelector('.jsSeverity');
    const stSel  = card.querySelector('.jsStatus');

    const sevBadge = card.querySelector('.jsBadgeSeverity');
    const stBadge  = card.querySelector('.jsBadgeStatus');

    if(sevSel && sevBadge){
      sevBadge.className = 'badge me-1 jsBadgeSeverity ' + sevClass(sevSel.value);
      sevBadge.textContent = sevSel.value;
    }
    if(stSel && stBadge){
      stBadge.className = 'badge jsBadgeStatus ' + statusClass(stSel.value);
      stBadge.textContent = stSel.value;
    }
  }

  function updateSummary(){
    const cards = document.querySelectorAll('.hallazgo-item');
    let total = cards.length;
    let open = 0, closed = 0, ncr = 0;

    cards.forEach(c=>{
      const type = c.querySelector('.jsType')?.value || '';
      const st = c.querySelector('.jsStatus')?.value || 'Open';
      if(st === 'Closed') closed++; else open++;
      if(type === 'NCR') ncr++;
    });

    document.getElementById('sumTotal').textContent = total;
    document.getElementById('sumOpen').textContent = open;
    document.getElementById('sumClosed').textContent = closed;
    document.getElementById('sumNCR').textContent = ncr;
  }

  /* =========================================================
     Plantillas dinámicas
  ========================================================= */
  function getTemplates(ensayo, isoCat, searchText = "") {
    const out = [];
    const s = (searchText || "").toLowerCase().trim();

    // Ensayo específico
    const block1 = TEMPLATE_LIB[ensayo] || {};
    const arr1 = block1[isoCat] || [];
    arr1.forEach(t => out.push(t));

    // Fallback General
    const blockG = TEMPLATE_LIB.General || {};
    const arrG = blockG[isoCat] || [];
    arrG.forEach(t => out.push(t));

    if (!s) return out;
    return out.filter(t => t.toLowerCase().includes(s));
  }

  function fillTemplateSelect(selectEl, items) {
    selectEl.innerHTML = `<option value="">-- Seleccionar plantilla --</option>`;
    if(!items.length){
      const opt = document.createElement("option");
      opt.value = "";
      opt.textContent = "No hay plantillas para esta combinación";
      selectEl.appendChild(opt);
      return;
    }
    items.forEach((txt, idx) => {
      const opt = document.createElement("option");
      opt.value = txt;
      opt.textContent = (idx + 1) + ". " + (txt.length > 110 ? (txt.slice(0, 110) + "...") : txt);
      selectEl.appendChild(opt);
    });
  }

  function initHallazgoCard(card){
    const ensayoSel = card.querySelector(".ensayoSelect");
    const isoSel    = card.querySelector(".isoSelect");
    const plantSel  = card.querySelector(".plantillaSelect");
    const searchInp = card.querySelector(".plantillaSearch");

    if(!ensayoSel || !isoSel || !plantSel) return;

    const ensayo = ensayoSel.value || "General";
    const isoCat = isoSel.value || "Trazabilidad";
    const search = searchInp ? (searchInp.value || "") : "";

    const items = getTemplates(ensayo, isoCat, search);
    fillTemplateSelect(plantSel, items);
  }

  // ===========================
  // Crear nuevo hallazgo (accordion item)
  // ===========================
  function newHallazgoHTML(idx){
    return `
      <div class="accordion-item hallazgo-item border-0 shadow-sm mb-2" data-finding-id="0">
        <h2 class="accordion-header" id="heading-new-${idx}">
          <button class="accordion-button d-flex align-items-center gap-2" type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapse-new-${idx}"
                  aria-expanded="true"
                  aria-controls="collapse-new-${idx}">
            <span class="badge bg-secondary me-1">#${idx}</span>
            <span class="me-2 fw-semibold">NCR</span>
            <span class="badge me-1 jsBadgeSeverity bg-success">Minor</span>
            <span class="badge jsBadgeStatus bg-primary">Open</span>
            <span class="ms-auto text-muted small jsHeaderText">— sin descripción —</span>
          </button>
        </h2>

        <div id="collapse-new-${idx}" class="accordion-collapse collapse show"
             aria-labelledby="heading-new-${idx}">
          <div class="accordion-body">

            <input type="hidden" name="finding_id[]" value="0">

            <div class="row g-2 align-items-end">
              <div class="col-md-3">
                <label class="form-label form-label-sm">Tipo</label>
                <select name="finding_type[]" class="form-select form-select-sm jsType">
                  <option value="NCR" selected>NCR</option>
                  <option value="Observación">Observación</option>
                  <option value="Oportunidad">Oportunidad</option>
                  <option value="Buena práctica">Buena práctica</option>
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label form-label-sm">Categoría</label>
                <input type="text" name="category[]" class="form-control form-control-sm jsCategory"
                      list="areas_sugeridas"
                      placeholder="Granulometría, Doc Control...">
              </div>

              <div class="col-md-2">
                <label class="form-label form-label-sm">Severidad</label>
                <select name="severity_item[]" class="form-select form-select-sm jsSeverity">
                  <option value="Minor" selected>Minor</option>
                  <option value="Major">Major</option>
                  <option value="Critical">Critical</option>
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label form-label-sm">Estado</label>
                <select name="status_item[]" class="form-select form-select-sm jsStatus">
                  <option value="Open" selected>Open</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>

              <div class="col-md-2 text-end">
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-secondary jsMoveUp" title="Subir">
                    <i class="bi bi-arrow-up"></i>
                  </button>
                  <button type="button" class="btn btn-outline-secondary jsMoveDown" title="Bajar">
                    <i class="bi bi-arrow-down"></i>
                  </button>
                  <button type="button" class="btn btn-outline-primary jsDuplicate" title="Duplicar">
                    <i class="bi bi-files"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger jsRemove" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>

              <div class="col-md-3 mt-2">
                <label class="form-label form-label-sm">Ensayo / Proceso</label>
                <select class="form-select form-select-sm ensayoSelect">
                  <option value="General" selected>General</option>
                  <option value="Trazabilidad">Trazabilidad</option>
                  <option value="Preparación">Preparación de muestras</option>
                  <option value="Contenido de humedad">Contenido de humedad</option>
                  <option value="Granulometría">Granulometría</option>
                  <option value="Atterberg">Atterberg</option>
                  <option value="Proctor">Proctor</option>
                  <option value="Gravedad Especifica">Gravedad Específica</option>
                  <option value="Castillo de Arena">Castillo de Arena</option>
                  <option value="Reactividad Acida">Reactividad Ácida</option>
                  <option value="Sanidad/ Sulfatos">Sanidad / Sulfatos</option>
                  <option value="Abrasion/ Maq. Los Angeles">Abrasión / Los Ángeles</option>
                  <option value="Hidrómetro / Finos">Hidrómetro / Finos</option>
                  <option value="Concreto / Cilindros">Concreto / Cilindros</option>
                  <option value="Gestión de Equipos y Calibraciones">Equipos / Calibraciones</option>
                  <option value="Control Documental">Control Documental</option>
                  <option value="Seguridad">Seguridad / Residuos / Químicos</option>
                  <option value="Buenas">Buenas prácticas</option>
                </select>
              </div>

              <div class="col-md-3 mt-2">
                <label class="form-label form-label-sm">Categoría ISO</label>
                <select class="form-select form-select-sm isoSelect">
                  <option value="Trazabilidad" selected>Trazabilidad</option>
                  <option value="Muestra">Muestra</option>
                  <option value="Preparación">Preparación</option>
                  <option value="Método">Método / Procedimiento</option>
                  <option value="Equipo">Equipo / Calibración</option>
                  <option value="Ambiente">Ambiente / Condiciones</option>
                  <option value="Cálculo">Cálculo / Resultados</option>
                  <option value="Reporte">Reporte / Registros</option>
                  <option value="Competencia">Competencia / Entrenamiento</option>
                  <option value="Seguridad">Seguridad / Químicos</option>
                  <option value="Buenas">Buenas prácticas</option>
                </select>
              </div>

              <div class="col-md-4 mt-2">
                <label class="form-label form-label-sm">Plantillas</label>
                <select class="form-select form-select-sm plantillaSelect">
                  <option value="">-- Seleccionar plantilla --</option>
                </select>
                <small class="text-muted">Se llena según Ensayo + Categoría ISO.</small>
              </div>

              <div class="col-md-2 mt-2">
                <label class="form-label form-label-sm">Buscar</label>
                <input type="text" class="form-control form-control-sm plantillaSearch"
                       placeholder="Buscar...">
              </div>

              <div class="col-12 mt-2">
                <label class="form-label form-label-sm">Descripción breve del hallazgo</label>
                <textarea name="description[]" rows="3" class="form-control form-control-sm jsDesc"></textarea>
                <div class="text-muted small mt-1">
                  Tip: describe <b>qué pasó</b>, <b>evidencia</b>, <b>impacto</b> y <b>acción sugerida</b>.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    `;
  }

  // ===========================
  // Init existing hallazgos
  // ===========================
  document.querySelectorAll('.hallazgo-item').forEach(card=>{
    applyBadges(card);
    updateHeaderText(card);
    initHallazgoCard(card); // ✅ cargar plantillas dinámicas
  });

  // ✅ asegurar IDs únicos en hallazgos existentes
document.querySelectorAll('.hallazgo-item').forEach(card=>{
  resetAccordionIds(card, uid());
});

  renumber();
  updateSummary();


  // ===========================
  // Add hallazgo
  // ===========================
  document.getElementById('addHallazgoBtn').addEventListener('click', function(){
  const acc = document.getElementById('hallazgosAccordion');
  const count = document.querySelectorAll('.hallazgo-item').length + 1;

  acc.insertAdjacentHTML('beforeend', newHallazgoHTML(count));
  const last = acc.lastElementChild;

  // ✅ evitar IDs duplicados
  resetAccordionIds(last, uid());

  applyBadges(last);
  updateHeaderText(last);
  initHallazgoCard(last);
  renumber();
  updateSummary();
});


  // ===========================
  // Delegación de eventos
  // ===========================
  document.getElementById('hallazgosContainer').addEventListener('click', function(e){
    const card = e.target.closest('.hallazgo-item');
    if(!card) return;

    if(e.target.closest('.jsRemove')){
      if(confirm('¿Eliminar este hallazgo?')){
        card.remove();
        renumber();
        updateSummary();
      }
      return;
    }

    if(e.target.closest('.jsDuplicate')){
  const acc = document.getElementById('hallazgosAccordion');
  const clone = card.cloneNode(true);

  // finding_id -> 0 (nuevo)
  const hid = clone.querySelector('input[name="finding_id[]"]');
  if (hid) hid.value = 0;
  clone.setAttribute('data-finding-id', '0');

  // ✅ IDs únicos para no romper Bootstrap
  resetAccordionIds(clone, uid());

  // ✅ dejar abierto el nuevo (opcional)
  const collapse = clone.querySelector('.accordion-collapse');
  const btn = clone.querySelector('.accordion-button');
  if (collapse) collapse.classList.add('show');
  if (btn) {
    btn.classList.remove('collapsed');
    btn.setAttribute('aria-expanded','true');
  }

  acc.insertBefore(clone, card.nextSibling);

  applyBadges(clone);
  updateHeaderText(clone);
  initHallazgoCard(clone);
  renumber();
  updateSummary();
  return;
}


    if(e.target.closest('.jsMoveUp')){
      const prev = card.previousElementSibling;
      if(prev) prev.before(card);
      renumber();
      updateSummary();
      return;
    }

    if(e.target.closest('.jsMoveDown')){
      const next = card.nextElementSibling;
      if(next) next.after(card);
      renumber();
      updateSummary();
      return;
    }
  });

  document.getElementById('hallazgosContainer').addEventListener('change', function(e){
    const card = e.target.closest('.hallazgo-item');
    if(!card) return;

    // Plantillas: insertar texto
    if(e.target.classList.contains('plantillaSelect')){
      const value = e.target.value;
      if(!value) return;

      const textarea = card.querySelector('.jsDesc');
      if(textarea){
        if(!textarea.value.trim()){
          textarea.value = value;
        } else {
          textarea.value = textarea.value.trim() + "\n- " + value;
        }
      }

      // Autocompletar "Categoría" si está vacío (opcional)
      const catInput = card.querySelector('.jsCategory');
      const isoSel = card.querySelector('.isoSelect');
      if(catInput && isoSel && !catInput.value.trim()){
        catInput.value = isoSel.value;
      }

      e.target.value = "";
      updateHeaderText(card);
      return;
    }

    if(e.target.classList.contains('jsSeverity') || e.target.classList.contains('jsStatus')){
  applyBadges(card);
  updateSummary();

  // ✅ NUEVO: si cierran el hallazgo, quitar acciones asociadas "NCR #n"
  if(e.target.classList.contains('jsStatus')){
    const st = e.target.value || 'Open';
    if(st === 'Closed'){
      const badge = card.querySelector('.accordion-button .badge.bg-secondary');
      const nTxt = (badge?.textContent || '').replace('#','').trim();
      const n = parseInt(nTxt, 10);

      if(Number.isFinite(n)){
        const ref = `NCR #${n}`;
        const body = document.getElementById('accionesBody');

        // borra las acciones de ese hallazgo
        [...body.querySelectorAll('tr')].forEach(tr => {
          const inp = tr.querySelector('input[name="hallazgo_ref[]"]');
          if(inp && (inp.value || '').trim() === ref){
            tr.remove();
          }
        });

        // reenumera tabla acciones
        [...body.querySelectorAll('tr')].forEach((tr, idx) => {
          tr.children[0].textContent = idx + 1;
        });
      }
    }
  }

  return;
}

    // Cambia tipo
    if(e.target.classList.contains('jsType')){
      updateSummary();
      return;
    }

    // Cambia Ensayo o ISO: refresca plantillas
    if(e.target.classList.contains('ensayoSelect') || e.target.classList.contains('isoSelect')){
      initHallazgoCard(card);
      return;
    }
  });

  // Search plantillas + header desc
  document.getElementById('hallazgosContainer').addEventListener('input', function(e){
    const card = e.target.closest('.hallazgo-item');
    if(!card) return;

    if(e.target.classList.contains('jsDesc')){
      updateHeaderText(card);
      return;
    }

    if(e.target.classList.contains('plantillaSearch')){
      initHallazgoCard(card);
      return;
    }
  });

})();

(function(){
  const body   = document.getElementById('accionesBody');
  const btnAdd = document.getElementById('addAccBtn');

  // ===========================
  // Helpers Hallazgos
  // ===========================
  function getHallazgoCardFromCollapse(collapse){
    return collapse ? collapse.closest('.hallazgo-item') : null;
  }

  function getHallazgoNumberFromCard(card){
    const badge = card?.querySelector('.accordion-button .badge.bg-secondary');
    if(!badge) return null;
    const t = (badge.textContent || '').replace('#','').trim();
    const n = parseInt(t, 10);
    return Number.isFinite(n) ? n : null;
  }

  function getHallazgoType(card){
    return card?.querySelector('.jsType')?.value || '';
  }

  function isHallazgoOpen(card){
    const st = card?.querySelector('.jsStatus')?.value || 'Open';
    return st === 'Open';
  }

  function canHaveAction(card){
    const type = getHallazgoType(card);
    return type !== 'Buena práctica';
  }

  function getRefPrefix(type){
    if(type === 'Buena práctica') return 'BP';
    if(type === 'Observación') return 'OBS';
    if(type === 'Oportunidad') return 'OPP';
    return 'NCR';
  }

  function getActiveHallazgoCard(){
    // 1) Si hay abierto, usarlo
    const openCollapse = document.querySelector('#hallazgosAccordion .accordion-collapse.show');
    if(openCollapse){
      const card = getHallazgoCardFromCollapse(openCollapse);
      if(card && isHallazgoOpen(card)) return card;
    }

    // 2) Si no, buscar el último Open
    const cards = [...document.querySelectorAll('.hallazgo-item')];
    for(let i = cards.length - 1; i >= 0; i--){
      if(isHallazgoOpen(cards[i])) return cards[i];
    }

    return null;
  }

  // ===========================
  // Acciones helpers
  // ===========================
  function renumberAccionesTable(){
    [...body.querySelectorAll('tr')].forEach((tr, idx) => {
      tr.children[0].textContent = idx + 1;
    });
  }

  function makeAccRow(nextIndex, hallazgoRefDefault){
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="text-center">${nextIndex}</td>
      <td>
        <input type="hidden" name="accion_id[]" value="0">
        <input type="text" name="hallazgo_ref[]" class="form-control form-control-sm"
               value="${hallazgoRefDefault}">
      </td>
      <td>
        <textarea name="accion[]" rows="2" class="form-control form-control-sm" required></textarea>
      </td>
      <td>
        <input type="text" name="responsable[]" class="form-control form-control-sm">
      </td>
      <td>
        <input type="date" name="fecha_compromiso[]" class="form-control form-control-sm">
      </td>
      <td>
        <select name="accion_status[]" class="form-select form-select-sm">
          <option value="Open" selected>Open</option>
          <option value="In Progress">In Progress</option>
          <option value="Closed">Closed</option>
        </select>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger jsRemoveAcc">Quitar</button>
      </td>
    `;
    return tr;
  }

  // ===========================
  // ADD ACCIÓN (control total)
  // ===========================
  btnAdd.addEventListener('click', () => {
    const card = getActiveHallazgoCard();
    if(!card){
      alert('No hay hallazgos Open. Crea o abre un hallazgo primero.');
      return;
    }

    if(!canHaveAction(card)){
      alert('Las Buenas Prácticas no requieren acciones.');
      return;
    }

    const n = getHallazgoNumberFromCard(card);
    if(!n){
      alert('No se pudo determinar el número del hallazgo.');
      return;
    }

    const type   = getHallazgoType(card);
    const prefix = getRefPrefix(type);
    const ref    = `${prefix} #${n}`;

    const idx = body.querySelectorAll('tr').length + 1;
    body.appendChild(makeAccRow(idx, ref));
  });

  // ===========================
  // REMOVE ACCIÓN
  // ===========================
  body.addEventListener('click', (e) => {
    if(e.target.classList.contains('jsRemoveAcc')){
      e.target.closest('tr').remove();
      renumberAccionesTable();
    }
  });

})();



</script>

<?php include_once('../components/footer.php'); ?>