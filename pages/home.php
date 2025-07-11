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
                    $week = date('Y-m-d', strtotime('-14 days'));
                    //$Requisitions = find_all('lab_test_requisition_form');
                    $Requisitions = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM lab_test_requisition_form WHERE Registed_Date >= '{$week}' ORDER BY Registed_Date DESC");
                    $testTypes = [];
                    $statusCounts = [
                      'Preparation' => 0,
                      'Realization' => 0,
                      'Delivery' => 0,
                      'Review' => 0,
                      'Repeat' => 0,
                    ];

                    // Cargar los datos de todas las tablas una sola vez
                    $Reviewed = "(SELECT 1 FROM test_reviewed WHERE Tracking = p.Tracking)";
                    $preparationData = find_all('test_preparation');
                    $realizationData = find_all('test_realization');
                    $deliveryData = find_all('test_delivery');
                    $reviewData = find_by_sql("SELECT * FROM test_review p  WHERE NOT EXISTS $Reviewed");
                    $repeatData = find_all('test_repeat');

                    // Crear un array para cada tabla con las muestras y su estado
                    $testDataByTable = [
                      'Preparation' => $preparationData,
                      'Realization' => $realizationData,
                      'Delivery' => $deliveryData,
                      'Review' => $reviewData,
                      'Repeat' => $repeatData,
                    ];

                    foreach ($Requisitions as $requisition) {
                      if (!empty($requisition['Test_Type'])) {
                        // Separar los tipos de prueba por comas
                        $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));

                        foreach ($testTypesArray as $type) {
                          $type = normalize($type); // Normaliza si es necesario
                          $testTypes[$type][] = [
                            'Sample_ID' => $requisition['Sample_ID'],
                            'Sample_Number' => $requisition['Sample_Number'],
                            'Test_Type' => $type,
                          ];
                        }
                      }
                    }


                    foreach ($testTypes as $testType => $data) {
                      foreach ($data as $item) {
                        $status = getStatus($item['Sample_ID'], $item['Sample_Number'], $item['Test_Type'], $testDataByTable);
                        if ($status !== 'NoStatusFound') {
                          $statusCounts[$status]++; // Incrementa el contador del estado correspondiente

                          echo '<tr>';
                          echo '<td>' . $item['Sample_ID'] . '</td>';
                          echo '<td>' . $item['Sample_Number'] . '</td>';
                          echo '<td>' . $item['Test_Type'] . '</td>';
                          echo '<td><span class="badge bg-' . getBadgeClass($status) . '">' . translateStatus($status) . '</span></td>';
                          echo '</tr>';
                        }
                      }
                    }

                    function getStatus($sampleID, $sampleNumber, $testType, $testDataByTable)
                    {
                      // Verificar los estados en el orden correcto
                      foreach (['Repeat', 'Review', 'Delivery', 'Realization', 'Preparation'] as $statusType) {
                        $status = getStatusFromTable($sampleID, $sampleNumber, $testType, $testDataByTable[$statusType]);
                        if ($status !== 'NoStatusFound') {
                          return $status;
                        }
                      }
                      return 'NoStatusFound';
                    }

                    function getStatusFromTable($sampleID, $sampleNumber, $testType, $tableData)
                    {
                      foreach ($tableData as $row) {
                        if ($row['Sample_ID'] == $sampleID && $row['Sample_Number'] == $sampleNumber && $row['Test_Type'] == $testType) {
                          return $row['Status'];
                        }
                      }
                      return 'NoStatusFound';
                    }

                    function getBadgeClass($status)
                    {
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

                    function translateStatus($status)
                    {
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
                <?php $week = date('Y-m-d', strtotime('-7 days')); ?>
                <?php $Seach = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type, Start_Date, Send_By FROM test_repeat WHERE Start_Date >= '{$week}'"); ?>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">Muestra</th>
                      <th scope="col">Numero de muestra</th>
                      <th scope="col">Tipo de prueba</th>
                      <th scope="col">Fecha</th>
                      <th scope="col">Enviado Por</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($Seach as $Seach): ?>
                      <tr>
                        <td><?php echo $Seach['Sample_ID']; ?></td>
                        <td><?php echo $Seach['Sample_Number']; ?></td>
                        <td><?php echo $Seach['Test_Type']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($Seach['Start_Date'])); ?></td>
                        <td><?php echo $Seach['Send_By']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>


              </div>

            </div>
          </div>
          <!-- End Method Proctor -->

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
                    $week = date('Y-m-d', strtotime('-31 days'));
                    $Requisition = find_by_sql("SELECT * FROM lab_test_requisition_form WHERE Registed_Date >= '{$week}' ORDER BY Registed_Date DESC");
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

                            return $preparation["Sample_ID"] === $requisition["Sample_ID"] &&
                              $preparation["Sample_Number"] === $requisition["Sample_Number"] &&
                              $preparation["Test_Type"] === $requisition[$testTypeKey];
                          });

                          $matchingReviews = array_filter($Review, function ($review) use ($requisition, $testTypeKey) {
                            return $review["Sample_ID"] === $requisition["Sample_ID"] &&
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
                        // Buscar en la tabla grain_size_general
                        $GrainResults = find_by_sql("SELECT * FROM grain_size_general WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");

                        // Buscar en otras tablas adicionales
                        $GrainResultsCoarse = find_by_sql("SELECT * FROM grain_size_coarse WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                        $GrainResultsCoarseThan = find_by_sql("SELECT * FROM grain_size_coarsethan WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                        $GrainResultsCoarseFilter = find_by_sql("SELECT * FROM grain_size_coarse_filter WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                        $GrainResultsFine = find_by_sql("SELECT * FROM grain_size_fine WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                        $GrainResultsLPF = find_by_sql("SELECT * FROM grain_size_lpf WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");
                        $GrainResultsUpstream = find_by_sql("SELECT * FROM grain_size_upstream_transition_fill WHERE Sample_ID = '{$sample['Sample_ID']}' AND Sample_Number = '{$sample['Sample_Number']}' LIMIT 1");

                        // Combinamos los resultados de las tablas
                        foreach ([$GrainResults, $GrainResultsCoarse, $GrainResultsCoarseThan, $GrainResultsCoarseFilter, $GrainResultsFine, $GrainResultsLPF, $GrainResultsUpstream] as $GrainResults) :
                          foreach ($GrainResults as $Grain) :
                            if ($Grain) :
                              // Asignar valores de las columnas de las tablas dependiendo de la tabla
                              if ($GrainResults === $GrainResultsCoarse || $GrainResults === $GrainResultsCoarseThan) {
                                $T3p4 = (float)$Grain['CumRet9'] ?? 0;
                                $T3p8 = (float)$Grain['CumRet10'] ?? 0;
                                $TNo4 = (float)$Grain['CumRet11'] ?? 0;
                              } elseif ($GrainResults === $GrainResultsFine) {
                                $T3p4 = (float)$Grain['CumRet9'] ?? 0;
                                $T3p8 = (float)$Grain['CumRet11'] ?? 0;
                                $TNo4 = (float)$Grain['CumRet12'] ?? 0;
                              } elseif ($GrainResults === $GrainResultsCoarseFilter || $GrainResults === $GrainResultsLPF) {
                                $T3p4 = (float)$Grain['CumRet5'] ?? 0;
                                $T3p8 = (float)$Grain['CumRet6'] ?? 0;
                                $TNo4 = (float)$Grain['CumRet7'] ?? 0;
                              } elseif ($GrainResults === $GrainResultsUpstream) {
                                $T3p4 = (float)$Grain['CumRet8'] ?? 0;
                                $T3p8 = (float)$Grain['CumRet10'] ?? 0;
                                $TNo4 = (float)$Grain['CumRet11'] ?? 0;
                              } else {
                                // Default para grain_size_general
                                $T3p4 = (float)$Grain['CumRet11'] ?? 0;
                                $T3p8 = (float)$Grain['CumRet13'] ?? 0;
                                $TNo4 = (float)$Grain['CumRet14'] ?? 0;
                              }

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
                    <?php endif;
                          endforeach;
                        endforeach;
                      endif;
                    endforeach; ?>

                  </tbody>
                </table>


              </div>

            </div>
          </div><!-- End Method Proctor -->

          <!-- Muestras Registradas -->
          <?php /*
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Muestras Registradas</h5>
                <!-- Table with stripped rows -->
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th scope="col">Muestra</th>
                      <th scope="col">Numero de muestra</th>
                      <th scope="col">Solicitados</th>
                      <th scope="col">Entregados</th>
                      <th scope="col">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($Requisition as $ReqViews): ?>
                      <?php
                      $count_solicitados = 0;
                      $count_entregados = 0;

                      // Consulta para obtener todas las entregas de la muestra actual
                      $query = "SELECT Test_Type FROM test_delivery WHERE Sample_Name = '{$ReqViews['Sample_ID']}' AND Sample_Number = '{$ReqViews['Sample_Number']}'";
                      $result = $db->query($query);
                      $entregados = [];
                      while ($row = $result->fetch_assoc()) {
                        $entregados[] = $row['Test_Type'];
                      }

                      // Contar los ensayos solicitados y entregados
                      if (!empty($ReqViews['Test_Type'])) {
                        $testTypesArray = array_map('trim', explode(',', $ReqViews['Test_Type']));

                        foreach ($testTypesArray as $type) {
                          $count_solicitados++;
                          if (in_array($type, $entregados)) {
                            $count_entregados++;
                          }
                        }
                      }


                      // Calcular el porcentaje de ensayos entregados
                      $porce_entregados = ($count_solicitados > 0) ? round(($count_entregados / $count_solicitados) * 100) : 0;
                      ?>
                      <tr>
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
                              <h5 class="modal-title">Detalle del ensayo</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="container">
                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title">Muestra</h5>
                                    <h5><?php echo $ReqViews['Sample_ID'] . "-" . $ReqViews['Sample_Number']; ?></h5>
                                  </div>
                                </div>

                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title">Ensayos solicitados</h5>
                                    <ul class="list-group">
                                      <?php for ($i = 1; $i <= 20; $i++) {
                                        $testTypeValue = $ReqViews['Test_Type' . $i];
                                        if (!empty($testTypeValue)) { ?>
                                          <li class="list-group-item"><?php echo $testTypeValue; ?></li>
                                      <?php }
                                      } ?>
                                    </ul>
                                  </div>
                                </div>

                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title">Comentario</h5>
                                    <ul class="list-group">
                                      <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <h5><code><?php echo $ReqViews['Comment']; ?></code></h5>
                                      </li>
                                    </ul>
                                  </div>
                                </div>

                                <div class="card">
                                  <div class="card-body">
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
                                    </ul>
                                  </div>
                                </div>
                              </div>

                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div><!-- End Modal -->
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <!-- End Table with stripped rows -->
              </div>
            </div>
          </div>
          */ ?>
          <!-- End Muestras Registradas -->

        </div>
      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <div class="col-lg-4">

        <!-- Cantidades en Proceso -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Cantidades en Proceso</h5>

            <ul class="list-group">
              <?php foreach ($statusCounts as $status => $count): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?php echo translateStatus($status); ?>
                  <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                </li>
              <?php endforeach; ?>
            </ul>

          </div>
        </div>
        <!-- End Cantidades en Proceso -->

        <!-- CANTIDAD DE ENSAYOS PENDIENTES -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Cantidad de Ensayos Pendientes</h5>
              <ul class="list-group">
                <?php
                function normalize($v)
                {
                  return strtoupper(trim($v));
                }

                $tables_to_check = [
                  'test_preparation',
                  'test_delivery',
                  'test_realization',
                  'test_repeat',
                  'test_review',
                  'test_reviewed'
                ];

                $indexed_status = [];

                foreach ($tables_to_check as $table) {
                  $rows = find_all($table);
                  foreach ($rows as $row) {
                    $key = normalize($row['Sample_ID']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
                    $indexed_status[$key] = true;
                  }
                }

                $typeCount = [];
                $seen = [];

                foreach ($Requisitions as $requisition) {
                  if (empty($requisition['Test_Type'])) continue;

                  $sampleID = normalize($requisition['Sample_ID']);
                  $sampleNumber = normalize($requisition['Sample_Number']);

                  $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));

                  foreach ($testTypesArray as $testTypeRaw) {
                    $testType = normalize($testTypeRaw);
                    $uniqueKey = $sampleID . "|" . $sampleNumber . "|" . $testType;

                    if (isset($seen[$uniqueKey])) continue;
                    $seen[$uniqueKey] = true;

                    if (!isset($indexed_status[$uniqueKey])) {
                      $typeCount[$testType] = ($typeCount[$testType] ?? 0) + 1;
                    }
                  }
                }


                if (empty($typeCount)) {
                  echo '<li class="list-group-item">✅ No hay ensayos pendientes</li>';
                } else {
                  foreach ($typeCount as $testType => $count) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    echo '<h5><code>' . htmlspecialchars($testType) . '</code></h5>';
                    echo '<span class="badge bg-primary rounded-pill">' . $count . '</span>';
                    echo '</li>';
                  }
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
        <!-- End CANTIDAD DE ENSAYOS PENDIENTES -->

      </div>
      <!-- End Right side columns -->

</div>

              </ul>
            </div>
          </div>

        </div>
      </div><!-- End CANTIDAD DE ENSAYOS PENDIENTES -->
    
      </div><!-- End Right side columns -->

    </div>
  </section>
<?php
function obtenerMuestrasABotar($conexion, $tablas, $fechaLimite) {
    $muestras = [];
    foreach ($tablas as $tabla) {
        $columns = "id, Sample_ID, Sample_Number, test_type";

        $checkColumnQuery = "SHOW COLUMNS FROM $tabla LIKE 'Tare_Name'";
        $checkColumnResult = mysqli_query($conexion, $checkColumnQuery);
        if (mysqli_num_rows($checkColumnResult) > 0) {
            $columns .= ", Tare_Name";
        }

        $checkMaterialTypeQuery = "SHOW COLUMNS FROM $tabla LIKE 'Material_Type'";
        $checkMaterialTypeResult = mysqli_query($conexion, $checkMaterialTypeQuery);
        if (mysqli_num_rows($checkMaterialTypeResult) > 0) {
            $columns .= ", Material_Type";
        }

        $query = "SELECT $columns FROM $tabla WHERE Test_Start_Date >= '$fechaLimite'";
        $result = mysqli_query($conexion, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $muestras[] = $row;
            }
        }
    }

    $queryEnsayos = "SELECT Sample_ID, Sample_Number, test_type FROM ensayos_en_revision WHERE Test_Start_Date >= '$fechaLimite'";
    $resultEnsayos = mysqli_query($conexion, $queryEnsayos);
    if ($resultEnsayos) {
        while ($row = mysqli_fetch_assoc($resultEnsayos)) {
            $muestras[] = $row;
        }
    }

    return $muestras;
}
?>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-trash"></i> Listado de muestras a Botar</strong>
        <a href="../pdf/exportar_muestra_botar.php" target="_blank" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
        </a>
      </div>
      <div class="card-body">
        <!-- Barra de búsqueda -->
        <input type="text" id="buscarMuestras" class="form-control mb-3" placeholder="Buscar muestra...">

        <?php
        $fechaLimite = date('Y-m-d H:i:s', strtotime('-7 days'));
        $muestras = [];

        $result = $db->query("SHOW TABLES");
        $tablas = [];
        while ($row = $result->fetch_row()) {
          $tablas[] = $row[0];
        }

        foreach ($tablas as $tabla) {
          $columns = "id, Sample_ID, Sample_Number, test_type";

          // Posibles nombres de columna para 'bandeja'
          $posiblesBandejas = [
            'Tare_Name', 'Container', 'Container1', 'Container2','LL_Container_1', 'LL_Container_2', 'LL_Container_3', 'PL_Container_1', 'PL_Container_2', 'PL_Container_3',
            'Container3', 'Container4', 'Container5', 'Container6',
            'TareMc', 'Tare_Name_MC_Before', 'Tare_Name_MC_After'
          ];

          $colBandeja = null;
          foreach ($posiblesBandejas as $col) {
            $check = $db->query("SHOW COLUMNS FROM `$tabla` LIKE '$col'");
            if ($check && $check->num_rows > 0) {
              $colBandeja = $col;
              break;
            }
          }

          if ($colBandeja) {
            $columns .= ", `$colBandeja` AS Bandeja";
          }

          // Detectar columnas adicionales
          $hasMat = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Material_Type'");
          if ($hasMat && $hasMat->num_rows > 0) $columns .= ", Material_Type";

          $hasDate = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'Test_Start_Date'");
          $hasDiscarded = $db->query("SHOW COLUMNS FROM `$tabla` LIKE 'discarded'");

          if ($hasDate && $hasDate->num_rows > 0) {
            $query = "SELECT $columns FROM `$tabla` WHERE Test_Start_Date >= '$fechaLimite'";
            if ($hasDiscarded && $hasDiscarded->num_rows > 0) {
              $query .= " AND (discarded IS NULL OR discarded = 0)";
            }
            $res = $db->query($query);
            while ($row = $res->fetch_assoc()) {
              $row['tabla'] = $tabla;
              $muestras[] = $row;
            }
          }
        }
        ?>

        <div class="table-responsive">
          <table class="table table-striped table-hover" id="tablaMuestras">
            <thead class="table-light">
              <tr>
                <th>Nombre de Muestra</th>
                <th>Número</th>
                <th>Tipo de Material</th>
                <th>Tipo de Ensayo</th>
                <th>Número de Bandeja</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($muestras)): ?>
                <tr>
                  <td colspan="5" class="text-center text-success">✅ No hay muestras a botar</td>
                </tr>
              <?php else: ?>
                <?php foreach ($muestras as $muestra): ?>
                  <tr>
                    <td><?= htmlspecialchars($muestra['Sample_ID'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($muestra['Sample_Number'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($muestra['Material_Type'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($muestra['test_type'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($muestra['Bandeja'] ?? 'N/A') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

 </section>
</main><!-- End #main -->
</script>



<!-- Búsqueda instantánea -->
<script>
document.getElementById('buscarMuestras').addEventListener('keyup', function () {
  const filtro = this.value.toLowerCase();
  const filas = document.querySelectorAll('#tablaMuestras tbody tr');
  filas.forEach(fila => {
    const textoFila = fila.textContent.toLowerCase();
    fila.style.display = textoFila.includes(filtro) ? '' : 'none';
  });
});

</main><!-- End #main -->
<?php include_once('../components/footer.php'); ?>