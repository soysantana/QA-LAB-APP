<?php
$class_home = !empty($class_home) ? $class_home : "collapsed";
$class_form = !empty($class_form) ? $class_form : "collapsed";
$form_show = !empty($form_show) ? $form_show : " ";
$profile_show = !empty($profile_show) ? $profile_show : "collapsed";
$requisition_form = !empty($requisition_form) ? $requisition_form : "collapsed";
?>
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?php echo $class_home; ?> " href="../pages/home.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Components</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Alerts</span>
            </a>
          </li>
          <li>
            <a href="components-accordion.html">
              <i class="bi bi-circle"></i><span>Accordion</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $class_form; ?>" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Register Forms</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse <?php echo $form_show; ?> " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/app/pages/atterberg-limit.php">
              <i class="bi bi-circle"></i><span>Atterberg Limit</span>
            </a>
          </li>
          <li>
            <a href="forms-layouts.html">
              <i class="bi bi-circle"></i><span>Grain Size</span>
            </a>
          </li>
          <li>
            <a href="forms-editors.html">
              <i class="bi bi-circle"></i><span>SG</span>
            </a>
          </li>
          <li>
            <a href="forms-validation.html">
              <i class="bi bi-circle"></i><span>Density</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link <?php echo $profile_show; ?>" href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-register.php">
          <i class="bi bi-card-list"></i>
          <span>New Account</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $requisition_form; ?>" href="requisition-form.php">
          <i class="bi bi-file-earmark"></i>
          <span>Requisition Form</span>
        </a>
      </li><!-- End Register Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <script>
// Obtén la URL actual para determinar la página actual
const currentPage = window.location.href;

// Obtén los enlaces de la barra lateral
const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-item a');

// Itera a través de los enlaces y resalta el enlace correspondiente
sidebarLinks.forEach(link => {
    if (currentPage.includes(link.getAttribute('href'))) {
        link.classList.add('active'); // Puedes aplicar estilos CSS a la clase 'active'
    }
});

</script>