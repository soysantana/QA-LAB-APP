<?php
// pages/docs_list.php
$page_title = 'Documentos';
require_once('../config/load.php');
page_require_level(2);
if (session_status() === PHP_SESSION_NONE) session_start();

// utilidades
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function normalize_web_path_inline(string $p): string {
  $p = trim($p);
  $p = str_replace('\\','/',$p);
  $p = preg_replace('#/+#','/',$p);
  $p = rtrim($p, " \t\n\r\0\x0B+");
  if ($p !== '' && $p[0] !== '/') $p = '/'.$p;
  return $p;
}

/** ---- tu lógica de filtro/consulta (SEMANA + LIKE ESCAPE '!') ---- */
$q = trim($_GET['q'] ?? '');
$q_sql = $db->escape($q);
$q_sql = str_replace(['!', '%', '_'], ['!!','!%','!_'], $q_sql);
$like  = "%{$q_sql}%";

$weekExpr = "YEARWEEK(CURDATE(), 1)";
$inWeek   = "(YEARWEEK(created_at, 1) = {$weekExpr} OR YEARWEEK(signed_at, 1) = {$weekExpr})";

$searchExpr = ($q !== '')
  ? " ( COALESCE(sample_id,'')     LIKE '{$like}' ESCAPE '!'
     OR COALESCE(sample_number,'') LIKE '{$like}' ESCAPE '!'
     OR COALESCE(test_type,'')     LIKE '{$like}' ESCAPE '!' ) "
  : " 1=1 ";

$sql = "
  SELECT * FROM doc_files
   WHERE {$inWeek}
     AND {$searchExpr}
   ORDER BY created_at DESC
   LIMIT 300";

$rows = find_by_sql($sql) ?: [];
$results_count = count($rows);

/** --- función render_rows(...) tal como la tienes --- */

// === AJAX EARLY-RETURN (ANTES de imprimir cualquier HTML) ===
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
  header('Content-Type: text/html; charset=utf-8');
  echo render_rows($rows);
  exit;
}

// A PARTIR DE AQUÍ YA PUEDES IMPRIMIR HTML
include_once('../components/header.php');
$today = date('Y-m-d');

/**
 * Filtro de semana actual:
 * Usamos YEARWEEK(fecha, 1) (modo ISO, semana inicia lunes) para:
 * - created_at EN esta semana
 *   O
 * - signed_at EN esta semana
 */

// ===== Búsqueda semana actual + LIKE robusto (ESCAPE '!') =====
$q = trim($_GET['q'] ?? '');
$q_sql = $db->escape($q);
// Escapa el carácter de escape (!) y luego los comodines % y _
$q_sql = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q_sql);
$like  = "%{$q_sql}%";  // se usará con ESCAPE '!'

// Semana ISO (lunes-domingo). Cambia el "1" por "0" si prefieres domingo-sábado.
$weekExpr = "YEARWEEK(CURDATE(), 1)";
$inWeek   = "(YEARWEEK(created_at, 1) = {$weekExpr} OR YEARWEEK(signed_at, 1) = {$weekExpr})";

$searchExpr = ($q !== '')
  ? " ( COALESCE(sample_id,'')     LIKE '{$like}' ESCAPE '!'
     OR COALESCE(sample_number,'') LIKE '{$like}' ESCAPE '!'
     OR COALESCE(test_type,'')     LIKE '{$like}' ESCAPE '!' ) "
  : " 1=1 ";

$sql = "
  SELECT * FROM doc_files
   WHERE {$inWeek}
     AND {$searchExpr}
   ORDER BY created_at DESC
   LIMIT 300";

$rows = find_by_sql($sql) ?: [];
$results_count = count($rows);

// --- render filas (reutilizable para AJAX) ---
function render_rows(array $rows) {
  ob_start();
  if (empty($rows)) { ?>
    <tr><td colspan="7" class="text-center text-muted py-4">No hay documentos de esta semana.</td></tr>
  <?php } else {
    foreach ($rows as $r):
      $badge = ($r['status'] === 'signed') ? 'success' : 'warning';
      $statusText = ($r['status'] === 'signed') ? 'Firmado' : 'En espera';

      $file_path = $r['file_path'] ?? '';
      $file_name = $r['file_name'] ?? ($file_path ? basename(str_replace('\\','/',$file_path)) : 'documento.pdf');

      $fp_sane = normalize_web_path_inline($file_path);
      $viewUrlById   = '/pages/serve_pdf.php?' . http_build_query(['id' => (int)$r['id']]);
      $viewUrlByPath = '/pages/serve_pdf.php?' . http_build_query(['path' => $fp_sane]);
      $viewUrl = !empty($fp_sane) ? $viewUrlByPath : $viewUrlById;

      // created_at → YYYY-MM-DD
      $createdFmt = '';
      if (!empty($r['created_at'])) {
        $t2 = strtotime($r['created_at']);
        $createdFmt = $t2 ? date('Y-m-d', $t2) : $r['created_at'];
      }

      // Signed By (oculta “Sistema”) y Signed At → YYYY-MM-DD
      $signedByRaw = $r['signed_by'] ?? '';
      $showSignedBy = ($signedByRaw !== '' && strcasecmp($signedByRaw, 'Sistema') !== 0);
      $signedAtRaw = $r['signed_at'] ?? '';
      $signedAtFmt = '';
      if ($signedAtRaw) {
        $ts = strtotime($signedAtRaw);
        $signedAtFmt = $ts ? date('Y-m-d', $ts) : $signedAtRaw;
      }
?>
      <tr>
        <td class="text-nowrap"><?= e($createdFmt) ?></td>
        <td class="text-nowrap"><?= e(($r['sample_id'] ?? '').' / '.($r['sample_number'] ?? '')) ?></td>
        <td class="text-nowrap"><?= e($r['test_type'] ?? '') ?></td>
        <td class="text-nowrap"><?= e($r['template'] ?? '') ?></td>
        <td class="text-nowrap">v<?= (int)($r['version'] ?? 1) ?></td>
        <td>
          <span class="badge bg-<?= $badge ?>"><?= e($statusText) ?></span>
          <?php if ($showSignedBy || $signedAtFmt): ?>
            <div class="small text-muted">
              <?php if ($showSignedBy): ?>
                <i class="bi bi-check2-circle"></i> <?= e($signedByRaw) ?>
              <?php endif; ?>
              <?php if ($signedAtFmt): ?>
                <span class="text-nowrap"><?= $showSignedBy ? ' — ' : '' ?><?= e($signedAtFmt) ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </td>
        <td>
          <div class="d-flex flex-wrap gap-2">
            <?php if (!empty($file_path)): ?>
              <a class="btn btn-outline-primary btn-sm btn-colocar"
                 data-bs-toggle="modal"
                 data-bs-target="#pdfPlaceModal"
                 data-doc-id="<?= (int)$r['id'] ?>"
                 data-view-url="<?= e($viewUrl) ?>"
                 data-file-name="<?= e($file_name) ?>">
                Firmar Resultado
              </a>
              <a class="btn btn-outline-secondary btn-sm"
                 href="<?= e($viewUrlById) ?>"
                 target="_blank" rel="noopener">
                Ver Archivo PDF
              </a>
            <?php else: ?>
              <span class="text-muted">Sin archivo</span>
            <?php endif; ?>

            <?php if (($r['status'] ?? '') !== 'signed'): ?>
              <form action="/database/doc_mark_signed.php" method="post" class="m-0">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-success btn-sm">
                  <i class="bi bi-pen"></i> Marcar firmado
                </button>
              </form>
            <?php endif; ?>

            <form action="/database/doc_delete.php" method="post" class="m-0"
                  onsubmit="return confirm('¿Eliminar este PDF? Esta acción no se puede deshacer.');">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </form>
          </div>
        </td>
      </tr>
<?php
    endforeach;
  }
  return ob_get_clean();
}

// AJAX: devolver solo filas
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
  header('Content-Type: text/html; charset=utf-8');
  echo render_rows($rows);
  exit;
}

$today = date('Y-m-d');
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Documentos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item">Documentos</li>
        <li class="breadcrumb-item active">Listado</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <?php echo display_msg($msg); ?>

        <div class="card">
          <div class="card-header py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
              <h5 class="mb-0">Semana actual</h5>
              <?php if ($q !== ''): ?>
                <span class="badge bg-secondary ms-1">Filtro: <?= e($q) ?> (<?= (int)$results_count ?>)</span>
              <?php endif; ?>
            </div>

            <div class="toolbar d-flex flex-wrap align-items-center gap-2">
              <!-- Búsqueda en vivo (pill) -->
              <div class="input-group input-group-sm search-pill">
                <span class="input-group-text border-0 bg-transparent ps-2"><i class="bi bi-search"></i></span>
                <input id="qLive" type="search" class="form-control border-0"
                       value="<?= e($q) ?>" placeholder="Sample ID / Number / Test Type">
                <button class="btn btn-light btn-sm border-0" type="button" id="btnClearLive" title="Limpiar">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>

              <!-- Descarga (dropdown + fecha) -->
              <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-download"></i> Descargar
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" id="dlCreated"
                         href="/database/docs_download_day.php?date=<?= e($today) ?>&scope=created">
                         PDFs creados hoy</a></li>
                  <li><a class="dropdown-item" id="dlSigned"
                         href="/database/docs_download_day.php?date=<?= e($today) ?>&scope=signed">
                         PDFs firmados hoy</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li class="px-3 py-2">
                    <label class="form-label small mb-1">Elegir fecha</label>
                    <input type="date" class="form-control form-control-sm" id="dlDate" value="<?= e($today) ?>">
                    <div class="form-text">Ajusta los enlaces de arriba.</div>
                  </li>
                </ul>
              </div>

              <a href="/pages/doc_upload_external.php" class="btn btn-primary btn-sm">
                <i class="bi bi-upload"></i> Subir
              </a>
              <a href="/pages/backup_resultados_firmados.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-archive"></i> Backups
              </a>
            </div>
          </div>

          <style>
            .toolbar .search-pill{
              background: #f1f3f5;
              border-radius: 999px;
              padding-right: .25rem;
            }
            .toolbar .search-pill .form-control{
              background: transparent;
              box-shadow: none !important;
            }
            .toolbar .search-pill .input-group-text{
              background: transparent;
            }
            @media (max-width: 576px){
              .toolbar{ width:100%; }
              .toolbar .search-pill{ flex:1 1 auto; min-width: 240px; }
            }
          </style>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Fecha</th>
                    <th>Sample</th>
                    <th>Test</th>
                    <th>Template</th>
                    <th>Versión</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody id="docsTbody">
                  <?= render_rows($rows) ?>
                </tbody>
              </table>
            </div>
          </div>
        </div> <!-- /.card -->

        <!-- Modal: Colocar textos en PDF -->
        <div class="modal fade" id="pdfPlaceModal" tabindex="-1" aria-labelledby="pdfPlaceLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="pdfPlaceLabel">
                  <i class="bi bi-file-earmark-pdf"></i>
                  <span id="pdfPlaceTitle">Firmar Resultado</span>
                </h5>
                <div class="d-flex align-items-center gap-3 me-2">
                  <a id="openNewTab2" class="btn btn-link btn-sm" target="_blank" rel="noopener">Abrir en nueva pestaña</a>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
              </div>

              <div class="modal-body">
                <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
                  <div>
                    <label class="form-label mb-1">Campo a colocar</label>
                    <select id="fieldKind" class="form-select form-select-sm">
                      <option value="reviewedBy">Reviewed By</option>
                      <option value="reviewedDate">Reviewed Date</option>
                      <option value="approvedBy">Approved By</option>
                      <option value="approvedDate">Approved Date</option>
                    </select>
                  </div>
                  <div>
                    <label class="form-label mb-1">Reviewed By</label>
                    <input id="rvBy" class="form-control form-control-sm" placeholder="Nombre revisor">
                  </div>
                  <div>
                    <label class="form-label mb-1">Reviewed Date</label>
                    <input id="rvDate" type="date" class="form-control form-control-sm">
                  </div>
                  <div>
                    <label class="form-label mb-1">Approved By</label>
                    <input id="apBy" class="form-control form-control-sm" placeholder="Nombre aprobador">
                  </div>
                  <div>
                    <label class="form-label mb-1">Approved Date</label>
                    <input id="apDate" type="date" class="form-control form-control-sm">
                  </div>

                  <div class="ms-auto d-flex gap-2">
                    <button id="btnClearMarks" class="btn btn-outline-secondary btn-sm">Limpiar marcas</button>
                    <button id="btnInsertUpload" class="btn btn-primary btn-sm">Insertar y subir</button>
                    <button id="btnInsertDownload" class="btn btn-outline-secondary btn-sm">Descargar</button>
                  </div>
                </div>

                <div id="pdfContainer" class="border rounded p-2"
                     style="height:70vh; overflow:auto; position:relative; background:#f8f9fa"></div>
                <div id="placeMsg" class="mt-2 text-muted"></div>

                <input type="hidden" id="docId2">
                <input type="hidden" id="viewUrl2">
                <input type="hidden" id="fileName2">
              </div>
            </div>
          </div>
        </div>

        <style>
          .pdf-page-wrap { box-shadow: 0 0 12px rgba(0,0,0,.08); background:#fff; }
          .pin { box-shadow: 0 1px 3px rgba(0,0,0,.2); }
        </style>
<!-- Módulo PDF + firma -->
<script type="module">
  import * as pdfjsLib from "https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.296/build/pdf.min.mjs";
  pdfjsLib.GlobalWorkerOptions.workerSrc =
    "https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.296/build/pdf.worker.min.mjs";

  /* 👇 IMPORTA rgb y degrees para color/rotación */
  import { PDFDocument, StandardFonts, rgb, degrees } from "https://esm.sh/pdf-lib@1.17.1";

  const $ = (id)=>document.getElementById(id);
  const container=$('pdfContainer'), msg=$('placeMsg');
  const openNew=$('openNewTab2'), titleEl=$('pdfPlaceTitle');
  const modalEl=document.getElementById('pdfPlaceModal');

  let currentUrl='', currentName='', currentId=0;
  let marks=[]; let rendering=false;

  const labelFor = (k)=>({reviewedBy:'Reviewed By',reviewedDate:'Reviewed Date',approvedBy:'Approved By',approvedDate:'Approved Date'})[k]||k;
  const toYYYYMMDD = (iso)=>{ if(!iso) return ''; const [y,m,d]=iso.split('-'); return (y&&m&&d)?`${y}-${m}-${d}`:iso; };
  const cssToPdfPoint = (xCss,yCss,viewportWidthCss,pw,ph)=>{ const r=pw/viewportWidthCss; return {xPdf:xCss*r, yPdf:ph-(yCss*r)}; };

  async function renderPDF(url){
    if (rendering) return;
    rendering = true;
    try{
      container.innerHTML=''; msg.textContent='Cargando PDF…';
      const pdf=await pdfjsLib.getDocument({ url, withCredentials: true }).promise;
      for(let pageNum=1; pageNum<=pdf.numPages; pageNum++){
        const page=await pdf.getPage(pageNum), scale=1.1, viewport=page.getViewport({scale});
        const wrap=document.createElement('div');
        wrap.className='pdf-page-wrap position-relative mb-3';
        wrap.style.width=viewport.width+'px'; wrap.style.margin='0 auto';

        const canvas=document.createElement('canvas');
        canvas.width=viewport.width; canvas.height=viewport.height;
        canvas.style.width=viewport.width+'px'; canvas.style.height=viewport.height+'px';

        const overlay=document.createElement('div');
        Object.assign(overlay.style,{position:'absolute',left:'0',top:'0',width:viewport.width+'px',height:viewport.height+'px',cursor:'crosshair'});

        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

        overlay.addEventListener('click',(e)=>{
          const rect=overlay.getBoundingClientRect();
          const x=e.clientX-rect.left, y=e.clientY-rect.top;
          const kind=$('fieldKind').value;

          const pin=document.createElement('div');
          pin.className='pin'; pin.textContent=labelFor(kind);
          Object.assign(pin.style,{position:'absolute',left:(x-4)+'px',top:(y-12)+'px',background:'#0d6efd',color:'#fff',
                                   fontSize:'10px',padding:'2px 4px',borderRadius:'3px',userSelect:'none',pointerEvents:'none'});
          overlay.appendChild(pin);

          marks.push({ pageIndex:pageNum-1, xCss:x, yCss:y, viewportWidthCss:viewport.width, kind });
          msg.textContent = `Marcado ${labelFor(kind)} en página ${pageNum}. Total: ${marks.length}`;
        });

        wrap.appendChild(canvas); wrap.appendChild(overlay); container.appendChild(wrap);
      }
      msg.textContent='Listo. Haz clic para colocar los textos.';
    } catch (e) {
      console.error(e);
      msg.textContent = 'Error al renderizar: ' + e.message;
    } finally { rendering = false; }
  }

  document.getElementById('btnClearMarks')?.addEventListener('click', ()=>{
    marks=[]; container.querySelectorAll('.pin').forEach(n=>n.remove());
    msg.textContent='Marcas limpiadas.';
  });

  async function insert(mode){
    try{
      if(!currentUrl){ msg.textContent='URL vacía.'; return; }
      if(!marks.length){ msg.textContent='Coloca al menos una marca.'; return; }

      const data={
        reviewedBy:  ($('rvBy')?.value||'').trim(),
        reviewedDate:toYYYYMMDD($('rvDate')?.value||''),
        approvedBy:  ($('apBy')?.value||'').trim(),
        approvedDate:toYYYYMMDD($('apDate')?.value||''),
      };

      msg.textContent='Editando PDF…';
      const res = await fetch(currentUrl, { credentials: 'same-origin' });
      if(!res.ok) throw new Error('No se pudo cargar ('+res.status+')');
      const base = await res.arrayBuffer();

      const pdfDoc = await PDFDocument.load(base);

      /* 👇 Formato de texto de la firma */
      // Fuente estándar (elige una). Ej.: Times negrita “tipo sello”
      // const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
      // const font = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
      // const font = await pdfDoc.embedFont(StandardFonts.TimesRoman);
      const font  = await pdfDoc.embedFont(StandardFonts.TimesRomanBold);
      const size  = 12;                 // tamaño
      const color = rgb(0, 0, 0.8);     // azul oscuro
      const angle = degrees(0);         // rotación opcional

      const byPage=new Map();
      for(const m of marks){ if(!byPage.has(m.pageIndex)) byPage.set(m.pageIndex,[]); byPage.get(m.pageIndex).push(m); }
      for (const [idx,arr] of byPage.entries()){
        const page=pdfDoc.getPage(idx); const {width:pw,height:ph}=page.getSize();
        for (const m of arr) {
          const val = data[m.kind] || '';
          if (!val) continue;
          const {xPdf,yPdf}=cssToPdfPoint(m.xCss,m.yCss,m.viewportWidthCss,pw,ph);
          /* 👇 Aplica tamaño, color y rotación */
          page.drawText(val, {
            x: xPdf + 2,
            y: yPdf - 6,
            size,
            font,
            color,
            rotate: angle
          });
        }
      }
      const out = await pdfDoc.save();

      if(mode==='download'){
        const blob=new Blob([out],{type:'application/pdf'});
        const a=document.createElement('a');
        a.href=URL.createObjectURL(blob);
        a.download=(($('fileName2')?.value||'document').replace(/\.pdf$/i,''))+'_text_'+Date.now()+'.pdf';
        a.click();
        setTimeout(()=>URL.revokeObjectURL(a.href),1000);
        msg.textContent='Descargado ✅';
        return;
      }

      msg.textContent='Subiendo…';
      const fd=new FormData();
      const baseName=(($('fileName2')?.value||'document').replace(/\.pdf$/i,''))+'_text_'+Date.now()+'.pdf';
      fd.append('file', new Blob([out],{type:'application/pdf'}), baseName);
      const id=$('docId2')?.value; if(id) fd.append('doc_id', String(id));

      const resp=await fetch('/database/upload_pdf_editado.php', { method:'POST', body:fd, credentials: 'same-origin' });
      const txt=await resp.text();
      if(!resp.ok) throw new Error(txt||'Error al subir');
      msg.textContent='Listo ✅ '+txt+'. Cierra el modal y usa "Ver PDF" para abrir el actualizado.';
    } catch (e) {
      console.error(e);
      msg.textContent = 'Error: ' + e.message;
    }
  }

  document.getElementById('btnInsertDownload')?.addEventListener('click', ()=>insert('download'));
  document.getElementById('btnInsertUpload')?.addEventListener('click',   ()=>insert('upload'));

  function bindPlaceButtons(){
    document.querySelectorAll('.btn-colocar').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        currentUrl  = btn.getAttribute('data-view-url') || '';
        currentName = btn.getAttribute('data-file-name') || 'document.pdf';
        currentId   = parseInt(btn.getAttribute('data-doc-id'),10) || 0;

        document.getElementById('docId2').value    = String(currentId);
        document.getElementById('viewUrl2').value  = currentUrl;
        document.getElementById('fileName2').value = currentName;

        const titleEl = document.getElementById('pdfPlaceTitle');
        const openNew = document.getElementById('openNewTab2');
        if (titleEl) titleEl.textContent = currentName;
        if (openNew) openNew.href = currentUrl || '#';

        marks=[]; container.innerHTML=''; msg.textContent='';

        if(!currentUrl){ msg.textContent='URL inválida o vacía.'; return; }
        msg.textContent = 'Cargando PDF…';
        await renderPDF(currentUrl);
      });
    });
  }
  // Exponer para que el script de búsqueda en vivo pueda re-enlazar
  window.bindPlaceButtons = bindPlaceButtons;
  bindPlaceButtons();

  modalEl?.addEventListener('hidden.bs.modal', ()=>{
    marks=[]; container.innerHTML=''; msg.textContent='';
    ['rvBy','rvDate','apBy','apDate'].forEach(id=>{ const el=$(id); if(el) el.value=''; });
  });
</script>

      </div> <!-- /.col -->
    </div> <!-- /.row -->
  </section>
</main>

<!-- Búsqueda en vivo (debounce + AJAX a ?ajax=1&q=...) + ajustar enlaces de descarga -->
<script>
  (function(){
    const qInput = document.getElementById('qLive');
    const tbody  = document.getElementById('docsTbody');
    const btnClear = document.getElementById('btnClearLive');

    const debounce = (fn, ms=280) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };

    async function fetchRows(query){
      const url = new URL(window.location.href);
      url.searchParams.set('ajax','1');
      url.searchParams.set('q', query || '');
      const resp = await fetch(url.toString(), { credentials: 'same-origin' });
      if (!resp.ok) return;
      const html = await resp.text();
      tbody.innerHTML = html;
      // Re-enlaza los botones del modal PDF (expuesto por el módulo)
      if (window.bindPlaceButtons) window.bindPlaceButtons();
    }
    const doSearch = debounce(()=>fetchRows(qInput.value.trim()), 280);

    qInput?.addEventListener('input', doSearch);
    btnClear?.addEventListener('click', ()=>{ qInput.value=''; fetchRows(''); });

    // ===== Actualiza enlaces de descarga según fecha elegida =====
    const dlDate = document.getElementById('dlDate');
    const dlCreated = document.getElementById('dlCreated');
    const dlSigned = document.getElementById('dlSigned');

    function setDateLinks(d){
      if (!/^\d{4}-\d{2}-\d{2}$/.test(d)) return;
      dlCreated.href = `/database/docs_download_day.php?date=${d}&scope=created`;
      dlSigned.href  = `/database/docs_download_day.php?date=${d}&scope=signed`;
    }
    dlDate?.addEventListener('change', ()=> setDateLinks(dlDate.value));
  })();
</script>

<?php include_once('../components/footer.php'); ?>
