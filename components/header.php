<?php $user = current_user(); ?>
<?php include_once('style.php'); ?>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="/index.php" class="logo d-flex align-items-center">
      <img src="/assets/img/favicon.ico" alt="">
      <span class="d-none d-lg-block">Laboratorio</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="POST" action="../php/ajax.php" id="sug-form">
      <input type="text" name="title" placeholder="Search" id="sug_input" list="search-results">
      <button type="submit"><i class="bi bi-search"></i></button>
      <datalist id="search-results"></datalist>
    </form>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <!-- 🔔 CAMPANA DE NOTIFICACIONES -->
      <li class="nav-item dropdown">

        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span id="notif-count" class="badge bg-primary badge-number">0</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="width:330px;">
          <li class="dropdown-header">
            Notificaciones del laboratorio
            <a href="../pages/message.php">
              <span class="badge rounded-pill bg-primary p-2 ms-2">Ver todo</span>
            </a>
          </li>

          <li>
            <hr class="dropdown-divider">
          </li>

          <div id="notif-list"></div>

          <li>
            <hr class="dropdown-divider">
          </li>
          <li class="dropdown-footer">
            <a href="../pages/message.php">Mostrar todas las notificaciones</a>
          </li>
        </ul>

      </li><!-- End Notification Nav -->

      <!-- Perfil -->
      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">

          <?php if (!empty($row['photo'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['photo']); ?>" class="rounded-circle">
          <?php else: ?>
            <img src="../assets/img/profile-default.jpg" class="rounded-circle">
          <?php endif; ?>

          <span class="d-none d-md-block dropdown-toggle ps-2">
            <?= remove_junk(ucfirst($user['name'])); ?>
          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= remove_junk(ucfirst($user['name'])); ?></h6>
            <span><?= remove_junk(ucfirst($user['username'])); ?></span>
          </li>

          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="../pages/users-profile.php">
              <i class="bi bi-person"></i><span>Mi perfil</span>
            </a>
          </li>

          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="../user/logout.php">
              <i class="bi bi-box-arrow-right"></i><span>Cerrar sesión</span>
            </a>
          </li>

        </ul>
      </li>

    </ul>
  </nav>
</header>

<!-- Menú por roles -->
<?php if ($user['user_level'] === '1'): ?>
  <?php include_once('menu.php'); ?>
<?php elseif ($user['user_level'] === '2'): ?>
  <?php include_once('menu-lv2.php'); ?>
<?php elseif ($user['user_level'] === '3'): ?>
  <?php include_once('menu-tecnico.php'); ?>
<?php elseif ($user['user_level'] === '4'): ?>
  <?php include_once('menu-lv4.php'); ?>
<?php endif; ?>