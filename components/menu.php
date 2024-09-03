<?php
$class_home = !empty($class_home) ? $class_home : "collapsed";
$class_form = !empty($class_form) ? $class_form : "collapsed";
$form_show = !empty($form_show) ? $form_show : " ";
$formPresa = !empty($formPresa) ? $formPresa : "collapsed";
$formPresaShow = !empty($formPresaShow) ? $formPresaShow : " ";
$profile_show = !empty($profile_show) ? $profile_show : "collapsed";
$requisition_form = !empty($requisition_form) ? $requisition_form : "collapsed";
$Sumarios = !empty($Sumarios) ? $Sumarios : "collapsed";
$Pending_List = !empty($Pending_List) ? $Pending_List : "collapsed";
$wepln = !empty($wepln) ? $wepln : "collapsed";
$GrainSize = !empty($GrainSize) ? $GrainSize : " ";
$gsPresa = !empty($gsPresa) ? $gsPresa : " ";
$sgPresa = !empty($sgPresa) ? $sgPresa : " ";
$Moisture = !empty($Moisture) ? $Moisture : " ";
$SG = !empty($SG) ? $SG : " ";
$Density = !empty($Density) ? $Density : " ";
$tracking_show = !empty($tracking_show) ? $tracking_show : " ";
$class_tracking = !empty($class_tracking) ? $class_tracking : "collapsed";
$preparation = !empty($preparation) ? $preparation : " ";
$realization = !empty($realization) ? $realization : " ";
$delivery = !empty($delivery) ? $delivery : " ";
$reviews = !empty($reviews) ? $reviews : " ";
$repeat = !empty($repeat) ? $repeat : " ";
$review = !empty($review) ? $review : "collapsed";
$review_essay = !empty($review_essay) ? $review_essay : "collapsed";
?>
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?php echo $class_home; ?> " href="/pages/home.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $class_tracking; ?>" data-bs-target="#Tracking-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-eye"></i><span>Seguimiento de muestras</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="Tracking-nav" class="nav-content collapse <?php echo $tracking_show; ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="/pages/test-preparation.php" class="<?php echo $preparation; ?>">
              <i class="bi bi-circle"></i><span>Ensayos en preparacion</span>
            </a>
          </li>
          <li>
            <a href="/pages/test-realization.php" class="<?php echo $realization; ?>">
              <i class="bi bi-circle"></i><span>Ensayos en realizacion</span>
            </a>
          </li>
          <li>
            <a href="/pages/test-delivery.php" class="<?php echo $delivery; ?>">
              <i class="bi bi-circle"></i><span>Ensayos en entrega</span>
            </a>
          </li>
          <li>
            <a href="/pages/test-repeat.php" class="<?php echo $repeat; ?>">
              <i class="bi bi-circle"></i><span>Ensayos en repeticion</span>
            </a>
          </li>
          <li>
            <a href="/pages/test-review.php" class="<?php echo $reviews; ?>">
              <i class="bi bi-circle"></i><span>Ensayos en revision</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tracking Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $class_form; ?>" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Formularios de registro</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse <?php echo $form_show; ?> " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/pages/moisture-oven.php" class="<?php echo $Moisture; ?>">
              <i class="bi bi-circle"></i><span>Moisture Content</span>
            </a>
          </li>
          <li>
            <a href="/pages/atterberg-limit.php">
              <i class="bi bi-circle"></i><span>Atterberg Limit</span>
            </a>
          </li>
          <li>
            <a href="/pages/grain-size.php" class="<?php echo $GrainSize; ?>">
              <i class="bi bi-circle"></i><span>Grain Size</span>
            </a>
          </li>
          <li>
            <a href="/pages/specific-gravity.php" class="<?php echo $SG; ?>">
              <i class="bi bi-circle"></i><span>Specific Gravity</span>
            </a>
          </li>
          <li>
            <a href="/pages/standard-proctor.php">
              <i class="bi bi-circle"></i><span>Standard Proctor</span>
            </a>
          </li>
          <li>
            <a href="/pages/LAA-Small.php">
              <i class="bi bi-circle"></i><span>Los Angeles Abrasion</span>
            </a>
          </li>
          <li>
            <a href="/pages/unixial-compressive.php">
              <i class="bi bi-circle"></i><span>UCS</span>
            </a>
          </li>
          <li>
            <a href="/pages/point-load.php">
              <i class="bi bi-circle"></i><span>PLT</span>
            </a>
          </li>
          <li>
            <a href="/pages/brazilian.php">
              <i class="bi bi-circle"></i><span>BTS</span>
            </a>
          </li>
          <li>
            <a href="/pages/leeb.php">
              <i class="bi bi-circle"></i><span>Leeb Hardness</span>
            </a>
          </li>
          <li>
            <a href="/pages/grout.php">
              <i class="bi bi-circle"></i><span>Grout</span>
            </a>
          </li>
          <li>
            <a href="/pages/concrete.php">
              <i class="bi bi-circle"></i><span>Concrete</span>
            </a>
          </li>
          <li>
            <a href="/pages/pinhole-test.php">
              <i class="bi bi-circle"></i><span>Pinhole</span>
            </a>
          </li>
          <li>
            <a href="/pages/soundness.php">
              <i class="bi bi-circle"></i><span>Soundness</span>
            </a>
          </li>
          <li>
            <a href="/pages/density-bulk.php" class="<?php echo $Density; ?>">
              <i class="bi bi-circle"></i><span>Density</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $formPresa; ?>" data-bs-target="#forms-presa" data-bs-toggle="collapse" href="#">
          <i class="bi bi-file-text"></i><span>Construccion</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-presa" class="nav-content collapse <?php echo $formPresaShow; ?> " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/pages/grain-size-lpf.php" class="<?php echo $gsPresa; ?>">
              <i class="bi bi-circle"></i><span>Grain Size</span>
            </a>
          </li>
          <li>
            <a href="/pages/atterberg-limit-tsf.php">
              <i class="bi bi-circle"></i><span>Atterberg Limit</span>
            </a>
          </li>
          <li>
            <a href="/pages/specific-gravity-fine-filter.php" class="<?php echo $sgPresa; ?>">
              <i class="bi bi-circle"></i><span>Specific Gravity</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-heading">paginas</li>

      <li class="nav-item">
        <a class="nav-link <?php echo $Sumarios; ?>" href="/pages/sumary.php">
          <i class="bi bi-database"></i>
          <span>Sumarios</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $Pending_List; ?>" href="/pages/pendings-list.php">
          <i class="bi bi-question-circle"></i>
          <span>Lista de Pendientes</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $wepln; ?>" href="/pages/weekly-planning.php">
          <i class="bi bi-calendar3"></i>
          <span>Planificación Semanal</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $review; ?>" href="/pages/essay-review.php">
          <i class="bi bi-card-checklist"></i>
          <span>Revisión de ensayo</span>
        </a>
      </li><!-- End Review Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $review_essay; ?>" href="/pages/essay.php">
          <i class="bi bi-database"></i>
          <span>Ensayos Registrados</span>
        </a>
      </li><!-- End Review Essay Page Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo $requisition_form; ?>" href="/pages/requisition-form.php">
          <i class="bi bi-file-earmark"></i>
          <span>Formulario de requisicion</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-heading">Configuracion</li>

      <!-- User Profile -->
      <li class="nav-item">
        <a class="nav-link <?php echo $profile_show; ?>" href="/pages/users-profile.php">
          <i class="bi bi-person"></i>
          <span>Perfil</span>
        </a>
      </li>
      <!-- End User Profile -->

      <!-- New Account -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="/pages/users-register.php">
          <i class="bi bi-card-list"></i>
          <span>Nueva cuenta</span>
        </a>
      </li>
      <!-- End New Account -->

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