<?php
$page_title = 'Inventario de Equipos (Moderno)';
require_once('../config/load.php');
page_require_level(3);

$cats = find_all('categories');
?>
<?php include_once('../components/header.php'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<style>
.thumb-img{ width:38px; height:38px; border-radius:50%; object-fit:cover; }
.badge-soft{ font-size:12px; }
</style>

<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-box-seam"></i> Inventario de Equipos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item active"><a href="../components/menu_inventarios.php">Inventarios</a></li>
      </ol>
    </nav>
  </div>

  <?php echo display_msg($msg); ?>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-3">

        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title mb-0">Listado de Artículos</h5>
            <div class="text-muted small">Búsqueda + filtros + edición + movimientos</div>
          </div>

          <div class="d-flex gap-2">
            <a href="add_product.php" class="btn btn-primary">
              <i class="bi bi-plus-circle"></i> Agregar
            </a>
            <a href="../pages/sumary/export_inventario_equipos.php" class="btn btn-success">
              <i class="bi bi-file-earmark-excel"></i> Exportar (actual)
            </a>
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select id="fltStatus" class="form-select">
              <option value="">Todos</option>
              <option value="Disponible">Disponible</option>
              <option value="No Disponible">No Disponible</option>
              <option value="En Uso">En Uso</option>
              <option value="Fuera de Servicio">Fuera de Servicio</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Categoría</label>
            <select id="fltCategory" class="form-select">
              <option value="">Todas</option>
              <?php foreach($cats as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo remove_junk($c['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Buscar</label>
            <input id="fltSearch" class="form-control" placeholder="Nombre, código, marca/modelo, categoría...">
          </div>
        </div>

        <div class="table-responsive">
          <table id="tblInv" class="table table-striped table-hover align-middle w-100">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Artículo</th>
                <th>Marca/Modelo</th>
                <th>Código</th>
                <th>Status</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Compra</th>
                <th>Agregado</th>
                <th>Acciones</th>
              </tr>
            </thead>
          </table>
        </div>

      </div>
    </div>
  </section>
</main>

<!-- Modal editar -->
<div class="modal fade" id="mdlEdit" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar producto</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="e_id">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Artículo</label>
            <input class="form-control" id="e_name">
          </div>
          <div class="col-md-6">
            <label class="form-label">Marca/Modelo</label>
            <input class="form-control" id="e_mm">
          </div>
          <div class="col-md-4">
            <label class="form-label">Código</label>
            <input class="form-control" id="e_code">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select class="form-select" id="e_status">
              <option>Disponible</option>
              <option>No Disponible</option>
              <option>En Uso</option>
              <option>Fuera de Servicio</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Categoría</label>
            <select class="form-select" id="e_cat">
              <?php foreach($cats as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo remove_junk($c['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Stock</label>
            <input class="form-control" id="e_qty" type="number" step="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label">Compra</label>
            <input class="form-control" id="e_buy" type="number" step="0.01">
          </div>
        </div>

        <hr>
        <h6 class="mb-2"><i class="bi bi-arrow-left-right"></i> Movimiento rápido</h6>
        <div class="row g-2">
          <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select id="m_type" class="form-select">
              <option value="IN">IN (Entrada)</option>
              <option value="OUT">OUT (Salida)</option>
              <option value="ADJUST">ADJUST (Ajuste)</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Cantidad</label>
            <input id="m_qty" class="form-control" type="number" step="0.01">
          </div>
          <div class="col-md-6">
            <label class="form-label">Razón</label>
            <input id="m_reason" class="form-control" placeholder="Ej: asignado a técnico / calibración / reposición...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Ref (opcional)</label>
            <input id="m_ref" class="form-control" placeholder="OC, ticket, solicitud...">
          </div>
          <div class="col-md-6 d-flex align-items-end gap-2">
            <button id="btnMove" class="btn btn-outline-primary w-100">
              <i class="bi bi-check2-circle"></i> Registrar movimiento
            </button>
            <button id="btnHistory" class="btn btn-outline-secondary w-100">
              <i class="bi bi-clock-history"></i> Ver historial
            </button>
          </div>
        </div>

        <div id="historyBox" class="mt-3" style="display:none;">
          <h6 class="mb-2">Historial (últimos 200)</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th><th>Tipo</th><th>Qty</th><th>Razón</th><th>Ref</th><th>Por</th>
                </tr>
              </thead>
              <tbody id="historyBody"></tbody>
            </table>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary" id="btnSave"><i class="bi bi-save"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>

<?php include_once('../components/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
let tbl;

function badgeStatus(s){
  s = (s||'').toString();
  const map = {
    'Disponible':'success',
    'En Uso':'warning',
    'No Disponible':'secondary',
    'Fuera de Servicio':'danger'
  };
  const cls = map[s] || 'primary';
  return `<span class="badge bg-${cls}">${s}</span>`;
}

$(function(){

  tbl = $('#tblInv').DataTable({
    processing:true,
    serverSide:true,
    pageLength:25,
    dom:'Bfrtip',
    buttons:[
      { extend:'print', text:'<i class="bi bi-printer"></i> Imprimir' },
      { extend:'excelHtml5', text:'<i class="bi bi-file-earmark-excel"></i> Excel' }
    ],
    ajax:{
      url:'../api/inventory/products_list.php',
      data:function(d){
        d.status = $('#fltStatus').val();
        d.category = $('#fltCategory').val();
      }
    },
    columns:[
      {data:'id'},
      {data:null, orderable:false, render:function(row){
        const img = (row.media_id==='0' || !row.image) ? 'no_image.jpg' : row.image;
        return `<img class="thumb-img" src="../uploads/products/${img}" alt="img">`;
      }},
      {data:'name'},
      {data:'Marca_Modelo'},
      {data:'Codigo'},
      {data:'Status', render:(d)=>badgeStatus(d)},
      {data:'categorie'},
      {data:'quantity', className:'text-center'},
      {data:'buy_price', className:'text-end', render:(d)=>Number(d||0).toFixed(2)},
      {data:'date', render:(d)=>d ? d.toString().substring(0,10) : ''},
      {data:null, orderable:false, render:function(row){
        return `
          <button class="btn btn-sm btn-warning btnEdit" data-id="${row.id}">
            <i class="bi bi-pencil-square"></i>
          </button>
          <a class="btn btn-sm btn-danger" href="../pages/delete_product.php?id=${row.id}">
            <i class="bi bi-trash"></i>
          </a>
        `;
      }}
    ]
  });

  $('#fltSearch').on('keyup', function(){ tbl.search(this.value).draw(); });
  $('#fltStatus,#fltCategory').on('change', function(){ tbl.draw(); });

  // Abrir modal editar
  $('#tblInv').on('click','.btnEdit', function(){
    const id = $(this).data('id');
    const row = tbl.row($(this).closest('tr')).data();

    $('#e_id').val(row.id);
    $('#e_name').val(row.name||'');
    $('#e_mm').val(row.Marca_Modelo||'');
    $('#e_code').val(row.Codigo||'');
    $('#e_status').val(row.Status||'Disponible');
    // no tenemos categorie_id en la respuesta => solución rápida: no cambiarlo aquí si no lo necesitas
    // si lo quieres, dime y lo agrego al JSON.
    $('#e_qty').val(row.quantity||0);
    $('#e_buy').val(row.buy_price||0);

    $('#historyBox').hide();
    $('#historyBody').html('');

    const modal = new bootstrap.Modal(document.getElementById('mdlEdit'));
    modal.show();
  });

  // Guardar producto
  $('#btnSave').on('click', async function(){
    const payload = new URLSearchParams({
      id: $('#e_id').val(),
      name: $('#e_name').val(),
      Marca_Modelo: $('#e_mm').val(),
      Codigo: $('#e_code').val(),
      Status: $('#e_status').val(),
      quantity: $('#e_qty').val(),
      buy_price: $('#e_buy').val(),
      // categorie_id: $('#e_cat').val(), // activar si lo agregamos al JSON
    });

    const res = await fetch('../api/inventory/product_save.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: payload.toString()
    }).then(r=>r.json()).catch(()=>({ok:false,msg:'Error'}));

    alert(res.msg || (res.ok ? 'OK' : 'Error'));
    if(res.ok) tbl.draw(false);
  });

  // Movimiento
  $('#btnMove').on('click', async function(){
    const payload = new URLSearchParams({
      product_id: $('#e_id').val(),
      movement_type: $('#m_type').val(),
      qty: $('#m_qty').val(),
      reason: $('#m_reason').val(),
      ref: $('#m_ref').val()
    });

    const res = await fetch('../api/inventory/product_move.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: payload.toString()
    }).then(r=>r.json()).catch(()=>({ok:false,msg:'Error'}));

    alert(res.msg || (res.ok ? 'OK' : 'Error'));
    if(res.ok){
      tbl.draw(false);
      $('#m_qty').val('');
      $('#m_reason').val('');
      $('#m_ref').val('');
    }
  });

  // Historial
  $('#btnHistory').on('click', async function(){
    const id = $('#e_id').val();
    const res = await fetch('../api/inventory/product_history.php?id='+encodeURIComponent(id))
      .then(r=>r.json()).catch(()=>({ok:false,data:[]}));

    if(!res.ok){ alert('No se pudo cargar historial'); return; }

    const rows = res.data || [];
    let html = '';
    rows.forEach(r=>{
      html += `<tr>
        <td>${(r.created_at||'').toString().replace('T',' ').substring(0,19)}</td>
        <td>${r.movement_type}</td>
        <td class="text-end">${r.qty}</td>
        <td>${r.reason||''}</td>
        <td>${r.ref||''}</td>
        <td>${r.created_by||''}</td>
      </tr>`;
    });

    $('#historyBody').html(html || '<tr><td colspan="6" class="text-center text-muted">Sin movimientos</td></tr>');
    $('#historyBox').show();
  });

});
</script>
