<?php
// components/menu_reportes.php
$page_title   = 'Reportes del Laboratorio';
$menu_active  = 'reportes';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Reportes del Laboratorio</h1>
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

      <!-- =========================
           Reportes
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Reportes Disponibles</h6>
      </div>

      <!-- Reporte Diario -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reporteDiario.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body text-center">
              <i class="bi bi-calendar-day" style="font-size:2rem;"></i>
              <h5 class="card-title mt-2">Digitar Reporte Diario</h5>
              <p class="card-text small text-muted">Generar o consultar reportes por día.</p>
            </div>
          </div>
        </a>
      </div>
       <!-- Reporte Diario -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/lista_reportes_mensuales.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body text-center">
              <i class="bi bi-calendar-day" style="font-size:2rem;"></i>
              <h5 class="card-title mt-2">Listado Reporte Diario</h5>
              <p class="card-text small text-muted">Generar o consultar reportes por día.</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Reporte Semanal -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/lista_reportes-semanales.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body text-center">
              <i class="bi bi-calendar-week" style="font-size:2rem;"></i>
              <h5 class="card-title mt-2">Reporte Semanal</h5>
              <p class="card-text small text-muted">Resumen por semana ISO.</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Reporte Mensual -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/lista_reportes_mensuales.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body text-center">
              <i class="bi bi-calendar-month" style="font-size:2rem;"></i>
              <h5 class="card-title mt-2">Reporte Mensual</h5>
              <p class="card-text small text-muted">Cantidad y estatus mensual del laboratorio.</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Reporte Trimestral -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reporteTrimestral.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body text-center">
              <i class="bi bi-calendar2-quarter" style="font-size:2rem;"></i>
              <h5 class="card-title mt-2">Reporte Trimestral</h5>
              <p class="card-text small text-muted">Análisis y tendencias por trimestre.</p>
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
</style>
