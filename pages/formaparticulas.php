<?php
// hoja_piedras.php
$page_title = 'Hoja de Trabajo – Clasificación de Partículas por Tamaño';
require_once('../config/load.php'); // si usas tu stack
include_once('../components/header.php'); // si usas tu header
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= h($page_title ?? 'Hoja de Trabajo') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap (omite si ya está en tu layout) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .table thead th { white-space: nowrap; }
    .num { width: 82px; }
    .avg { min-width: 90px; font-weight: 600; }
    .sticky-toolbar { position: sticky; top: 0; z-index: 9; background: #fff; border-bottom: 1px solid #eee; }
    .badge-wide { min-width: 130px; }
    .size-select { min-width: 110px; }
    /* Colores por dimensión */
    .dim-l th, .dim-l input { background: #e7f1ff !important; }
    .dim-a th, .dim-a input { background: #e8fff1 !important; }
    .dim-e th, .dim-e input { background: #fff6e6 !important; }
    .head-l { background:#cfe2ff !important; }
    .head-a { background:#d1f7e3 !important; }
    .head-e { background:#ffe6bf !important; }
    .dim-label { font-size: .8rem; color:#6c757d; }
    @media print { .no-print { display:none !important; } .card { border: none; box-shadow: none; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
  </style>
</head>
<body>
<main id="main" class="main container-fluid py-3">

  <div class="d-flex align-items-center justify-content-between sticky-toolbar py-2 no-print">
    <h1 class="h4 m-0"><i class="bi bi-rulers"></i> Clasificación de Partículas por Tamaño</h1>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-eraser"></i> Limpiar</button>
      <button class="btn btn-outline-primary" id="btnPrint"><i class="bi bi-printer"></i> Imprimir</button>
      <button class="btn btn-success" id="btnPDF"><i class="bi bi-filetype-pdf"></i> Exportar PDF</button>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-4">

      <!-- Encabezado -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label">Número de Muestra</label>
          <input type="text" class="form-control" id="numeroMuestra" placeholder="Nombre de muestra">
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha</label>
          <input type="date" class="form-control" id="fecha" value="<?= h(date('Y-m-d')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Técnico</label>
          <input type="text" class="form-control" id="tecnico" placeholder="Nombre del técnico">
        </div>
      </div>

      <!-- Parámetros -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label">Umbral “Chata” (Ancho/Espesor &ge;)</label>
          <input type="number" step="0.01" class="form-control" id="thrChata" value="3.00">
          <div class="form-text">Si A/E &ge; umbral ⇒ “Chata”.</div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Umbral “Alargada” (Largo/Ancho &ge;)</label>
          <input type="number" step="0.01" class="form-control" id="thrAlargada" value="3.00">
          <div class="form-text">Si L/A &ge; umbral ⇒ “Alargada”.</div>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2 no-print">
          <div class="input-group" style="max-width:420px;">
            <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
            <select id="defaultSize" class="form-select">
              <option value="">(Tamaño por defecto para nuevas filas)</option>
            </select>
            <button class="btn btn-outline-secondary" id="applyDefaultSize">Aplicar a filas vacías</button>
          </div>
          <button class="btn btn-success" id="btnAdd"><i class="bi bi-plus-circle"></i> Agregar partícula</button>
          <button class="btn btn-outline-secondary" id="btnRecalc"><i class="bi bi-calculator"></i> Recalcular</button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabla">
          <thead>
            <tr>
              <th rowspan="2" class="text-center align-middle">#</th>
              <th rowspan="2" class="text-center align-middle">Tamaño</th>
              <th colspan="5" class="text-center head-l">Largo (mm) <span class="dim-label">Eje mayor</span></th>
              <th colspan="5" class="text-center head-a">Ancho (mm) <span class="dim-label">Eje intermedio</span></th>
              <th colspan="5" class="text-center head-e">Espesor (mm) <span class="dim-label">Eje menor</span></th>
              <th rowspan="2" class="text-center align-middle">Largo Prom</th>
              <th rowspan="2" class="text-center align-middle">Ancho Prom</th>
              <th rowspan="2" class="text-center align-middle">Espesor Prom</th>
              <th rowspan="2" class="text-center align-middle">Clasificación</th>
              <th rowspan="2" class="no-print"></th>
            </tr>
            <tr>
              <?php for($i=1;$i<=5;$i++): ?><th class="text-center head-l">L<?= $i ?></th><?php endfor; ?>
              <?php for($i=1;$i<=5;$i++): ?><th class="text-center head-a">A<?= $i ?></th><?php endfor; ?>
              <?php for($i=1;$i<=5;$i++): ?><th class="text-center head-e">E<?= $i ?></th><?php endfor; ?>
            </tr>
          </thead>
          <tbody id="tbody">
            <!-- Filas dinámicas -->
          </tbody>
          <tfoot>
            <tr class="table-secondary">
              <th colspan="17" class="text-end">Promedios globales</th>
              <th class="text-center avg" id="avgL">—</th>
              <th class="text-center avg" id="avgA">—</th>
              <th class="text-center avg" id="avgE">—</th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Resumen Global -->
      <div class="row g-3 mt-3">
        <div class="col-md-4">
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between">
              <span>Total clasificadas</span><span class="fw-bold" id="totClas">0</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
              <span><span class="badge bg-warning text-dark badge-wide">Alargadas</span></span>
              <span><strong id="cAlarg">0</strong> (<span id="pAlarg">0%</span>)</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <span><span class="badge bg-info text-dark badge-wide">Chatas</span></span>
              <span><strong id="cChata">0</strong> (<span id="pChata">0%</span>)</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <span><span class="badge bg-success badge-wide">Ni chata ni alargada</span></span>
              <span><strong id="cNing">0</strong> (<span id="pNing">0%</span>)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen por Tamaño -->
      <div class="mt-4">
        <h6 class="mb-2">Resumen por tamaño</h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered">
            <thead class="table-light">
              <tr>
                <th>Tamaño</th>
                <th class="text-center">Total</th>
                <th class="text-center">Alargadas</th>
                <th class="text-center">Chatas</th>
                <th class="text-center">Ni chata ni alargada</th>
                <th class="text-center">% Alargadas</th>
                <th class="text-center">% Chatas</th>
                <th class="text-center">% Ni ch./alarg.</th>
              </tr>
            </thead>
            <tbody id="sizeSummaryBody">
              <!-- dinámico -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Observaciones -->
      <div class="mt-3">
        <label class="form-label">Observaciones</label>
        <textarea class="form-control" rows="3" id="observaciones" placeholder="Observaciones del ensayo…"></textarea>
      </div>

    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>

<script>
(function(){
  // catálogo de tamaños
  const SIZES = [
    {label:'3"',   mm:76.2},
    {label:'2½"',  mm:63.5},
    {label:'2"',   mm:50.8},
    {label:'1½"',  mm:38.1},
    {label:'1"',   mm:25.4},
    {label:'3/4"', mm:19.0},
    {label:'1/2"', mm:12.5},
    {label:'3/8"', mm:9.5},
  ];

  const tbody = document.getElementById('tbody');
  const avgL  = document.getElementById('avgL');
  const avgA  = document.getElementById('avgA');
  const avgE  = document.getElementById('avgE');

  const thrChata    = document.getElementById('thrChata');    // A/E >= ?
  const thrAlargada = document.getElementById('thrAlargada'); // L/A >= ?

  const btnAdd   = document.getElementById('btnAdd');
  const btnRecalc= document.getElementById('btnRecalc');
  const btnClear = document.getElementById('btnClear');
  const btnPrint = document.getElementById('btnPrint');
  const btnPDF   = document.getElementById('btnPDF');

  const defaultSize = document.getElementById('defaultSize');
  const applyDefaultSize = document.getElementById('applyDefaultSize');

  // resumen global
  const totClas = document.getElementById('totClas');
  const cAlarg  = document.getElementById('cAlarg');
  const cChata  = document.getElementById('cChata');
  const cNing   = document.getElementById('cNing');
  const pAlarg  = document.getElementById('pAlarg');
  const pChata  = document.getElementById('pChata');
  const pNing   = document.getElementById('pNing');

  // resumen por tamaño
  const sizeSummaryBody = document.getElementById('sizeSummaryBody');

  let counter = 0;

  // llenar select por defecto
  SIZES.forEach(s=>{
    const opt = document.createElement('option');
    opt.value = s.label;
    opt.textContent = `${s.label} (${s.mm} mm)`;
    defaultSize.appendChild(opt);
  });

  function fmt(x){ if (x===null || isNaN(x)) return '—'; return Number(x).toFixed(3); }
  function parseN(v){ const n = parseFloat(v); return isNaN(n) ? null : n; }
  function mean(arr){ const nums = arr.map(parseN).filter(v => v!==null); if (!nums.length) return null; return nums.reduce((a,b)=>a+b,0)/nums.length; }

  function sizeSelectTemplate(){
    let html = `<select class="form-select form-select-sm size-select" data-k="SZ">`;
    html += `<option value="">(Seleccione)</option>`;
    for (const s of SIZES) html += `<option value="${s.label}" data-mm="${s.mm}">${s.label} (${s.mm} mm)</option>`;
    html += `</select>`;
    return html;
  }

  function rowTemplate(n){
    let html = `<tr data-row="${n}">
      <td class="text-center fw-semibold">${n}</td>
      <td>${sizeSelectTemplate()}</td>`;
    for(let i=1;i<=5;i++) html += `<td class="dim-l"><input title="Largo L${i}" placeholder="L${i}" type="number" step="0.01" class="form-control form-control-sm num" data-k="L${i}"></td>`;
    for(let i=1;i<=5;i++) html += `<td class="dim-a"><input title="Ancho A${i}" placeholder="A${i}" type="number" step="0.01" class="form-control form-control-sm num" data-k="A${i}"></td>`;
    for(let i=1;i<=5;i++) html += `<td class="dim-e"><input title="Espesor E${i}" placeholder="E${i}" type="number" step="0.01" class="form-control form-control-sm num" data-k="E${i}"></td>`;
    html += `
      <td class="text-center avg" data-out="L">—</td>
      <td class="text-center avg" data-out="A">—</td>
      <td class="text-center avg" data-out="E">—</td>
      <td class="text-center" data-out="C">—</td>
      <td class="no-print text-end">
        <button class="btn btn-sm btn-outline-danger" data-action="del" title="Eliminar"><i class="bi bi-x-lg"></i></button>
      </td>
    </tr>`;
    return html;
  }

  function addRow(prefill){
    counter++;
    tbody.insertAdjacentHTML('beforeend', rowTemplate(counter));
    const tr = tbody.querySelector('tr:last-child');

    if (prefill){
      Object.entries(prefill).forEach(([k,v])=>{
        if (k === 'SZ') {
          const sel = tr.querySelector('select[data-k="SZ"]');
          if (sel) sel.value = v;
        } else {
          const inp = tr.querySelector(`input[data-k="${k}"]`);
          if (inp) inp.value = v;
        }
      });
    }

    tr.addEventListener('input', recalcAll);
    tr.addEventListener('change', recalcAll);
    tr.querySelector('[data-action="del"]').addEventListener('click', (e)=>{
      e.preventDefault(); tr.remove();
      [...tbody.querySelectorAll('tr')].forEach((row, idx)=> row.querySelector('td:first-child').textContent = (idx+1));
      recalcAll();
    });
    recalcAll();
  }

  function classify(L,A,E){
    const tC = parseN(thrChata.value)    || 3.0; // A/E
    const tA = parseN(thrAlargada.value) || 3.0; // L/A
    if (A && E && (A/E) >= tC) return 'Chata';
    if (L && A && (L/A) >= tA) return 'Alargada';
    return 'Ni chata ni alargada';
  }

  function recalcAll(){
    const rows = [...tbody.querySelectorAll('tr')];
    const Ls=[], As=[], Es=[];
    let cntAlarg=0, cntChata=0, cntNing=0, cntClas=0;

    const bySize = {};
    const ensureSize = (s)=>{ if(!bySize[s]) bySize[s]={total:0,Alargada:0,Chata:0,Ning:0}; return bySize[s]; };

    rows.forEach(tr=>{
      const size = (tr.querySelector('select[data-k="SZ"]').value || '').trim();
      const LsArr = [...Array(5)].map((_,i)=> parseN(tr.querySelector(`input[data-k="L${i+1}"]`).value));
      const AsArr = [...Array(5)].map((_,i)=> parseN(tr.querySelector(`input[data-k="A${i+1}"]`).value));
      const EsArr = [...Array(5)].map((_,i)=> parseN(tr.querySelector(`input[data-k="E${i+1}"]`).value));

      const Lav = mean(LsArr);
      const Aav = mean(AsArr);
      const Eav = mean(EsArr);

      tr.querySelector('[data-out="L"]').textContent = fmt(Lav);
      tr.querySelector('[data-out="A"]').textContent = fmt(Aav);
      tr.querySelector('[data-out="E"]').textContent = fmt(Eav);

      let cls = '—';
      if (Lav!==null && Aav!==null && Eav!==null){
        cls = classify(Lav,Aav,Eav);
        cntClas++;
        if (cls==='Alargada') cntAlarg++;
        else if (cls==='Chata') cntChata++;
        else cntNing++;
        Ls.push(Lav); As.push(Aav); Es.push(Eav);

        const key = size || '(sin tamaño)';
        const acc = ensureSize(key);
        acc.total++;
        if (cls==='Alargada') acc.Alargada++;
        else if (cls==='Chata') acc.Chata++;
        else acc.Ning++;
      }
      tr.querySelector('[data-out="C"]').textContent = cls;
    });

    const gL = Ls.length ? Ls.reduce((a,b)=>a+b,0)/Ls.length : null;
    const gA = As.length ? As.reduce((a,b)=>a+b,0)/As.length : null;
    const gE = Es.length ? Es.reduce((a,b)=>a+b,0)/Es.length : null;
    avgL.textContent = fmt(gL);
    avgA.textContent = fmt(gA);
    avgE.textContent = fmt(gE);

    totClas.textContent = cntClas;
    cAlarg.textContent = cntAlarg;
    cChata.textContent = cntChata;
    cNing.textContent  = cntNing;
    pAlarg.textContent = cntClas ? (100*cntAlarg/cntClas).toFixed(1)+'%' : '0%';
    pChata.textContent = cntClas ? (100*cntChata/cntClas).toFixed(1)+'%' : '0%';
    pNing.textContent  = cntClas ? (100*cntNing /cntClas).toFixed(1)+'%' : '0%';

    const sizeSummaryBody = document.getElementById('sizeSummaryBody');
    sizeSummaryBody.innerHTML = '';
    Object.keys(bySize).sort((a,b)=>a.localeCompare(b,'es')).forEach(k=>{
      const v = bySize[k];
      const pA = v.total ? (100*v.Alargada/v.total).toFixed(1)+'%' : '0%';
      const pC = v.total ? (100*v.Chata/v.total).toFixed(1)+'%' : '0%';
      const pN = v.total ? (100*v.Ning /v.total).toFixed(1)+'%' : '0%';
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${k}</td>
        <td class="text-center">${v.total}</td>
        <td class="text-center">${v.Alargada}</td>
        <td class="text-center">${v.Chata}</td>
        <td class="text-center">${v.Ning}</td>
        <td class="text-center">${pA}</td>
        <td class="text-center">${pC}</td>
        <td class="text-center">${pN}</td>
      `;
      sizeSummaryBody.appendChild(tr);
    });
  }

  applyDefaultSize.addEventListener('click', (e)=>{
    e.preventDefault();
    const def = defaultSize.value;
    if (!def) return;
    [...tbody.querySelectorAll('tr')].forEach(tr=>{
      const sel = tr.querySelector('select[data-k="SZ"]');
      if (sel && !sel.value) sel.value = def;
    });
    recalcAll();
  });

  btnAdd.addEventListener('click', (e)=>{ e.preventDefault(); addRow({ SZ: defaultSize.value || '' }); });
  btnRecalc.addEventListener('click', (e)=>{ e.preventDefault(); recalcAll(); });
  btnClear.addEventListener('click', (e)=>{
    e.preventDefault();
    if (confirm('¿Limpiar todas las filas?')) {
      tbody.innerHTML=''; counter=0; recalcAll();
    }
  });
  btnPrint.addEventListener('click', (e)=>{ e.preventDefault(); window.print(); });

  btnPDF.addEventListener('click', (e)=>{
    e.preventDefault();
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit:'pt', format:'a4' });

    const numero = document.getElementById('numeroMuestra').value || '—';
    const fecha  = document.getElementById('fecha').value || '—';
    const tec    = document.getElementById('tecnico').value || '—';

    doc.setFontSize(14);
    doc.text('Hoja de Trabajo – Clasificación de Partículas por Tamaño', 40, 40);
    doc.setFontSize(11);
    doc.text(`Número de Muestra: ${numero}`, 40, 62);
    doc.text(`Fecha: ${fecha}`, 300, 62);
    doc.text(`Técnico: ${tec}`, 40, 80);

    const rows = [...tbody.querySelectorAll('tr')];
    const body = rows.map((tr, idx)=>{
      const SZ  = tr.querySelector('select[data-k="SZ"]').value || '';
      const LsArr = [...Array(5)].map((_,i)=> tr.querySelector(`input[data-k="L${i+1}"]`).value || '');
      const AsArr = [...Array(5)].map((_,i)=> tr.querySelector(`input[data-k="A${i+1}"]`).value || '');
      const EsArr = [...Array(5)].map((_,i)=> tr.querySelector(`input[data-k="E${i+1}"]`).value || '');
      const Lav = tr.querySelector('[data-out="L"]').textContent;
      const Aav = tr.querySelector('[data-out="A"]').textContent;
      const Eav = tr.querySelector('[data-out="E"]').textContent;
      const C   = tr.querySelector('[data-out="C"]').textContent;
      return [idx+1, SZ, ...LsArr, ...AsArr, ...EsArr, Lav, Aav, Eav, C];
    });

    const head = [[
      'N°','Tamaño',
      'L1','L2','L3','L4','L5',
      'A1','A2','A3','A4','A5',
      'E1','E2','E3','E4','E5',
      'L Prom','A Prom','E Prom','Clasificación'
    ]];

    doc.autoTable({
      startY: 100,
      head, body,
      styles: { fontSize: 8, cellPadding: 3 },
      headStyles: { fillColor: [240,240,240] },
      theme: 'grid',
      columnStyles: { 0: { cellWidth: 22 }, 1: { cellWidth: 40 } }
    });

    let y = doc.lastAutoTable.finalY + 16;
    const tot = document.getElementById('totClas').textContent;
    const ca  = document.getElementById('cAlarg').textContent;
    const cc  = document.getElementById('cChata').textContent;
    const cn  = document.getElementById('cNing').textContent;
    const pa  = document.getElementById('pAlarg').textContent;
    const pc  = document.getElementById('pChata').textContent;
    const pn  = document.getElementById('pNing').textContent;

    doc.setFontSize(12);
    doc.text('Resumen de Clasificación (Global)', 40, y);
    doc.setFontSize(10);
    doc.text(`Total clasificadas: ${tot}`, 40, y+18);
    doc.text(`Alargadas: ${ca} (${pa})`, 40, y+36);
    doc.text(`Chatas: ${cc} (${pc})`, 40, y+54);
    doc.text(`Ni chata ni alargada: ${cn} (${pn})`, 40, y+72);

    const sizeData = [];
    [...document.querySelectorAll('#sizeSummaryBody tr')].forEach(tr=>{
      const tds = tr.querySelectorAll('td');
      sizeData.push([tds[0].textContent, tds[1].textContent, tds[2].textContent, tds[3].textContent, tds[4].textContent, tds[5].textContent, tds[6].textContent, tds[7].textContent]);
    });

    y += 100;
    doc.setFontSize(12);
    doc.text('Resumen por tamaño', 40, y);
    doc.autoTable({
      startY: y+10,
      head: [['Tamaño','Total','Alargadas','Chatas','Ni ch./alarg.','% Alarg.','% Chatas','% Ni ch./alarg.']],
      body: sizeData,
      styles: { fontSize: 9, cellPadding: 3 },
      headStyles: { fillColor: [240,240,240] },
      theme: 'grid'
    });

    const gL = document.getElementById('avgL').textContent;
    const gA = document.getElementById('avgA').textContent;
    const gE = document.getElementById('avgE').textContent;
    doc.text(`Promedios globales → Largo: ${gL}  |  Ancho: ${gA}  |  Espesor: ${gE}`, 40, doc.lastAutoTable.finalY + 18);

    doc.save(`Hoja_Particulas_${numero || 'sin_numero'}.pdf`);
  });

  // Fila demo
  addRow({ SZ:'3/8"', L1:51.42, L2:41.37, A1:26.46, A2:21.36, E1:16.97, E2:21.47 });

})();
</script>

</body>
</html>
<?php include_once('../components/footer.php'); ?>
