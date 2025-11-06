<?php
// pages/dashboard_stages.php
$page_title = 'Dashboard de Etapas';
$menu_active = 'dashboard';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ------------------------------
// Conteos por etapa (último registro por Sample_ID + Test_Type con Register_Date)
// ------------------------------
$prep_count = find_by_sql("
  SELECT COUNT(*) AS n FROM (
    SELECT tp.Sample_ID, tp.Test_Type
    FROM test_preparation tp
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_preparation
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = tp.Sample_ID AND t.Test_Type = tp.Test_Type AND t.last_dt <=> tp.Register_Date
    WHERE tp.Status IN ('Pending','In Progress')
  ) x
")[0]['n'] ?? 0;

$real_count = find_by_sql("
  SELECT COUNT(*) AS n FROM (
    SELECT tr.Sample_ID, tr.Test_Type
    FROM test_realization tr
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_realization
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = tr.Sample_ID AND t.Test_Type = tr.Test_Type AND t.last_dt <=> tr.Register_Date
    WHERE tr.Status IN ('Pending','In Progress')
  ) x
")[0]['n'] ?? 0;

$deliv_count = find_by_sql("
  SELECT COUNT(*) AS n FROM (
    SELECT td.Sample_ID, td.Test_Type
    FROM test_delivery td
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_delivery
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = td.Sample_ID AND t.Test_Type = td.Test_Type AND t.last_dt <=> td.Register_Date
    WHERE td.Status IN ('Pending','In Progress')
  ) x
")[0]['n'] ?? 0;

// ------------------------------
// Listados por etapa
// ------------------------------
// PREPARATION
$prep_rows = find_by_sql("
  SELECT
    l.Sample_ID,
    l.Sample_Number,
   
    l.Material_Type,
    l.Client,
    p.Test_Type,
    p.Technician     AS Stage_Tech,
    p.Status         AS Stage_Status,
    p.Start_Date     AS Stage_Start,
    p.Register_Date  AS Stage_Reg
  FROM lab_test_requisition_form l
  JOIN (
    SELECT tp.*
    FROM test_preparation tp
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_preparation
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = tp.Sample_ID AND t.Test_Type = tp.Test_Type AND t.last_dt <=> tp.Register_Date
    WHERE tp.Status IN ('Pending','In Progress')
  ) p ON p.Sample_ID = l.Sample_ID
  ORDER BY COALESCE(p.Register_Date, '1900-01-01') DESC, l.Sample_ID DESC, p.Test_Type ASC
");

// REALIZATION
$real_rows = find_by_sql("
  SELECT
    l.Sample_ID,
    l.Sample_Number,
    
    l.Material_Type,
    l.Client,
    r.Test_Type,
    r.Technician     AS Stage_Tech,
    r.Status         AS Stage_Status,
    r.Start_Date     AS Stage_Start,
    r.Register_Date  AS Stage_Reg
  FROM lab_test_requisition_form l
  JOIN (
    SELECT tr.*
    FROM test_realization tr
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_realization
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = tr.Sample_ID AND t.Test_Type = tr.Test_Type AND t.last_dt <=> tr.Register_Date
    WHERE tr.Status IN ('Pending','In Progress')
  ) r ON r.Sample_ID = l.Sample_ID
  ORDER BY COALESCE(r.Register_Date, '1900-01-01') DESC, l.Sample_ID DESC, r.Test_Type ASC
");

// DELIVERY
$deliv_rows = find_by_sql("
  SELECT
    l.Sample_ID,
    l.Sample_Number,
  
    l.Material_Type,
    l.Client,
    d.Test_Type,
    d.Technician     AS Stage_Tech,   -- En tu delivery quizás uses Delivered_By; ajusta si aplica
    d.Status         AS Stage_Status,
    d.Start_Date     AS Stage_Start,
    d.Register_Date  AS Stage_Reg
  FROM lab_test_requisition_form l
  JOIN (
    SELECT td.*
    FROM test_delivery td
    JOIN (
      SELECT Sample_ID, Test_Type, MAX(Register_Date) AS last_dt
      FROM test_delivery
      GROUP BY Sample_ID, Test_Type
    ) t ON t.Sample_ID = td.Sample_ID AND t.Test_Type = td.Test_Type AND t.last_dt <=> td.Register_Date
    WHERE td.Status IN ('Pending','In Progress')
  ) d ON d.Sample_ID = l.Sample_ID
  ORDER BY COALESCE(d.Register_Date, '1900-01-01') DESC, l.Sample_ID DESC, d.Test_Type ASC
");

// Rutas de destino (puedes cambiar los nombres de archivos si tus páginas son otras)
function link_for_stage($stage, $sample_id, $test_type){
  $q = '?sample_id='.urlencode($sample_id).'&test='.urlencode($test_type);
  switch ($stage) {
    case 'prep': return '/pages/test-preparation.php' . $q;
    case 'real': return '/pages/test-realization.php' . $q;
    case 'dlv' : return '/pages/test-delivery.php'    . $q;
    default: return '#';
  }
}
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Dashboard de Etapas</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/index.php">Inicio</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  <!-- Tarjetas -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-box-seam fs-1 me-3 text-primary"></i>
          <div>
            <div class="text-muted small">En Preparación</div>
            <div class="h3 m-0"><?php echo (int)$prep_count; ?></div>
          </div>
        </div>
        <div class="card-footer bg-white">
          <a class="text-decoration-none" href="#tab-prep" data-bs-toggle="tab">Ver listado &rarr;</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-activity fs-1 me-3 text-success"></i>
          <div>
            <div class="text-muted small">En Realización</div>
            <div class="h3 m-0"><?php echo (int)$real_count; ?></div>
          </div>
        </div>
        <div class="card-footer bg-white">
          <a class="text-decoration-none" href="#tab-real" data-bs-toggle="tab">Ver listado &rarr;</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-file-earmark-check fs-1 me-3 text-warning"></i>
          <div>
            <div class="text-muted small">Para Entrega</div>
            <div class="h3 m-0"><?php echo (int)$deliv_count; ?></div>
          </div>
        </div>
        <div class="card-footer bg-white">
          <a class="text-decoration-none" href="#tab-dlv" data-bs-toggle="tab">Ver listado &rarr;</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="prep-tab" data-bs-toggle="tab" data-bs-target="#tab-prep" type="button" role="tab">
            <i class="bi bi-box-seam me-1"></i> Preparación
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="real-tab" data-bs-toggle="tab" data-bs-target="#tab-real" type="button" role="tab">
            <i class="bi bi-activity me-1"></i> Realización
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="dlv-tab" data-bs-toggle="tab" data-bs-target="#tab-dlv" type="button" role="tab">
            <i class="bi bi-file-earmark-check me-1"></i> Entrega
          </button>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content">

        <!-- TAB: Preparación -->
        <div class="tab-pane fade show active" id="tab-prep" role="tabpanel" aria-labelledby="prep-tab">
          <div class="table-responsive">
            <table id="tblPrep" class="table table-sm table-hover align-middle w-100">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Sample ID</th>
                  <th>Sample Number</th>
              
                  <th>Material</th>
                  <th>Cliente</th>
                  <th>Test</th>
                  <th>Técnico</th>
                  <th>Status</th>
                  <th>Inicio</th>
                  <th>Registro</th>
                </tr>
              </thead>
              <tbody>
              <?php $i=0; foreach($prep_rows as $row): $i++; $href = link_for_stage('prep', $row['Sample_ID'], $row['Test_Type']); ?>
                <tr data-href="<?php echo e($href); ?>">
                  <td><?php echo $i; ?></td>
                  <td class="fw-semibold"><?php echo e($row['Sample_ID']); ?></td>
                  <td><?php echo e($row['Sample_Number']); ?></td>
                 
                  <td><?php echo e($row['Material_Type']); ?></td>
                  <td><?php echo e($row['Client']); ?></td>
                  <td><?php echo e($row['Test_Type']); ?></td>
                  <td><?php echo e($row['Stage_Tech']); ?></td>
                  <td>
                    <?php
                      $s=(string)$row['Stage_Status']; $badge='secondary';
                      if($s==='Pending') $badge='warning';
                      if($s==='In Progress') $badge='primary';
                      if($s==='Completed') $badge='success';
                    ?>
                    <span class="badge bg-<?php echo $badge; ?>"><?php echo e($s); ?></span>
                  </td>
                  <td><?php echo e($row['Stage_Start']); ?></td>
                  <td><?php echo e($row['Stage_Reg']); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB: Realización -->
        <div class="tab-pane fade" id="tab-real" role="tabpanel" aria-labelledby="real-tab">
          <div class="table-responsive">
            <table id="tblReal" class="table table-sm table-hover align-middle w-100">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Sample ID</th>
                  <th>Sample Number</th>
                 
                  <th>Material</th>
                  <th>Cliente</th>
                  <th>Test</th>
                  <th>Técnico</th>
                  <th>Status</th>
                  <th>Inicio</th>
                  <th>Registro</th>
                </tr>
              </thead>
              <tbody>
              <?php $i=0; foreach($real_rows as $row): $i++; $href = link_for_stage('real', $row['Sample_ID'], $row['Test_Type']); ?>
                <tr data-href="<?php echo e($href); ?>">
                  <td><?php echo $i; ?></td>
                  <td class="fw-semibold"><?php echo e($row['Sample_ID']); ?></td>
                  <td><?php echo e($row['Sample_Number']); ?></td>
                  
                  <td><?php echo e($row['Material_Type']); ?></td>
                  <td><?php echo e($row['Client']); ?></td>
                  <td><?php echo e($row['Test_Type']); ?></td>
                  <td><?php echo e($row['Stage_Tech']); ?></td>
                  <td>
                    <?php
                      $s=(string)$row['Stage_Status']; $badge='secondary';
                      if($s==='Pending') $badge='warning';
                      if($s==='In Progress') $badge='primary';
                      if($s==='Completed') $badge='success';
                    ?>
                    <span class="badge bg-<?php echo $badge; ?>"><?php echo e($s); ?></span>
                  </td>
                  <td><?php echo e($row['Stage_Start']); ?></td>
                  <td><?php echo e($row['Stage_Reg']); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB: Entrega -->
        <div class="tab-pane fade" id="tab-dlv" role="tabpanel" aria-labelledby="dlv-tab">
          <div class="table-responsive">
            <table id="tblDlv" class="table table-sm table-hover align-middle w-100">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Sample ID</th>
                  <th>Sample Number</th>
                
                  <th>Material</th>
                  <th>Cliente</th>
                  <th>Test</th>
                  <th>Técnico</th>
                  <th>Status</th>
                  <th>Inicio</th>
                  <th>Registro</th>
                </tr>
              </thead>
              <tbody>
              <?php $i=0; foreach($deliv_rows as $row): $i++; $href = link_for_stage('dlv', $row['Sample_ID'], $row['Test_Type']); ?>
                <tr data-href="<?php echo e($href); ?>">
                  <td><?php echo $i; ?></td>
                  <td class="fw-semibold"><?php echo e($row['Sample_ID']); ?></td>
                  <td><?php echo e($row['Sample_Number']); ?></td>
                  
                  <td><?php echo e($row['Material_Type']); ?></td>
                  <td><?php echo e($row['Client']); ?></td>
                  <td><?php echo e($row['Test_Type']); ?></td>
                  <td><?php echo e($row['Stage_Tech']); ?></td>
                  <td>
                    <?php
                      $s=(string)$row['Stage_Status']; $badge='secondary';
                      if($s==='Pending') $badge='warning';
                      if($s==='In Progress') $badge='primary';
                      if($s==='Completed' || $s==='Delivered') $badge='success';
                    ?>
                    <span class="badge bg-<?php echo $badge; ?>"><?php echo e($s); ?></span>
                  </td>
                  <td><?php echo e($row['Stage_Start']); ?></td>
                  <td><?php echo e($row['Stage_Reg']); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<!-- Recursos -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.7/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.7/js/dataTables.bootstrap5.min.js"></script>

<style>
  table tbody tr { cursor: pointer; }
  table tbody tr:hover { background: rgba(0,0,0,0.03); }
</style>

<script>
(function(){
  document.addEventListener('click', function(e){
    const tr = e.target.closest('tr[data-href]');
    if(tr){
      const url = tr.getAttribute('data-href');
      if(url) window.location.href = url;
    }
  });

  const commonOpts = {
    paging: true,
    pageLength: 25,
    lengthMenu: [10,25,50,100],
    ordering: true,
    language: { url: "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-ES.json" }
  };

  // Índices de columnas (Inicio=9, Registro=10 en las tres tablas)
  new DataTable('#tblPrep', {...commonOpts, order:[[10,'desc']]});
  new DataTable('#tblReal', {...commonOpts, order:[[10,'desc']]});
  new DataTable('#tblDlv',  {...commonOpts, order:[[10,'desc']]});
})();
</script>

<?php include_once('../components/footer.php'); ?>
