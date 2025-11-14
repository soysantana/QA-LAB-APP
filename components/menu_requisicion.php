<?php
// components/menu_requisicion.php
$page_title   = 'Requisición de Muestras';
$menu_active  = 'requisicion';
require_once('../config/load.php');
page_require_level(3);
$user      = current_user();
$userLevel = $user['user_level'] ?? 99; // 1=admin, 2=supervisor, 3=técnico, etc.

function can_view(?array $roles = null): bool {
    // Sin roles definidos → visible para todos
    if ($roles === null || $roles === []) {
        return true;
    }

    global $userLevel;
    return in_array($userLevel, $roles, true);
}
include_once('../components/header.php');
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Requisición de Muestras</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Requisición</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <!-- =========================
           Requisición y registro
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Requisición y registro</h6>
      </div>
       <!-- Muestras del día -->
        
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/samples_today.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-clipboard-data me-2"></i>
                <h5 class="card-title mb-0">Numero de Muestra Siguiente</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Todas las muestras registradas en la fecha actual y su resumen de ensayos.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Nueva requisición -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/requisition-form.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-file-earmark-plus me-2"></i>
                <h5 class="card-title mb-0">Nueva requisición</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Registrar nuevas muestras y ensayos solicitados al laboratorio.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Requisiciones recientes / listado general -->
      <div class="col-12 col-md-6 col-lg-4">
        <!-- Ajusta la ruta a tu listado real de requisiciones -->
        <a href="/pages/requisition-form-view.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-list-ul me-2"></i>
                <h5 class="card-title mb-0">Muestras Registradas</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Listado de requisiciones y muestras registradas.
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
  /* Toque visual para las tarjetas del hub de requisición */
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
