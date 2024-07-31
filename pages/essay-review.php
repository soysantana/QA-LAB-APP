<?php
$page_title = 'Essay Review';
$review = 'show';
require_once('../config/load.php');

// Add any necessary functions or includes here
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Essay Review</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Essay Review</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">

        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Essay menu under review</h5>
              <div class="accordion accordion-flush" id="accordionFlushExample">

              <?php
                function fetchData($tableName, $applyDateFilter = false)
                {
                 if ($applyDateFilter) {
                   $week = date('Y-m-d', strtotime('-7 days'));
                  
                   return find_by_sql("SELECT * FROM {$tableName} WHERE Registed_Date >= '{$week}'");
                 } else {
                   return find_all($tableName);
                 }
                }
               // Array of tables and their display names
               $tables = [
                'moisture_oven' => 'Moisture Oven',
                'moisture_constant_mass' => 'Moisture Constant Mass',
                'moisture_microwave' => 'Moisture Microwave',
                'atterberg_limit' => 'Atterberg Limit',
                'grain_size_general' => 'Grain Size General',
                'grain_size_fine' => 'Grain Size Fine',
                'grain_size_coarse' => 'Grain Size Coarse',
                'grain_size_coarsethan' => 'Grain Size Coarsethan',
                'specific_gravity' => 'Specific Gravity',
                'specific_gravity_coarse' => 'Specific Gravity Coarse',
                'specific_gravity_fine' => 'Specific Gravity Fine',
                // Add more tables as needed
               ];
              
               // Loop through the tables
               foreach ($tables as $tableName => $displayName) {
                // Fetch data for the current table
                $data = fetchData($tableName, true);

                // Check if data is empty
                if (empty($data)) {
                  continue; // Skip the table if no data
                }
    
                // Display accordion for the current table
                echo '
                <div class="accordion-item">
                <h2 class="accordion-header" id="flush-heading' . $tableName . '">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' . $tableName . '" aria-expanded="false" aria-controls="flush-collapse' . $tableName . '">
                ' . $displayName . '
                </button>
                </h2>
                <div id="flush-collapse' . $tableName . '" class="accordion-collapse collapse" aria-labelledby="flush-heading' . $tableName . '" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">';
              
                foreach ($data as $entry) {
                  $testType = $entry['Test_Type'];
                  $link = '';
                  
                  switch ($testType) {
                    case 'AL':
                      $link = '../reviews/atterberg-limit.php?id=' . $entry['id'];
                      break;
                    case 'MC_Oven':
                      $link = '../reviews/moisture-oven.php?id=' . $entry['id'];
                      break;
                    case 'MC_Microwave':
                      $link = '../reviews/moisture-microwave.php?id=' . $entry['id'];
                      break;
                    case 'MC_Constant_Mass':
                      $link = '../reviews/moisture-constant-mass.php?id=' . $entry['id'];
                      break;
                    case 'GS':
                      $link = '../reviews/grain-size.php?id=' . $entry['id'];
                      break;
                    case 'GS-Fine':
                      $link = '../reviews/grain-size-fine-agg.php?id=' . $entry['id'];
                      break;
                    case 'GS-Coarse':
                      $link = '../reviews/grain-size-coarse-agg.php?id=' . $entry['id'];
                      break;
                    case 'GS-CoarseThan':
                      $link = '../reviews/grain-size-coarsethan-agg.php?id=' . $entry['id'];
                      break;
                    case 'SG':
                      $link = '../reviews/specific-gravity.php?id=' . $entry['id'];
                      break;
                    case 'SG-Coarse':
                      $link = '../reviews/specific-gravity-coarse-aggregates.php?id=' . $entry['id'];
                      break;
                    case 'SG-Fine':
                      $link = '../reviews/specific-gravity-fine-aggregate.php?id=' . $entry['id'];
                      break;
                      
                      default:
                      $link = '#';
                      break;
                    }

                    echo '<a href="' . $link . '" class="text-danger">' . $entry['Sample_ID'] . '-' . $entry['Sample_Number'] . '</a><br>';
                  }           
                
                echo '</div>
                </div>
                </div>';
               }
              ?>

              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-6">
          
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Essay menu under review</h5>
            <div class="accordion accordion-flush" id="accordionFlushExample">
              
              <?php
                function fetchData2($tableName, $applyDateFilter = false)
                {
                 if ($applyDateFilter) {
                   $week = date('Y-m-d', strtotime('-7 days'));
                  
                   return find_by_sql("SELECT * FROM {$tableName} WHERE Registed_Date >= '{$week}'");
                 } else {
                   return find_all($tableName);
                 }
                }
              
               $tables = [
                'standard_proctor' => 'Standard Proctor',
                'point_load' => 'Point Load',
                'unixial_compressive' => 'Unixial Compressive',
                'brazilian' => 'Brazilian',
                'los_angeles_abrasion_coarse_filter' => 'LAA Small',
               ];
    
               foreach ($tables as $tableName => $displayName) {
                $data = fetchData2($tableName, true);
                
                // Check if data is empty
                if (empty($data)) {
                  continue; // Skip the table if no data
                }
      
                echo ' <div class="accordion-item"> <h2 class="accordion-header" id="flush-heading' . $tableName . '">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' . $tableName . '" aria-expanded="false" aria-controls="flush-collapse' . $tableName . '">
                ' . $displayName . '
                </button> </h2>
                <div id="flush-collapse' . $tableName . '" class="accordion-collapse collapse" aria-labelledby="flush-heading' . $tableName . '" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">';
    
                foreach ($data as $entry) {
                  $testType = $entry['Test_Type'];
                  $link = '';

                  switch ($testType) {
                    case 'SP':
                      $link = '../reviews/standard-proctor.php?id=' . $entry['id'];
                      break;
                    case 'PLT':
                      $link = '../reviews/point-load.php?id=' . $entry['id'];
                      break;
                    case 'UCS':
                      $link = '../reviews/unixial-compressive.php?id=' . $entry['id'];
                      break;
                    case 'BTT':
                      $link = '../reviews/brazilian.php?id=' . $entry['id'];
                      break;
                    case 'LAA_Coarse_Filter':
                      $link = '../reviews/LAA-Small.php?id=' . $entry['id'];
                      break;
            
                      default:
                      $link = '#';
                      break;
                    }

                    echo '<a href="' . $link . '" class="text-danger">' . $entry['Sample_ID'] . '-' . $entry['Sample_Number'] . '</a><br>';
                  }           
                  
                  echo ' </div> </div> </div>' ;
                 }
              ?>
                
              </div>
            </div>
          </div>

        </div>

        </div>
    </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>
