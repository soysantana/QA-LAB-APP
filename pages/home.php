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

  <?php
  // Helpers comunes
  function normalize_str($v) { return strtoupper(trim((string)$v)); }

  function getBadgeClass($status) {
    switch ($status) {
      case 'Preparation': return 'primary';
      case 'Realization': return 'secondary';
      case 'Delivery':    return 'success';
      case 'Review':      return 'dark';
      case 'Repeat':      return 'warning';
      default:            return 'danger';
    }
  }

  function translateStatus($status) {
    switch ($status) {
      case 'Preparation': return 'Preparación';
      case 'Realization': return 'Realización';
      case 'Delivery':    return 'Entrega';
      case 'Review':      return 'Revisión';
      case 'Repeat':      return 'Repetición';
      default:            return $status;
    }
  }

  // Devuelve el nombre del estado si se encuentra en la tabla correspondiente
  function getStatusFromTable($sampleID, $sampleNumber, $testType, $tableData) {
    foreach ($tableData as $row) {
      if (
        ($row['Sample_ID']     ?? null) == $sampleID &&
        ($row['Sample_Number'] ?? null) == $sampleNumber &&
        ($row['Test_Type']     ?? null) == $testType
      ) {
        return true;
      }
    }
    return false;
  }

  // Recorre tablas por prioridad y retorna el nombre del estado encontrado
  function getStatus($sampleID, $sampleNumber, $testType, $testDataByTable) {
    foreach (['Repeat', 'Review', 'Delivery', 'Realization', 'Preparation'] as $statusType) {
      if (getStatusFromTable($sampleID, $sampleNumber, $testType, $testDataByTable[$statusType])) {
        return $statusType;
      }
    }
    return 'NoStatusFound';
  }
  ?>

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Proceso de muestreo -->
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
                    $Requisitions = find_by_sql("
                      SELECT Sample_ID, Sample_Number, Test_Type
                      FROM lab_test_requisition_form
                      WHERE Registed_Date >= '{$week}'
                      ORDER BY Registed_Date DESC
                    ");

                    $statusCounts = [
                      'Preparation' => 0,
                      'Realization' => 0,
                      'Delivery'    => 0,
                      'Review'      => 0,
                      'Repeat'      => 0,
                    ];

                    // Cargar datos de seguimiento una vez
                    $preparationData = find_all('test_preparation');
                    $realizationData = find_all('test_realization');
                    $deliveryData    = find_all('test_delivery');

                    // test_review: quitar los que ya tienen registro correspondiente en test_reviewed con la misma llave
                    $reviewData = find_by_sql("
                      SELECT p.*
                      FROM test_review p
                      WHERE NOT EXISTS (
                        SELECT 1
                        FROM test_reviewed r
                        WHERE r.Sample_ID = p.Sample_ID
                          AND r.Sample_Number = p.Sample_Number
                          AND r.Test_Type = p.Test_Type
                      )
                    ");

                    $repeatData = find_all('test_repeat');

                    $testDataByTable = [
                      'Preparation' => $preparationData,
                      'Realization' => $realizationData,
                      'Delivery'    => $deliveryData,
                      'Review'      => $reviewData,
                      'Repeat'      => $repeatData,
                    ];

                    // Explota Test_Type y evalúa estado por muestra/tipo
                    foreach ($Requisitions as $requisition) {
                      if (!empty($requisition['Test_Type'])) {
                        $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));
                        foreach ($testTypesArray as $type) {
                          if ($type === '') continue;
                          $status = getStatus(
                            $requisition['Sample_ID'],
                            $requisition['Sample_Number'],
                            $type,
                            $testDataByTable
                          );
                          if ($status !== 'NoStatusFound') {
                            $statusCounts[$status]++;
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($requisition['Sample_ID']) . '</td>';
                            echo '<td>' . htmlspecialchars($requisition['Sample_Number']) . '</td>';
                            echo '<td>' . htmlspecialchars($type) . '</td>';
                            echo '<td><span class="badge bg-' . getBadgeClass($status) . '">' . translateStatus($status) . '</span></td>';
                            echo '</tr>';
                          }
                        }
                      }
                    }
                    ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Proceso de muestreo -->

          <!-- Ensayos en Repetición -->
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
                <?php $week7 = date('Y-m-d', strtotime('-7 days')); ?>
                <?php $repeatRows = find_by_sql("
                  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date, Send_By
                  FROM test_repeat
                  WHERE Start_Date >= '{$week7}'
                  ORDER BY Start_Date DESC
                "); ?>
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
                    <?php foreach ($repeatRows as $row): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['Sample_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Sample_Number']) ?></td>
                        <td><?= htmlspecialchars($row['Test_Type']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['Start_Date']))) ?></td>
                        <td><?= htmlspecialchars($row['Send_By']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
          <!-- End Ensayos en Repetición -->

          <!-- Método Proctor -->
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
                    $week31 = date('Y-m-d', strtotime('-31 days'));
                    $RequisitionSP = find_by_sql("
                      SELECT Sample_ID, Sample_Number, Sample_Date, Test_Type
                      FROM lab_test_requisition_form
                      WHERE Registed_Date >= '{$week31}'
                      ORDER BY Registed_Date DESC
                    ");
                    $PreparationSP = find_all("test_preparation");
                    $ReviewSP      = find_all("test_review");

                    $testTypesSP = [];
                    foreach ($RequisitionSP as $req) {
                      if (empty($req['Test_Type'])) continue;

                      $sid  = $req['Sample_ID'];
                      $snum = $req['Sample_Number'];
                      $sdate= $req['Sample_Date'];

                      $tokens = array_map('trim', explode(',', $req['Test_Type']));
                      $tokens = array_filter($tokens, fn($t)=>$t!=='');

                      foreach ($tokens as $tt) {
                        // solo tomaremos pendientes (no en Preparation ni Review)
                        $inPrep = false;
                        foreach ($PreparationSP as $p) {
                          if (($p['Sample_ID']??null)===$sid && ($p['Sample_Number']??null)===$snum && ($p['Test_Type']??null)===$tt) { $inPrep=true; break; }
                        }
                        $inRev = false;
                        foreach ($ReviewSP as $r) {
                          if (($r['Sample_ID']??null)===$sid && ($r['Sample_Number']??null)===$snum && ($r['Test_Type']??null)===$tt) { $inRev=true; break; }
                        }
                        if (!$inPrep && !$inRev) {
                          $testTypesSP[] = [
                            "Sample_ID"     => $sid,
                            "Sample_Number" => $snum,
                            "Sample_Date"   => $sdate,
                            "Test_Type"     => normalize_str($tt),
                          ];
                        }
                      }
                    }

                    usort($testTypesSP, function($a,$b){
                      return strcmp($a['Test_Type'], $b['Test_Type']);
                    });

                    foreach ($testTypesSP as $sample) :
                      if ($sample['Test_Type'] !== 'SP') continue;

                      $sid   = $sample['Sample_ID'];
                      $snum  = $sample['Sample_Number'];
                      $sidEsc  = $db->escape($sid);
                      $snumEsc = $db->escape($snum);

                      $datasets = [
                        'general'  => find_by_sql("SELECT CumRet11, CumRet13, CumRet14 FROM grain_size_general WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1"),
                        'coarse'   => find_by_sql("SELECT CumRet9,  CumRet10, CumRet11 FROM grain_size_coarse  WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1"),
                        'fine'     => find_by_sql("SELECT CumRet9,  CumRet11, CumRet12 FROM grain_size_fine    WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1"),
                        'lpf'      => find_by_sql("SELECT CumRet5,  CumRet6,  CumRet7  FROM grain_size_lpf     WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1"),
                        'upstream' => find_by_sql("SELECT CumRet8,  CumRet10, CumRet11 FROM grain_size_upstream_transition_fill WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1"),
                      ];

                      $printed = false;

                      foreach ($datasets as $src => $rows) {
                        if (empty($rows)) continue;

                        $g = $rows[0];
                        switch ($src) {
                          case 'coarse':
                            $T3p4 = (float)($g['CumRet9']  ?? 0);
                            $T3p8 = (float)($g['CumRet10'] ?? 0);
                            $TNo4 = (float)($g['CumRet11'] ?? 0);
                            break;
                          case 'fine':
                            $T3p4 = (float)($g['CumRet9']  ?? 0);
                            $T3p8 = (float)($g['CumRet11'] ?? 0);
                            $TNo4 = (float)($g['CumRet12'] ?? 0);
                            break;
                          case 'lpf':
                            $T3p4 = (float)($g['CumRet5']  ?? 0);
                            $T3p8 = (float)($g['CumRet6']  ?? 0);
                            $TNo4 = (float)($g['CumRet7']  ?? 0);
                            break;
                          case 'upstream':
                            $T3p4 = (float)($g['CumRet8']  ?? 0);
                            $T3p8 = (float)($g['CumRet10'] ?? 0);
                            $TNo4 = (float)($g['CumRet11'] ?? 0);
                            break;
                          default: // general
                            $T3p4 = (float)($g['CumRet11'] ?? 0);
                            $T3p8 = (float)($g['CumRet13'] ?? 0);
                            $TNo4 = (float)($g['CumRet14'] ?? 0);
                        }

                        if     ($T3p4 > 0)                                 $metodo = 'C';
                        elseif ($T3p8 > 0 && $T3p4 == 0)                   $metodo = 'B';
                        elseif ($TNo4 > 0 && $T3p4 == 0 && $T3p8 == 0)     $metodo = 'A';
                        else                                               $metodo = 'No se puede determinar el método';

                        $corr = ($T3p4 > 5) ? 'Corrección por Sobre Tamaño, realizar SG Partículas Finas y Gruesas' : '';

                        echo '<tr>';
                        echo '<td>'.htmlspecialchars($sid.'-'.$snum.'-SP').'</td>';
                        echo '<td>'.htmlspecialchars($metodo).'</td>';
                        echo '<td>'.htmlspecialchars($corr).'</td>';
                        echo '</tr>';

                        $printed = true;
                      }

                      if (!$printed) {
                        echo '<tr>';
                        echo '<td>'.htmlspecialchars($sid.'-'.$snum.'-SP').'</td>';
                        echo '<td>No data</td>';
                        echo '<td>Sin resultados de granulometría para inferir método</td>';
                        echo '</tr>';
                      }

                    endforeach;
                    ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Método Proctor -->

          <!-- (Sección “Muestras Registradas” está comentada como en tu version) -->

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
                  <?= htmlspecialchars(translateStatus($status)) ?>
                  <span class="badge bg-primary rounded-pill"><?= (int)$count ?></span>
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
                function normalize($v){ return strtoupper(trim($v)); }

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

                // Reusar $Requisitions (14 días) para coherencia, o cambiar a ventana propia
                foreach ($Requisitions as $requisition) {
                  if (empty($requisition['Test_Type'])) continue;

                  $sampleID    = normalize($requisition['Sample_ID']);
                  $sampleNumber= normalize($requisition['Sample_Number']);
                  $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));

                  foreach ($testTypesArray as $testTypeRaw) {
                    if ($testTypeRaw === '') continue;
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
                    echo '<span class="badge bg-primary rounded-pill">' . (int)$count . '</span>';
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
  </section>
</main><!-- End #main -->

<!-- Búsqueda instantánea (para el widget de “muestras a botar” si lo reactivas) -->
<script>
  const input = document.getElementById('buscarMuestras');
  if (input) {
    input.addEventListener('keyup', function() {
      const filtro = this.value.toLowerCase();
      const filas = document.querySelectorAll('#tablaMuestras tbody tr');
      filas.forEach(fila => {
        const textoFila = fila.textContent.toLowerCase();
        fila.style.display = textoFila.includes(filtro) ? '' : 'none';
      });
    });
  }
</script>

<?php include_once('../components/footer.php'); ?>
