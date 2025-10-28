<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

/**
 * Prefijos base SIN año (la API añadirá el año actual)
 */
$prefijos_base = [
  'PVDJ-AGG',
  'PVDJ-AGG-INV',
  'PVDJ-AGG-DIO',
  'LBOR',
  'PVDJ-MISC',
  'LLD-258',
  'SD3-258',
  'SD2-258',
  'SD1-258',
];
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Muestras del Día</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item">Reportes</li>
        <li class="breadcrumb-item active">Muestras del Día</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row g-3">

      <!-- Card: Resumen -->
      <div class="col-12 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">📅 Resumen</h5>
            <div class="d-flex align-items-baseline gap-2">
              <div class="fs-2 fw-bold" id="todayCount">--</div>
              <div class="text-muted">muestras registradas hoy</div>
            </div>
            <div class="small text-muted" id="todayDate"></div>
          </div>
        </div>
      </div>

      <!-- Card: Siguiente Sample_ID -->
      <div class="col-12 col-lg-8">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">🔢 Siguiente número de muestra</h5>

            <form class="row gy-2 gx-2 align-items-end" id="prefixForm" onsubmit="return false;">
              <div class="col-md-5">
                <label class="form-label">Prefijo (sin año)</label>
                <select class="form-select" id="prefixSelect">
                  <?php foreach ($prefijos_base as $p): ?>
                    <option value="<?php echo htmlspecialchars($p,ENT_QUOTES,'UTF-8');?>"><?php echo htmlspecialchars($p,ENT_QUOTES,'UTF-8');?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-5">
                <label class="form-label">O escribe uno (con o sin año)</label>
                <input type="text" class="form-control" id="prefixCustom" placeholder="Ej: PVDJ-AGG-DIO (o PVDJ-AGG-DIO25)">
              </div>

              <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" id="btnConsultar">
                  <i class="bi bi-search"></i> Consultar
                </button>
              </div>
            </form>

            <hr>

            <div class="d-flex flex-wrap align-items-center gap-3">
              <div class="text-muted">Siguiente Sample_ID:</div>
              <div class="badge bg-success text-wrap fs-6" id="nextId">--</div>
            </div>
            <div class="small text-muted mt-1" id="nextMeta"></div>
            <div class="small text-muted mt-1" id="prevYearNote"></div>
          </div>
        </div>
      </div>

      <!-- Tabla de muestras -->
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0">Muestras registradas hoy</h5>
              <button class="btn btn-outline-secondary btn-sm" id="btnRefrescar">
                <i class="bi bi-arrow-clockwise"></i> Refrescar
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-striped align-middle" id="tblToday">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Sample_ID</th>
                    <th>Sample_Number</th>
                    <th>Test_Type</th>
                    <th>Material_Type</th>
                    <th>Registed_Date</th>
                  </tr>
                </thead>
                <tbody><!-- se llena por JS --></tbody>
              </table>
            </div>

            <div class="small text-muted">
              Nota: Si tu columna <code>Registed_Date</code> es DATETIME, ajusta el flag en la API.
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

<script>
(function(){
  const fmt = new Intl.DateTimeFormat('es-DO', { dateStyle:'full' });
  document.getElementById('todayDate').textContent = 'Hoy es ' + fmt.format(new Date());

  const prefixSelect = document.getElementById('prefixSelect');
  const prefixCustom = document.getElementById('prefixCustom');
  const btnConsultar = document.getElementById('btnConsultar');
  const btnRefrescar = document.getElementById('btnRefrescar');
  const nextIdEl     = document.getElementById('nextId');
  const nextMetaEl   = document.getElementById('nextMeta');
  const prevYearEl   = document.getElementById('prevYearNote');
  const todayCountEl = document.getElementById('todayCount');
  const tbody        = document.querySelector('#tblToday tbody');

  function currentPrefix(){
    const custom = prefixCustom.value.trim();
    return custom !== '' ? custom : prefixSelect.value; // sin año por defecto
  }

  async function loadData(){
    const prefix = encodeURIComponent(currentPrefix());
    const url = `/api/samples_today_and_next.php?prefix=${prefix}`;
    const res = await fetch(url, { headers:{'Cache-Control':'no-cache'} });
    const data = await res.json();

    if(!data.ok){
      alert(data.error || 'Error al cargar datos');
      return;
    }

    // Contador
    todayCountEl.textContent = data.today_count ?? 0;

    // Siguiente ID
    if (data.next && data.next.next_id){
      nextIdEl.textContent = data.next.next_id;
      nextMetaEl.textContent = `Prefijo resuelto: ${data.next.resolved_prefix} | Máximo encontrado: ${data.next.max_found ?? 0} | Año: ${data.next.year}`;
    } else {
      nextIdEl.textContent = '--';
      nextMetaEl.textContent = 'Sin datos para el prefijo elegido';
    }

    // Nota de transición de año (si aplica)
    prevYearEl.textContent = '';
    if (data.next && data.next.prev_year_context){
      const p = data.next.prev_year_context;
      prevYearEl.textContent = `Info año previo (${p.prev_year}): prefijo ${p.prev_prefix}, máximo ${p.prev_max_found}. ${p.note}`;
    }

    // Tabla
    tbody.innerHTML = '';
    (data.today || []).forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${row.id ?? ''}</td>
        <td>${row.Sample_ID ?? ''}</td>
        <td>${row.Sample_Number ?? ''}</td>
        <td>${row.Test_Type ?? ''}</td>
        <td>${row.Material_Type ?? ''}</td>
        <td>${row.Registed_Date ?? ''}</td>
      `;
      tbody.appendChild(tr);
    });

    // Si usas DataTables, inicialízalo una sola vez aquí (opcional).
    // if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#tblToday')) {
    //   $('#tblToday').DataTable({ pageLength:25, order:[[0,'desc']] });
    // }
  }

  btnConsultar.addEventListener('click', loadData);
  btnRefrescar.addEventListener('click', loadData);

  // Carga inicial
  loadData();
})();
</script>

<?php include_once('../components/footer.php'); ?>
