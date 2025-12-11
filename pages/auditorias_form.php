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
  }
}

// Generar código por defecto si es nuevo
if (!$audit) {
  $year  = date('Y');
  $code  = "AUD-{$year}-";
  // Aquí podrías buscar el consecutivo real; por ahora 001
  $code .= str_pad("1", 3, "0", STR_PAD_LEFT);
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

      <form method="post" action="auditorias_save.php" id="formAuditoria">

        <input type="hidden" name="id" value="<?php echo $audit['id'] ?? ''; ?>">

        <!-- BLOQUE 1: DATOS BÁSICOS -->
        <div class="row g-3 mb-3">

          <div class="col-md-3">
            <label class="form-label">Código de Auditoría</label>
            <input type="text" name="Audit_Code" class="form-control"
                   required
                   value="<?php echo htmlentities($audit['Audit_Code'] ?? $code); ?>">
            <small class="text-muted">Ej: AUD-2025-003.</small>
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
                $tipos = ['Interna','Externa','Cruzada','Cliente','Certificación'];
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
              <option value="Preparación de Muestras">
              <option value="Granulometría">
              <option value="Atterberg / Clasificación">
              <option value="Proctor & Densidades de Campo">
              <option value="CBR / Ensayos de Resistencia">
              <option value="Concreto / Cylinders">
              <option value="Hidrómetro / Fines">
              <option value="Control Documental">
              <option value="Gestión de Equipos y Calibraciones">
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
        <h5 class="mt-3">Hallazgos de la Auditoría</h5>
        <p class="text-muted small mb-2">
          Agrega NCR, observaciones, oportunidades de mejora o buenas prácticas.  
          Cada hallazgo tiene tipo, categoría, severidad y estado. Esto es lo que hace moderno el módulo 
        </p>

        <div id="hallazgosContainer">
          <?php if (!empty($hallazgos)): ?>
            <?php foreach ($hallazgos as $h): ?>
              <div class="card mb-2 hallazgo-item">
                <div class="card-body">
                  <div class="row g-2 align-items-start">
                    <div class="col-md-2">
                      <label class="form-label form-label-sm">Tipo</label>
                      <select name="finding_type[]" class="form-select form-select-sm">
                        <?php
                          $tiposH = ['NCR','Observación','Oportunidad','Buena práctica'];
                          foreach ($tiposH as $tH):
                        ?>
                          <option value="<?php echo $tH; ?>"
                            <?php if($h['finding_type']==$tH) echo 'selected'; ?>>
                            <?php echo $tH; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label class="form-label form-label-sm">Categoría</label>
                      <input type="text" name="category[]" class="form-control form-control-sm"
                             list="areas_sugeridas"
                             value="<?php echo htmlentities($h['category']); ?>">
                    </div>

                    <div class="col-md-2">
                      <label class="form-label form-label-sm">Severidad</label>
                      <select name="severity_item[]" class="form-select form-select-sm">
                        <?php
                          $sevsH = ['Minor','Major','Critical'];
                          foreach ($sevsH as $sH):
                        ?>
                          <option value="<?php echo $sH; ?>"
                            <?php if($h['severity']==$sH) echo 'selected'; ?>>
                            <?php echo $sH; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label class="form-label form-label-sm">Estado</label>
                      <select name="status_item[]" class="form-select form-select-sm">
                        <option value="Open"   <?php if($h['status']=='Open') echo 'selected'; ?>>Open</option>
                        <option value="Closed" <?php if($h['status']=='Closed') echo 'selected'; ?>>Closed</option>
                      </select>
                    </div>

                    <div class="col-md-2">
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

                    <div class="col-md-2 text-end">
                      <label class="form-label form-label-sm d-block">&nbsp;</label>
                      <button type="button" class="btn btn-sm btn-outline-danger removeHallazgo">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>

                    <div class="col-12 mt-2">
                      <label class="form-label form-label-sm">Descripción breve del hallazgo</label>
                      <textarea name="description[]" rows="2"
                                class="form-control form-control-sm"><?php
                        echo htmlentities($h['description']);
                      ?></textarea>
                    </div>

                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addHallazgoBtn">
          <i class="bi bi-plus-circle"></i> Agregar hallazgo
        </button>

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
          <a href="auditorias_list.php" class="btn btn-secondary">
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
// Template HTML para un nuevo hallazgo
function createHallazgoCard() {
  return `
  <div class="card mb-2 hallazgo-item">
    <div class="card-body">
      <div class="row g-2 align-items-start">
        <div class="col-md-2">
          <label class="form-label form-label-sm">Tipo</label>
          <select name="finding_type[]" class="form-select form-select-sm">
            <option value="NCR">NCR</option>
            <option value="Observación">Observación</option>
            <option value="Oportunidad">Oportunidad</option>
            <option value="Buena práctica">Buena práctica</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label form-label-sm">Categoría</label>
          <input type="text" name="category[]" class="form-control form-control-sm"
                 list="areas_sugeridas"
                 placeholder="Granulometría, Doc Control...">
        </div>

        <div class="col-md-2">
          <label class="form-label form-label-sm">Severidad</label>
          <select name="severity_item[]" class="form-select form-select-sm">
            <option value="Minor">Minor</option>
            <option value="Major">Major</option>
            <option value="Critical">Critical</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label form-label-sm">Estado</label>
          <select name="status_item[]" class="form-select form-select-sm">
            <option value="Open">Open</option>
            <option value="Closed">Closed</option>
          </select>
        </div>

        <div class="col-md-2">
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

        <div class="col-md-2 text-end">
          <label class="form-label form-label-sm d-block">&nbsp;</label>
          <button type="button" class="btn btn-sm btn-outline-danger removeHallazgo">
            <i class="bi bi-trash"></i>
          </button>
        </div>

        <div class="col-12 mt-2">
          <label class="form-label form-label-sm">Descripción breve del hallazgo</label>
          <textarea name="description[]" rows="2"
                    class="form-control form-control-sm"></textarea>
        </div>

      </div>
    </div>
  </div>`;
}

document.getElementById('addHallazgoBtn').addEventListener('click', function() {
  const container = document.getElementById('hallazgosContainer');
  container.insertAdjacentHTML('beforeend', createHallazgoCard());
});

// Delegación de eventos para eliminar y plantillas
document.getElementById('hallazgosContainer').addEventListener('click', function(e) {
  if (e.target.closest('.removeHallazgo')) {
    const card = e.target.closest('.hallazgo-item');
    if (card) card.remove();
  }
});

document.getElementById('hallazgosContainer').addEventListener('change', function(e) {
  if (e.target.classList.contains('plantillaSelect')) {
    const value = e.target.value;
    if (!value) return;
    const card = e.target.closest('.hallazgo-item');
    if (!card) return;
    const textarea = card.querySelector('textarea[name="description[]"]');
    if (textarea && !textarea.value) {
      textarea.value = value;
    } else if (textarea) {
      textarea.value = textarea.value + (textarea.value.trim() ? ' ' : '') + value;
    }
    e.target.value = "";
  }
});
</script>

<?php include_once('../components/footer.php'); ?>
