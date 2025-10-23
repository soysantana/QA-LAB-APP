<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
?>
<main id="main" class="main">
  <div class="pagetitle"><h1>Subir PDF externo (múltiples)</h1></div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <form action="/database/doc_upload_external_save.php" method="post" enctype="multipart/form-data" class="mt-3" id="uploadForm">
          <div class="mb-3">
            <label class="form-label">Archivos PDF</label>
            <input type="file" class="form-control" name="pdfs[]" id="pdfs" accept="application/pdf" multiple required>
            <div class="form-text">
              Convenciones soportadas: <code>SID_SNUM_TTYPE[_vN].pdf</code>,
              <code>SID-####-TT</code>, <code>SID-####-TT-TT2</code>,
              y <code>LL25-126-PVGT00003792-PLT</code> (SID-SNUM-TTYPE).
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr>
                  <th style="width:35%">Archivo</th>
                  <th style="width:35%">Sample ID</th>
                  <th style="width:15%">Sample Number</th>
                  <th style="width:10%">Test Type</th>
                  <th style="width:5%"></th>
                </tr>
              </thead>
              <tbody id="fileRows"></tbody>
            </table>
          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Subir todo</button>
            <a class="btn btn-secondary" href="/pages/docs_list.php">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<script>
function parseName(rawName) {
  let name = rawName.replace(/\.pdf$/i,'').trim();
  // quita sufijo de versión tipo _vN o -vN o ' vN'
  name = name.replace(/(?:[_\-\s])v\d+$/i, '').trim();

  // 1) Formato con guiones (más flexible): SID - SNUM - TTYPE (TTYPE puede tener varios tokens alfa)
  if (name.includes('-')) {
    // Si el nombre trae espacios además de guiones, tomamos el primer “token de espacios”
    const firstToken = name.split(/\s+/)[0];
    const parts = firstToken.split('-').filter(Boolean);
    if (parts.length >= 3) {
      // Trailing alpha-only tokens => TTYPE (uno o varios, p.ej. PLT o GS-CF)
      const ttypeParts = [];
      for (let i = parts.length - 1; i >= 0; i--) {
        if (/^[A-Za-z]+$/.test(parts[i])) ttypeParts.unshift(parts[i]);
        else break;
      }
      if (ttypeParts.length >= 1) {
        const cutoff = parts.length - ttypeParts.length;     // índice del primer token NO TTYPE desde el final
        const maybeSnum = parts[cutoff - 1];                 // token previo a TTYPE
        const sidParts  = parts.slice(0, cutoff - 1);        // el resto conforma el SID
        if (maybeSnum && sidParts.length >= 1) {
          // Aceptamos SNUM alfanumérico (p.ej. PVGT00003792). No exigimos solo dígitos.
          if (/^[A-Za-z0-9]+$/.test(maybeSnum)) {
            return {
              sid: sidParts.join('-'),
              snum: maybeSnum,
              ttype: ttypeParts.join('-')
            };
          }
        }
      }

      // Caso alterno: último token es TTYPE (solo letras), previo SNUM puede ser dígitos puros
      const last = parts[parts.length - 1];
      if (/^[A-Za-z]+$/.test(last) && parts.length >= 3) {
        const sn = parts[parts.length - 2];
        const sid = parts.slice(0, -2).join('-');
        if (sid) {
          return { sid, snum: sn, ttype: last };
        }
      }

      // Fallback: si el último token es totalmente numérico, tómalo como SNUM y deja TTYPE vacío
      const lastToken = parts[parts.length - 1];
      if (/^\d+$/.test(lastToken)) {
        const sid = parts.slice(0, -1).join('-');
        return { sid, snum: lastToken, ttype: '' };
      }
    }
  }

  // 2) Formato con underscores: SID_SNUM_TTYPE
  if (name.includes('_')) {
    const parts = name.split('_').filter(Boolean);
    if (parts.length >= 3) {
      const ttype = parts.pop();
      const snum  = parts.pop();
      const sid   = parts.join('_');
      return { sid, snum, ttype };
    }
  }

  // 3) Formato con espacios: SID SNUM TTYPE
  if (name.includes(' ')) {
    const parts = name.split(/\s+/).filter(Boolean);
    if (parts.length >= 3) {
      const ttype = parts.pop();
      const snum  = parts.pop();
      const sid   = parts.join(' ');
      return { sid, snum, ttype };
    }
  }

  // 4) Fallback: todo como SID
  return { sid: name, snum: '', ttype: '' };
}

function addRow(file) {
  const parsed = parseName(file.name);
  const sid = parsed.sid || '';
  const snum = parsed.snum || '';
  const ttype = parsed.ttype || '';

  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <div class="small text-truncate" title="${file.name}">${file.name}</div>
      <input type="hidden" name="__filename[]" value="${file.name}">
    </td>
    <td><input type="text" class="form-control form-control-sm" name="sample_id[]" value="${sid}"></td>
    <td><input type="text" class="form-control form-control-sm" name="sample_number[]" value="${snum}"></td>
    <td><input type="text" class="form-control form-control-sm" name="test_type[]" value="${ttype}"></td>
    <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm" data-row-remove>&times;</button></td>
  `;
  tr.querySelector('[data-row-remove]').addEventListener('click', () => tr.remove());
  document.getElementById('fileRows').appendChild(tr);
}

const fileInput = document.getElementById('pdfs');
fileInput.addEventListener('change', () => {
  const tbody = document.getElementById('fileRows');
  tbody.innerHTML = '';
  const files = Array.from(fileInput.files || []);
  files.forEach(addRow);
});

document.getElementById('uploadForm').addEventListener('submit', (e) => {
  const filesCount = (fileInput.files || []).length;
  const rowsCount  = document.querySelectorAll('#fileRows tr').length;
  if (filesCount === 0 || rowsCount === 0) {
    e.preventDefault();
    alert('Selecciona al menos un PDF.');
    return;
  }
});
</script>
<?php include_once('../components/footer.php'); ?>
