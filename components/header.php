<?php $user = current_user(); ?>
<?php include_once('style.php'); ?>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/app/index.php" class="logo d-flex align-items-center">
        <img src="/app/assets/img/favicon.ico" alt="">
        <span class="d-none d-lg-block">Laboratorio</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="../php/ajax.php" id="sug-form">
        <input type="text" name="title" placeholder="Search" title="Enter search keyword" id="sug_input" list="search-results">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        <datalist id="search-results"></datalist>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

      <li class="nav-item dropdown">
       <?php $username = $user["name"]; ?>
       <?php $Repeat = find_by_sql("SELECT * FROM test_repeat WHERE Register_By = '{$username}' ORDER BY Start_Date DESC"); ?>
       <?php $Reviewed = find_by_sql("SELECT * FROM test_reviewed WHERE Register_By = '{$username}' AND Signed != 1 ORDER BY Start_Date DESC"); ?>

       <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span class="badge bg-primary badge-number"><?php echo count($Repeat) + count($Reviewed); ?></span>
       </a><!-- End Notification Icon -->

       <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
        <li class="dropdown-header">
          Tienes <?php echo count($Repeat) + count($Reviewed); ?> nuevas notificaciones
          <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todo</span></a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        
        <?php
         $count = 0;
         foreach ($Repeat as $repeatItem) : 
          if ($count >= 3) {
            break;
          }
        ?>
        <?php
          $urls = array(
          'AL' => '../reviews/atterberg-limit.php',
          'MC_Oven' => '../reviews/moisture-oven.php',
          'MC_Microwave' => '../reviews/moisture-microwave.php',
          'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
          'GS' => '../reviews/grain-size.php',
          'GS-Fine' => '../reviews/grain-size-fine-agg.php',
          'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
          'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
          'SG' => '../reviews/specific-gravity.php',
          'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
          'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
          'SP' => '../reviews/standard-proctor.php',
          );
           
          $id = $repeatItem['Sample_Name'];
          $number = $repeatItem['Sample_Number'];
          $testType = $repeatItem['Test_Type'];
          $tracking = $repeatItem['Tracking'];
        ?>
        
        <li class="notification-item" onclick="redirectToURL('<?php echo $repeatItem['Test_Type']; ?>', '<?php echo $repeatItem['Tracking']; ?>')">
          <i class="bi bi-exclamation-circle text-warning"></i>
          <div>
            <h4><?php echo $repeatItem['Sample_Name'] . '-' . $repeatItem['Sample_Number'] . '-' . $repeatItem['Test_Type']; ?></h4>
            <p><?php echo $repeatItem['Comment']; ?></p>
            <?php 
             $notificationDate = strtotime($repeatItem['Start_Date']); 
             $timeElapsed = time() - $notificationDate;
             
             $minutesElapsed = floor($timeElapsed / 60);
             $hoursElapsed = floor($minutesElapsed / 60);
             $daysElapsed = floor($hoursElapsed / 24);
             
             if ($daysElapsed > 0) {
                echo "<p>$daysElapsed day" . ($daysElapsed == 1 ? '' : 's') . " ago</p>";
             } elseif ($hoursElapsed > 0) {
                echo "<p>$hoursElapsed hr" . ($hoursElapsed == 1 ? '' : 's') . " ago</p>";
             } else {
                echo "<p>$minutesElapsed min. ago</p>";
             }
            ?>
          </div>
        </li>
        <?php $count++; endforeach; ?>
        <li>
          <hr class="dropdown-divider">
        </li>

        <?php
         $count = 0;
         foreach ($Reviewed as $reviewedItem) : 
          if ($count >= 3) {
            break;
          }
        ?>
        <?php
           $urls = array(
            'AL' => '../reviews/atterberg-limit.php',
            'MC_Oven' => '../reviews/moisture-oven.php',
            'MC_Microwave' => '../reviews/moisture-microwave.php',
            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
            'GS' => '../reviews/grain-size.php',
            'GS-Fine' => '../reviews/grain-size-fine-agg.php',
            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
            'SG' => '../reviews/specific-gravity.php',
            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
            'SP' => '../reviews/standard-proctor.php',
           );
           
           $id = $reviewedItem['Sample_Name'];
           $number = $reviewedItem['Sample_Number'];
           $testType = $reviewedItem['Test_Type'];
           $tracking = $reviewedItem['Tracking'];
        ?>
        
        <li class="notification-item" onclick="redirectToURL('<?php echo $reviewedItem['Test_Type']; ?>', '<?php echo $reviewedItem['Tracking']; ?>')">
          <i class="bi bi-check-circle text-success"></i>
          <div>
            <h4><?php echo $reviewedItem['Sample_Name'] . '-' . $reviewedItem['Sample_Number'] . '-' . $reviewedItem['Test_Type']; ?></h4>
            <p>Enviar a Firma</p>
            <?php 
             $notificationDate = strtotime($reviewedItem['Start_Date']); 
             $timeElapsed = time() - $notificationDate;
             
             $minutesElapsed = floor($timeElapsed / 60);
             $hoursElapsed = floor($minutesElapsed / 60);
             $daysElapsed = floor($hoursElapsed / 24);
             
             if ($daysElapsed > 0) {
              echo "<p>$daysElapsed day" . ($daysElapsed == 1 ? '' : 's') . " ago</p>";
             } elseif ($hoursElapsed > 0) {
              echo "<p>$hoursElapsed hr" . ($hoursElapsed == 1 ? '' : 's') . " ago</p>";
             } else {
              echo "<p>$minutesElapsed min. ago</p>";
             }
            ?>
          </div>
        </li>
        <?php $count++; endforeach; ?>
  
        <li>
          <hr class="dropdown-divider">
        </li>
        <li class="dropdown-footer">
          <a href="../pages/message.php">Mostrar todas las notificaciones</a>
        </li>

       </ul><!-- End Notification Dropdown Items -->
      </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($user['image']); ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo remove_junk(ucfirst($user['name'])); ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo remove_junk(ucfirst($user['name'])); ?></h6>
              <span><?php echo remove_junk(ucfirst($user['username'])); ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="../pages/users-profile.php">
                <i class="bi bi-person"></i>
                <span>Mi perfil</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="../user/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            
            <li>
              <hr class="dropdown-divider">
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
      <?php include_once('menu.php');?>

      <?php elseif($user['user_level'] === '2'): ?>
        <!-- Special user -->
      <?php include_once('menu.php');?>

      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
      <?php include_once('menu.php');?>

  <?php endif;?>

  <script>
    function redirectToURL(testType, tracking) {
        <?php if (isset($urls)) : ?>
            var urls = <?php echo json_encode($urls); ?>;
            var url = urls[testType];
            if (url) {
                // Agrega el parámetro id a la URL
                url += "?id=" + tracking;
                window.location.href = url;
            } else {
                alert("No se encontró una URL para este tipo de prueba.");
            }
        <?php else : ?>
            // Handle error when $urls is undefined
            console.error("Error: $urls is undefined.");
            alert("Error: $urls is undefined. Please contact the administrator.");
        <?php endif; ?>
    }
  </script>
