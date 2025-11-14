<?php
// components/menu_configuracion.php
$page_title   = 'Configuración del usuario y sistema';
$menu_active  = 'configuracion';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

$current_user = current_user();
$job_raw      = $current_user['job'] ?? '';
$job          = strtolower(trim($job_raw));

$is_technical   = (strpos($job, 'technical') !== false);
$is_supervisor  = (strpos($job, 'supervisor') !== false);
$is_doccontrol  = (strpos($job, 'document control') !== false);

// Técnico "puro": solo puede ver PERFIL
$is_pure_tech = $is_technical && !$is_supervisor && !$is_doccontrol;
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Configuración</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Configuración</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <!-- =========================
           Perfil
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Usuario</h6>
      </div>

      <!-- Perfil (visible para todos) -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/users-profile.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-circle me-2"></i>
                <h5 class="card-title mb-0">Perfil</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Actualizar tus datos personales, contraseña y preferencias del usuario.
              </p>
            </div>
          </div>
        </a>
      </div>

      <?php if (!$is_pure_tech): ?>
      <!-- =========================
           Administración (solo no-técnicos puros)
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Administración</h6>
      </div>

      <!-- Nueva cuenta -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/users-register.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-plus me-2"></i>
                <h5 class="card-title mb-0">Nueva cuenta</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Registrar nuevos usuarios en el sistema con su rol y nivel de acceso.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- Usuarios / Grupos -->
      <div class="col-12 col-md-6 col-lg-4">
        <a href="/pages/users-group.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-people-gear me-2"></i>
                <h5 class="card-title mb-0">Usuarios / Grupos</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Administración de roles, grupos de usuarios y niveles de acceso del laboratorio.
              </p>
            </div>
          </div>
        </a>
      </div>
      <?php endif; ?>

    </div><!-- End row -->
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php'); ?>

<style>
  /* Toque visual para las tarjetas del hub de configuración */
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
