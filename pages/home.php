<?php
  $page_title = 'Home';
  $class_home = ' ';
  require_once('../config/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('../index.php', false);}
?>
<?php include_once('../components/header.php'); ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<?php echo display_msg($msg); ?>


</main><!-- End #main -->
<?php include_once('../components/footer.php'); ?>