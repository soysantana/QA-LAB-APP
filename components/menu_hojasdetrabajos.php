<?php
// components/menu_hojastrabajos.php
$page_title   = 'Ensayos de laboratorio';
$menu_active  = 'ensayos'; // opcional, por si lo usas en el header
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Ensayos de laboratorio</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Ensayos de laboratorio</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <!-- =========================
           Propiedades índice
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Propiedades índice</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/moisture-content-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-droplet-half me-2"></i>
                <h5 class="card-title mb-0">Moisture Content</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos de contenido de humedad (MC) por distintos métodos.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/atterberg-limit.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-sliders me-2"></i>
                <h5 class="card-title mb-0">Atterberg Limit</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Límite líquido, plástico e índice de plasticidad según ASTM.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/reactivity-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-bezier me-2"></i>
                <h5 class="card-title mb-0">Reactividad</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos de reactividad Acida.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Granulometría / Peso específico
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Granulometría y gravedad específica</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/grain-size-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-funnel me-2"></i>
                <h5 class="card-title mb-0">Grain Size</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Análisis granulométrico (tamices / hidrómetro) para suelos y agregados.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/specific-gravity-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-speedometer2 me-2"></i>
                <h5 class="card-title mb-0">Specific Gravity</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Gravedad específica de suelos y agregados (coarse/fine).
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Compactación / densidad
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Compactación y densidad</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/standard-proctor.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-graph-up me-2"></i>
                <h5 class="card-title mb-0">Standard Proctor</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Curvas de compactación y parámetros óptimos de humedad/densidad.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/densidades-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-box me-2"></i>
                <h5 class="card-title mb-0">Density</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos de densidad de campo y laboratorio (Gamma, arena, etc.).
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/sand-castle-test.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-building me-2"></i>
                <h5 class="card-title mb-0">Sand Castle Test</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos especiales de estabilidad tipo “Sand Castle”.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Rocas / concreto
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Rocas y concreto</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/compressive-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-stopwatch me-2"></i>
                <h5 class="card-title mb-0">Compresión</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos de compresión simple para concreto y roca.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/point-Load.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-record-circle me-2"></i>
                <h5 class="card-title mb-0">Carga Puntual (PLT)</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayo de carga puntual en testigos de roca.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/brazilian.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-record2 me-2"></i>
                <h5 class="card-title mb-0">Brazilian</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayo brasileño de tracción indirecta en roca.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Durabilidad / dureza
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Durabilidad y dureza</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/LAA-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-tornado me-2"></i>
                <h5 class="card-title mb-0">Los Angeles Abrasion</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayo de abrasión para agregados (coarse / filter).
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/soundness.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-shield-check me-2"></i>
                <h5 class="card-title mb-0">Soundness</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayo de sanidad y resistencia a ciclos de sulfato.
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/leeb-hardness.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-hexagon-half me-2"></i>
                <h5 class="card-title mb-0">Leeb Hardness</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Medición de dureza con equipo Leeb portátil.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Otros / especiales
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Otros ensayos</h6>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/dispercion-menu.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-diagram-2 me-2"></i>
                <h5 class="card-title mb-0">Dispersión</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Ensayos de dispersión de suelos y clasificación correspondiente.
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
  /* Toque visual para las tarjetas del menú de ensayos */
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
