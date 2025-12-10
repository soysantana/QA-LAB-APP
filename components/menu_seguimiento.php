<?php
// components/menu_seguimiento.php
$page_title   = 'Seguimiento de Muestras y Ensayos';
$menu_active  = 'seguimiento';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

$current_user = current_user();
$job_raw      = $current_user['job'] ?? '';
$job          = strtolower(trim($job_raw));

$is_technical   = (strpos($job, 'technical') !== false);
$is_supervisor  = (strpos($job, 'supervisor') !== false);
$is_doccontrol  = (strpos($job, 'document control') !== false);

// Técnico "puro": ve todo este menú excepto "Ensayos en Revisión".
// Planificación y Rotación siempre visibles, luego controlas edición dentro de esas vistas.
$is_pure_tech = $is_technical && !$is_supervisor && !$is_doccontrol;
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Seguimiento de Muestras</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Seguimiento</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <!-- =========================
           PROCESO PRINCIPAL
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Proceso</h6>
      </div>

      <!-- Procesos de ensayos (Kanban) -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/kanban_muestras.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-kanban me-2"></i>
                <h5 class="card-title mb-0">Procesos de Ensayos</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Vista completa del flujo de ensayos: Registrado → Preparación → Realización → Repetición → Entrega.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Control de ensayo de concreto -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/control_ensayo_concreto.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-kanban me-2"></i>
                <h5 class="card-title mb-0">Control de Ensayo de Concreto</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado de ensayos de concreto para el seguimiento de rotura de probetas.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           CONTROL DE CALIDAD
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Control de Calidad</h6>
      </div>

     

      <!-- Ensayos en revisión (OCULTO para técnico puro) -->
      <?php if (!$is_pure_tech): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/test-review.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-search me-2"></i>
                <h5 class="card-title mb-0">Ensayos en Revisión</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos completados pendientes de verificación y validación de resultados.
              </p>
            </div>
          </div>
        </a>
      </div>
      <?php endif; ?>

      <!-- =========================
           ESTADO DE MUESTRAS
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Estado de Muestras</h6>
      </div>

      <!-- Muestras a botar -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/bandejas_descartar.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-clipboard-plus me-2"></i>
                <h5 class="card-title mb-0">Muestras a Botar</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado de muestras a descartar.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Ensayos pendientes -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/pendings-list.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-clock-history me-2"></i>
                <h5 class="card-title mb-0">Lista de Ensayos Pendientes</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos solicitados que aún no han sido entregados.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           PLANIFICACIÓN
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Planificación</h6>
      </div>

      <!-- Planificación diaria (VISIBLE para todos; control de edición dentro de weekly-planning.php) -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/weekly-planning.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-check me-2"></i>
                <h5 class="card-title mb-0">Planificación Diaria</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Planificación de actividades y ensayos por día y por semana para el laboratorio.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Rotación laboral (VISIBLE para todos; control de edición dentro de job-rotation.php) -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/job-rotation.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-people me-2"></i>
                <h5 class="card-title mb-0">Rotación Laboral</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Seguimiento de la rotación de personal y asignación de turnos en el laboratorio.
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
  /* Toque visual para las tarjetas del hub de seguimiento */
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
