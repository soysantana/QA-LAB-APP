<?php
 $page_title = "Home";
 $class_home = " ";
 require_once "../config/load.php";
 if (!$session->isUserLoggedIn(true)) {
    redirect("/index.php", false);
 }
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

<section class="section dashboard" class="">
  <div class="row">

  <!-- Left side columns -->
  <div class="col-lg-8">
    <div class="row">

    <!-- Process Database Requision -->
    <div class="col-12">
      <div class="card recent-sales overflow-auto">

      <div class="filter">
        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <li class="dropdown-header text-start">
            <h6>Filter</h6>
          </li>
        
          <li><a class="dropdown-item" href="#">Hoy</a></li>
          <li><a class="dropdown-item" href="#">Este mes</a></li>
          <li><a class="dropdown-item" href="#">Este año</a></li>
        </ul>
      </div>

      <div class="card-body">
        <h5 class="card-title">Proceso de muestreo <span>| Hoy</span></h5>

        <table class="table table-borderless datatable">
          <thead>
            <tr>
              <th scope="col">Muestra</th>
              <th scope="col">Numero de muestra</th>
              <th scope="col">Tipo de prueba</th>
              <th scope="col">Estado</th>
            </tr>
          </thead>
          <tbody>
        <?php
          $Requisitions = find_all('lab_test_requisition_form');
          $testTypes = [];

          foreach ($Requisitions as $requisition) {
            for ($i = 1; $i <= 20; $i++) {
              $testTypeKey = 'Test_Type' . $i;
              
              if (isset($requisition[$testTypeKey]) && $requisition[$testTypeKey] !== null && $requisition[$testTypeKey] !== '') {
                $testTypes[$requisition[$testTypeKey]][] = [
                  'Sample_ID' => $requisition['Sample_ID'],
                  'Sample_Number' => $requisition['Sample_Number'],
                  'Test_Type' => $requisition[$testTypeKey],
                ];
              }
            }
          }

          foreach ($testTypes as $testType => $data) {
            foreach ($data as $item) {

              $status = getStatus($item['Sample_ID'], $item['Sample_Number'], $item['Test_Type']);
              $allowedStatuses = ['Preparation', 'Realization', 'Delivery', 'Review', 'Repeat'];
              
              if (in_array($status, $allowedStatuses)) {
              echo '<tr>';
              echo '<td>' . $item['Sample_ID'] . '</td>';
              echo '<td>' . $item['Sample_Number'] . '</td>';
              echo '<td>' . $item['Test_Type'] . '</td>';
              

              $status = getStatus($item['Sample_ID'], $item['Sample_Number'], $item['Test_Type']);

              if ($status == 'Preparation') {
                echo '<td><span class="badge bg-success">Preparación</span></td>';
              } elseif ($status == 'Realization') {
                echo '<td><span class="badge bg-success">Realización</span></td>';
              } elseif ($status == 'Delivery') {
                echo '<td><span class="badge bg-success">Entrega</span></td>';
              } elseif ($status == 'Review') {
                echo '<td><span class="badge bg-success">Revisar</span></td>';
              } elseif ($status == 'Repeat') {
                echo '<td><span class="badge bg-warning">Repetir</span></td>';
              } else {
                echo '<td><span class="badge bg-danger">----</span></td>';
              }
    
              echo '</tr>';
            }
          }
         }
          
          function getStatus($sampleID, $sampleNumber, $testType) {
            $statusFromPreparation = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_preparation');
            $statusFromRealization = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_realization');
            $statusFromDelivery = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_delivery');
            $statusFromReview = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_review');
            $statusFromRepeat = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_repeat');
            
            if ($statusFromRepeat != 'NoStatusFound') {
              return $statusFromRepeat;
            } elseif ($statusFromReview != 'NoStatusFound') {
              return $statusFromReview;
            } elseif ($statusFromDelivery != 'NoStatusFound') {
              return $statusFromDelivery;
            } elseif ($statusFromRealization != 'NoStatusFound') {
              return $statusFromRealization;
            } elseif ($statusFromPreparation != 'NoStatusFound') {
              return $statusFromPreparation;
            } else {
              return 'NoStatusFound';
            }
          }
          
           function getStatusFromTestTable($sampleID, $sampleNumber, $testType, $tableName) {
            $testData = find_all($tableName);
            
            $matchingResults = array_filter($testData, function ($row) use ($sampleID, $sampleNumber, $testType) {
              return $row['Sample_Name'] == $sampleID && $row['Sample_Number'] == $sampleNumber && $row['Test_Type'] == $testType;
            });
            
            if (!empty($matchingResults)) {
              $lastResult = end($matchingResults);
              return $lastResult['Status'];
            }
            return 'NoStatusFound';
          }

        ?>

          </tbody>
        </table>

      </div>

    </div>
  </div><!-- End Process Database Requision -->

      <!-- Method Proctor -->
      <div class="col-12">
       <div class="card recent-sales overflow-auto">

       <div class="filter">
        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <li class="dropdown-header text-start">
            <h6>Filtrar</h6>
          </li>
        
          <li><a class="dropdown-item" href="#">Hoy</a></li>
          <li><a class="dropdown-item" href="#">Este mes</a></li>
          <li><a class="dropdown-item" href="#">Este año</a></li>
        </ul>
      </div>

      <div class="card-body">
        <h5 class="card-title">Metodo Para Proctor <span>| Hoy</span></h5>

        <table class="table table-borderless datatable">
          <thead>
            <tr>
              <th scope="col">Muestra</th>
              <th scope="col">Metodo</th>
              <th scope="col">Comentario</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $Requisition = find_all("lab_test_requisition_form");
              $Preparation = find_all("test_preparation");
              $Review = find_all("test_review");
              $testTypes = [];

                foreach ($Requisition as $requisition) {
                  for ($i = 1; $i <= 20; $i++) {
                    $testTypeKey = "Test_Type" . $i;

                    if (
                      isset($requisition[$testTypeKey]) &&
                      $requisition[$testTypeKey] !== null &&
                      $requisition[$testTypeKey] !== ""
                      ) {
                        $matchingPreparations = array_filter($Preparation, function ($preparation) use ($requisition, $testTypeKey) {
                        
                          return $preparation["Sample_Name"] === $requisition["Sample_ID"] &&
                          $preparation["Sample_Number"] === $requisition["Sample_Number"] &&
                          $preparation["Test_Type"] === $requisition[$testTypeKey];
                        });
                        
                        $matchingReviews = array_filter($Review, function ($review) use ($requisition, $testTypeKey) {
                          return $review["Sample_Name"] === $requisition["Sample_ID"] &&
                          $review["Sample_Number"] === $requisition["Sample_Number"] &&
                          $review["Test_Type"] === $requisition[$testTypeKey];
                        });

                        if (empty($matchingPreparations) && empty($matchingReviews)) {
                          $testTypes[] = [
                            "Sample_ID" => $requisition["Sample_ID"],
                            "Sample_Number" => $requisition["Sample_Number"],
                            "Sample_Date" => $requisition["Sample_Date"],
                            "Test_Type" => $requisition[$testTypeKey],
                          ];
                        }
                      }
                    }
                  }
                  
                  usort($testTypes, function ($a, $b) {
                    return strcmp($a["Test_Type"], $b["Test_Type"]);
                  });
                  
                  foreach ($testTypes as $index => $sample) :
                    if ($sample['Test_Type'] === 'SP') :
                      $GrainResults = find_by_sql("SELECT * FROM grain_size_general WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                      foreach ($GrainResults as $Grain) :
                        if ($Grain) :
                          $T3p4 = (float)$Grain['CumRet11'];
                          $T3p8 = (float)$Grain['CumRet13'];
                          $TNo4 = (float)$Grain['CumRet14'];
                          $resultado = '';
                          $CorrecionPorTamano = '';

                          if ($T3p4 > 0) {
                            $resultado = "C";
                          } elseif ($T3p8 > 0 && $T3p4 == 0) {
                            $resultado = "B";
                          } elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0) {
                            $resultado = "A";
                          } else {
                            $resultado = "No se puede determinar el método";
                          }

                          if ($T3p4 > 5) {
                            $CorrecionPorTamano = "Correcion Por Sobre Tamaño, realizar SG Particulas Finas y Gruesas";
                          }
            ?>
            <tr>
              <td><?php echo $sample['Sample_ID'] . "-" . $sample['Sample_Number'] . "-" . $sample['Test_Type']; ?></td>
              <td><?php echo $resultado; ?></td>
              <td><?php echo $CorrecionPorTamano; ?></td>
            </tr>
            <?php endif; endforeach; endif; endforeach; ?>
          </tbody>
        </table>


      </div>

    </div>
  </div><!-- End Method Proctor -->

  <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <h5 class="card-title">Muestras Registradas</h5>
            <?php $ReqViews = find_all('lab_test_requisition_form') ?>
            <!-- Table with stripped rows -->
            <table class="table datatable">
               <thead>
                  <tr>
                     <th scope="col">#</th>
                     <th scope="col">Muestra</th>
                     <th scope="col">Numero de muestra</th>
                     <th scope="col">Acciones</th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($ReqViews as $ReqViews):?>
                  <tr>
                     <th scope="row"><?php echo count_id();?></th>
                     <td><?php echo $ReqViews['Sample_ID']; ?></td>
                     <td><?php echo $ReqViews['Sample_Number']; ?></td>
                     <td>
                      <div class="btn-group" role="group">
                        <a href="requisition-form-edit.php?id=<?php echo $ReqViews['id']; ?>" class="btn btn-warning"><i class="bi bi-pen"></i></a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
               </tbody>
            </table>
            <!-- End Table with stripped rows -->
         </div>
      </div>
   </div>

    </div>
  </div><!-- End Left side columns -->

  <!-- Right side columns -->
  <div class="col-lg-4">

  <div class="card">
  <div class="card-body">
    <h5 class="card-title">Cantidades en Proceso</h5>
    
    <?php 
    
      function isSampleInPreviousTables($sampleName, $sampleNumber, $testType, $currentTableName, $previousTables) {
        foreach ($previousTables as $table) {
          if ($table === $currentTableName) {
           
            break;
          }

          $count = find_by_sql("SELECT COUNT(*) as count FROM $table WHERE Sample_Name = '$sampleName' AND Sample_Number = '$sampleNumber' AND Test_Type = '$testType'")[0]['count'];
          if ($count > 0) {
            return true; 
          }
        }
        return false; 
      }

      $tablas = [
        'test_preparation' => 'Preparación',
        'test_realization' => 'Realización',
        'test_delivery' => 'Entrega',
        'test_review' => 'Revision',
        'test_repeat' => 'Repeticion',
      ];
    
      $numRowsArray = []; 


      $reversedTables = array_reverse($tablas);

      foreach ($reversedTables as $table => $personalizedName) {

        $previousTables = array_keys(array_slice($tablas, array_search($table, array_keys($tablas)) + 1));

        $samples = find_by_sql("SELECT * FROM $table"); 

        $uniqueSamples = [];

        foreach ($samples as $sample) {
          $sampleName = $sample['Sample_Name'];
          $sampleNumber = $sample['Sample_Number'];
          $testType = $sample['Test_Type'];

          if (!isSampleInPreviousTables($sampleName, $sampleNumber, $testType, $table, $previousTables)) {

            $uniqueSamples["$sampleName|$sampleNumber|$testType"] = $sample;
          }
        }
        
        $numRows = count($uniqueSamples);

        $numRowsArray[$table] = ['name' => $personalizedName, 'count' => $numRows];
      }
    ?>
    
    <!-- List group With badges -->
    <ul class="list-group">
    <?php foreach ($tablas as $table => $personalizedName):?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?php echo $personalizedName; ?>
        <span class="badge bg-primary rounded-pill"><?php echo $numRowsArray[$table]['count']; ?></span>
      </li>
    <?php endforeach; ?>
    </ul><!-- End List With badges -->

  </div>
</div>

    <!-- CANTIDAD DE ENSAYOS PENDIENTES -->
    <div class="col-12">
    <div class="card">
    <div class="card-body">
        <h5 class="card-title">Cantidad de Ensayos Pendientes</h5>
        <ul class="list-group">
        <?php
         $columnas_tipo_prueba = array();
         for ($i = 1; $i <= 19; $i++) {
          $columnas_tipo_prueba[] = "Test_Type" . $i;
         }
        
         $columnas_por_tipo = array();
         foreach ($Requisition as $requisition) {
           foreach ($columnas_tipo_prueba as $columna) {
             $testTypeValue = $requisition[$columna];
             if ($testTypeValue !== null && $testTypeValue !== "") {
               $columnas_por_tipo[$testTypeValue] = $columna;
             }
           }
         }

         $typeCount = [];
         foreach ($testTypes as $sample) {
            $testType = $sample['Test_Type'];
            if (isset($typeCount[$testType])) {
                $typeCount[$testType]++;
            } else {
                $typeCount[$testType] = 1;
            }
         }
         foreach ($typeCount as $testType => $count) :
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <h5><code><?php echo $testType; ?></code></h5>
            <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
            <?php if (isset($columnas_por_tipo[$testType])) : ?>
            <?php else : ?>
            <span class="badge bg-danger rounded-pill">Opps</span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
        </ul>
     </div>
    </div>

   </div>
  </div><!-- End CANTIDAD DE ENSAYOS PENDIENTES -->

  </div><!-- End Right side columns -->

  </div>
</section>

</main><!-- End #main -->
<?php include_once('../components/footer.php'); ?>