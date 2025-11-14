<?php
// components/menu_reporte_diario.php
$page_title   = 'Reportes del laboratorio';
$menu_active  = 'reportes';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

$current_user = current_user();
$job_raw      = $current_user['job'] ?? '';
$job          = strtolower(trim($job_raw));

$is_technical = (strpos($job, 'technical') !== false);
$is_supervisor = (strpos($job, 'supervisor') !== false);
$is_doccontrol = (strpos($job, 'document control') !== false);

// Solo técnico "puro" (no supervisor / doc control)
$is_pure_tech = $is_technical && !$is_supervisor && !$is_doccontrol;
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Reportes del laboratorio</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Reportes</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <?php if (!$is_pure_tech): ?>
      <!-- =========================
           Reportes por período
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Reportes por período</h6>
      </div>

      <!-- Reporte diario -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reporteDiario.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-day me-2"></i>
                <h5 class="card-title mb-0">Reporte diario del laboratorio</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Resumen diario de muestras, ensayos realizados, entregas y observaciones.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Reportes diarios (listado) -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reporte_diario_lab.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-range me-2"></i>
                <h5 class="card-title mb-0">Listado de Reportes Diarios</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado de todos los resportes de una semana.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Ensayos entregados por semana ISO -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reportes_semana_iso.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar2-week me-2"></i>
                <h5 class="card-title mb-0">Ensayos entregados por semana ISO</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Cantidad de ensayos entregados agrupados por año, mes y semana ISO.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Operación y Desempeño
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Operación y Desempeño </h6>
      </div>

      <!-- Revisión de Ensayos -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/essay-review.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-database me-2"></i>
                <h5 class="card-title mb-0">Revisión de Ensayos</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado general de ensayos registrados en el sistema con filtros por fecha, cliente y tipo.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Desempeño de técnicos -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/rendimiento.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-lines-fill me-2"></i>
                <h5 class="card-title mb-0">Desempeño de técnicos</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Desglose de actividades por técnico (registro, preparación, realización, entrega, revisión).
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Ensayos registrados -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/essay.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-database me-2"></i>
                <h5 class="card-title mb-0">Ensayos registrados</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado general de ensayos registrados en el sistema con filtros por fecha, cliente y tipo.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Clientes y cumplimiento
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Clientes y cumplimiento</h6>
      </div>

      <!-- Detalle cliente -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/detalle-cliente.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-people me-2"></i>
                <h5 class="card-title mb-0">Detalle por cliente</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Resumen de muestras y ensayos por cliente, año y período, con volúmenes y tendencias.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Firma y documentación
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Firma y documentación</h6>
      </div>

      <!-- Firma de resultados -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/docs_list.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-pen me-2"></i>
                <h5 class="card-title mb-0">Firma de resultados</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Gestión de reportes listos para firma, versiones y control de documentos emitidos.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Sumarios generales -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/sumary.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-card-list me-2"></i>
                <h5 class="card-title mb-0">Sumarios generales</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Sumarios de ensayos y muestras por período con indicadores generales del laboratorio.
              </p>
            </div>
          </div>
        </a>
      </div>
      <?php endif; ?>

      <!-- =========================
           Hojas de trabajo
           (SIEMPRE visible, incluso para técnicos)
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Hojas de trabajo</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/components/menu_hojastrabajos.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-clipboard-check me-2"></i>
                <h5 class="card-title mb-0">Hojas de trabajo</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Acceso rápido a las hojas de trabajo de los diferentes ensayos del laboratorio.
              </p>
            </div>
          </div>
        </a>
      </div>

    </div><!-- End row -->
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php'); ?>

<style>
  .hover-elevate {
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .hover-elevate:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08);
  }
  .card-title {
    font-size: 0.98rem;
  }
  .card-text {
    font-size: 0.8rem;
  }
</style>
