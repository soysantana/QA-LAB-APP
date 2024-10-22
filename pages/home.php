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
  <h1>Panel Control</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item active">Panel Control</li>
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
$statusCounts = [
    'Preparation' => 0,
    'Realization' => 0,
    'Delivery' => 0,
    'Review' => 0,
    'Repeat' => 0,
];

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
            $statusCounts[$status]++; // Incrementa el contador del estado correspondiente
            
            echo '<tr>';
            echo '<td>' . $item['Sample_ID'] . '</td>';
            echo '<td>' . $item['Sample_Number'] . '</td>';
            echo '<td>' . $item['Test_Type'] . '</td>';
            echo '<td><span class="badge bg-' . getBadgeClass($status) . '">' . translateStatus($status) . '</span></td>';
            echo '</tr>';
        }
        // No se cuenta "NoStatusFound" ni se muestra
    }
}

function getStatus($sampleID, $sampleNumber, $testType) {
    $statusFromPreparation = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_preparation');
    $statusFromRealization = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_realization');
    $statusFromDelivery = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_delivery');
    $statusFromReview = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_review');
    $statusFromRepeat = getStatusFromTestTable($sampleID, $sampleNumber, $testType, 'test_repeat');

    // Verificar en orden para devolver el primer estado encontrado
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
        return 'NoStatusFound'; // Esto ya no se cuenta
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
    return 'NoStatusFound'; // Esto ya no se cuenta
}

function getBadgeClass($status) {
    switch ($status) {
        case 'Preparation':
            return 'primary';
        case 'Realization':
            return 'secondary';
        case 'Delivery':
            return 'success';
        case 'Review':
            return 'dark';
        case 'Repeat':
            return 'warning';
        default:
            return 'danger'; // Para NoStatusFound, pero no se mostrará
    }
}

function translateStatus($status) {
    switch ($status) {
        case 'Preparation':
            return 'Preparación';
        case 'Realization':
            return 'Realización';
        case 'Delivery':
            return 'Entrega';
        case 'Review':
            return 'Revisión';
        case 'Repeat':
            return 'Repetición';
        default:
            return $status; // Para NoStatusFound, pero no se mostrará
    }
}
?>


          </tbody>
        </table>

      </div>

    </div>
  </div><!-- End Process Database Requision -->

        <!-- ENSAYOS EN REPETICION -->
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
        <h5 class="card-title">Ensayos en Repeticion <span>| Hoy</span></h5>
        <?php $week = date('Y-m-d', strtotime('-7 days'));?>
        <?php $Seach = find_by_sql("SELECT * FROM test_repeat WHERE Start_Date >= '{$week}'");?>
        <table class="table table-borderless datatable">
          <thead>
            <tr>
              <th scope="col">Muestra</th>
              <th scope="col">Numero de muestra</th>
              <th scope="col">Tipo de prueba</th>
              <th scope="col">Fecha</th>
              <th scope="col">Tecnico</th>
              <th scope="col">Enviado Por</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($Seach as $Seach):?>
              <tr>
                <td><?php echo $Seach['Sample_Name']; ?></td>
                <td><?php echo $Seach['Sample_Number']; ?></td>
                <td><?php echo $Seach['Test_Type']; ?></td>
                <td><?php echo date('Y-m-d', strtotime($Seach['Start_Date'])); ?></td>
                <td></td>
                <td><?php echo $Seach['Register_By']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>


      </div>

    </div>
  </div><!-- End Method Proctor -->

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
                     <th scope="col">Solicitados</th>
                     <th scope="col">Entregados</th>

                     <th scope="col">Acciones</th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($ReqViews as $ReqViews):?>

                <?php
               $count_solicitados = 0;
               $count_entregados = 0;
               
               for ($i = 1; $i <= 20; $i++) {
                $column_name = 'Test_Type' . $i;
                if (isset($ReqViews[$column_name]) && !empty($ReqViews[$column_name])) {
                  $count_solicitados++;
                  // Verificar si el ensayo ha sido entregado
                  $query = "SELECT COUNT(*) AS entregado FROM test_delivery WHERE Sample_Name = '{$ReqViews['Sample_ID']}' AND Sample_Number = '{$ReqViews['Sample_Number']}' AND Test_Type = '{$ReqViews[$column_name]}'";
                  $result = $db->query($query);
                  $row = $result->fetch_assoc();
                  if ($row['entregado'] > 0) {
                    $count_entregados++;
                  }
                }
              }
                 // Calcular el porcentaje de ensayos entregados
                $porce_entregados = round(($count_entregados / $count_solicitados) * 100);
              ?>
                  <tr>
                     <th scope="row"><?php echo count_id();?></th>
                     <td><?php echo $ReqViews['Sample_ID']; ?></td>
                     <td><?php echo $ReqViews['Sample_Number']; ?></td>
                     <td><span class="badge bg-primary rounded-pill me-2"><?php echo $count_solicitados; ?></span></td>
                     <td><span class="badge bg-success rounded-pill me-2"><?php echo $count_entregados; ?></span></td>
                     <td>
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#requisitionview<?php echo $ReqViews['id']; ?>"><i class="bi bi-eye"></i></button>
                        <a href="requisition-form-edit.php?id=<?php echo $ReqViews['id']; ?>" class="btn btn-warning"><i class="bi bi-pen"></i></a>
                      </div>
                    </td>
                  </tr>

                  <div class="modal" id="requisitionview<?php echo $ReqViews['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Detalle del ensayo </h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        <div class="container">
                      
                      <div class="card">
                        <div class="card-body">
                        <!-- Requested Essays -->
                        <h5 class="card-title">Muestra</h5> 
                        <h5><?php echo $ReqViews['Sample_ID'] . "-" . $ReqViews['Sample_Number']; ?></h5>

                        </div>
                      </div>
                         
                      <div class="card">
                        <div class="card-body">
                        <!-- Requested Essays -->
                        <h5 class="card-title">Ensayos solicitados</h5>
                        <ul class="list-group">
                          <?php for ($i = 1; $i <= 20; $i++) { $testTypeValue = $ReqViews['Test_Type' . $i]; if ($testTypeValue !== null && $testTypeValue !== '') { ?>
                          <li class="list-group-item"><?php echo $testTypeValue; ?></li>
                          <?php } } ?>
                        </ul>
                        <!-- End Requested Essays -->
                        </div>
                      </div>

                      <div class="card">
                        <div class="card-body">
                        <!-- Requested Essays -->
                        <h5 class="card-title">Comentario</h5>
                        <ul class="list-group">
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <h5><code><?php echo $ReqViews['Comment']; ?></code></h5>
                          </li>
                        </ul>
                        <!-- End Requested Essays -->
                        </div>
                      </div>
                        
                      <div class="card">
                        <div class="card-body">
                        <!-- Requested Essays -->
                        <h5 class="card-title">Otros datos</h5>
                        <ul class="list-group">
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <h5><code>Fecha de la muestra</code></h5>
                            <span class="badge bg-primary rounded-pill"><?php echo $ReqViews['Sample_Date']; ?></span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <h5><code>Fecha de Registro</code></h5>
                            <span class="badge bg-primary rounded-pill"><?php echo $ReqViews['Registed_Date']; ?></span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <h5><code>Muestra por</code></h5>
                            <span class="badge bg-primary rounded-pill"><?php echo $ReqViews['Sample_By']; ?></span>
                          </li>
                        </ul><!-- End Requested Essays -->

                        </div>
                      </div>


                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- End Modal-->

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
    <!-- List group With badges -->
    <ul class="list-group">
    <?php foreach ($statusCounts as $status => $count): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?php echo translateStatus($status); ?>
            <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
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
         for ($i = 1; $i <= 20; $i++) {
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