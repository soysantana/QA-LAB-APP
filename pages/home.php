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
  </div>

  <?php echo display_msg($msg); ?>

  <?php
  /* =========================
     Helpers & utilidades
  ========================== */
  function normalize_str($v) { return strtoupper(trim((string)$v)); }
  function make_key($sid,$num,$tt){ return normalize_str($sid) . '|' . normalize_str($num) . '|' . normalize_str($tt); }

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

  /* =========================
     Caché simple (archivo)
  ========================== */
  function cache_get($key, $ttl=600){
    $file = sys_get_temp_dir()."/{$key}.cache.php";
    if (!is_file($file)) return null;
    if (filemtime($file) + $ttl < time()) return null;
    return include $file; // retorna array
  }
  function cache_set($key, $data){
    $file = sys_get_temp_dir()."/{$key}.cache.php";
    @file_put_contents($file, "<?php\nreturn ".var_export($data,true).";");
  }

  /* =========================
     Ventana temporal y datos
  ========================== */
  $week14 = date('Y-m-d', strtotime('-14 days'));
  $week7  = date('Y-m-d', strtotime('-7 days'));
  $week31 = date('Y-m-d', strtotime('-31 days'));

  // Requisiciones recientes (LIMIT para no cargar de más)
  $Requisitions = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date >= '{$week14}'
    ORDER BY Registed_Date DESC
    LIMIT 200
  ");

  /* =========================
     Precarga en SETS (hash) con filtro + caché
  ========================== */
  // NOTA: ajusta los nombres de campos de fecha si difieren (Start_Date / Created_At / etc.)
  function mk($sid,$num,$tt){ return strtoupper(trim($sid)).'|'.strtoupper(trim($num)).'|'.strtoupper(trim($tt)); }

  $sets = cache_get('dashboard_sets_fast', 600);
  if (!$sets) {
    $sets = ['Preparation'=>[], 'Realization'=>[], 'Delivery'=>[], 'Review'=>[], 'Repeat'=>[]];

    // preparation
    $pre  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation WHERE Start_Date >= '{$week14}'");
    foreach ($pre as $r)  $sets['Preparation'][ mk($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;

    // realization
    $real = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_realization WHERE Start_Date >= '{$week14}'");
    foreach ($real as $r) $sets['Realization'][ mk($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;

    // delivery (ajusta el nombre si tu tabla usa otro campo de fecha)
    $delv = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery WHERE Register_Date >= '{$week14}'");
    foreach ($delv as $r) $sets['Delivery'][ mk($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;

    // repeat
    $rep  = find_by_sql("SELECT Sample_ID, Sample_Number, Test_Type FROM test_repeat WHERE Start_Date >= '{$week14}'");
    foreach ($rep as $r)  $sets['Repeat'][ mk($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;

    // review (excluye reviewed) + filtro fecha
    $rev  = find_by_sql("
      SELECT p.Sample_ID, p.Sample_Number, p.Test_Type
      FROM test_review p
      LEFT JOIN test_reviewed r
        ON r.Sample_ID=p.Sample_ID AND r.Sample_Number=p.Sample_Number AND r.Test_Type=p.Test_Type
      WHERE r.Sample_ID IS NULL
        AND p.Start_Date >= '{$week14}'
    ");
    foreach ($rev as $r)  $sets['Review'][ mk($r['Sample_ID'],$r['Sample_Number'],$r['Test_Type']) ] = true;

    cache_set('dashboard_sets_fast', $sets);
  }

  // Contadores de estado
  $statusCounts = ['Preparation'=>0,'Realization'=>0,'Delivery'=>0,'Review'=>0,'Repeat'=>0];
  ?>

  <section class="section dashboard">
    <div class="row">

      <!-- Left -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Proceso de muestreo -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start"><h6>Filtrar</h6></li>
                  <li><a class="dropdown-item" href="#">Hoy</a></li>
                  <li><a class="dropdown-item" href="#">Este mes</a></li>
                  <li><a class="dropdown-item" href="#">Este año</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Proceso de muestreo <span>| Últimos 14 días (máx. 200)</span></h5>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th>Muestra</th>
                      <th>Número</th>
                      <th>Tipo de prueba</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach ($Requisitions as $req) {
                    if (empty($req['Test_Type'])) continue;
                    $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
                    $types = array_filter(array_map(fn($t)=> normalize_str($t), explode(',', $req['Test_Type'])));
                    foreach ($types as $tt) {
                      $k = make_key($sid,$num,$tt);
                      // prioridad de estado
                      if     (isset($sets['Repeat'][$k]))      $st='Repeat';
                      elseif (isset($sets['Review'][$k]))      $st='Review';
                      elseif (isset($sets['Delivery'][$k]))    $st='Delivery';
                      elseif (isset($sets['Realization'][$k])) $st='Realization';
                      elseif (isset($sets['Preparation'][$k])) $st='Preparation';
                      else continue;

                      $statusCounts[$st]++;
                      echo '<tr>';
                      echo '<td>'.htmlspecialchars($sid).'</td>';
                      echo '<td>'.htmlspecialchars($num).'</td>';
                      echo '<td>'.htmlspecialchars($tt).'</td>';
                      echo '<td><span class="badge bg-'.getBadgeClass($st).'">'.translateStatus($st).'</span></td>';
                      echo '</tr>';
                    }
                  }
                  ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /Proceso de muestreo -->

          <!-- Ensayos en Repetición -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start"><h6>Filtrar</h6></li>
                  <li><a class="dropdown-item" href="#">Hoy</a></li>
                  <li><a class="dropdown-item" href="#">Este mes</a></li>
                  <li><a class="dropdown-item" href="#">Este año</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Ensayos en Repetición <span>| Últimos 7 días</span></h5>
                <?php
                $repeatRows = find_by_sql("
                  SELECT Sample_ID, Sample_Number, Test_Type, Start_Date, Send_By
                  FROM test_repeat
                  WHERE Start_Date >= '{$week7}'
                  ORDER BY Start_Date DESC
                ");
                ?>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th>Muestra</th>
                      <th>Número</th>
                      <th>Tipo</th>
                      <th>Fecha</th>
                      <th>Enviado por</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($repeatRows as $r): ?>
                      <tr>
                        <td><?= htmlspecialchars($r['Sample_ID']) ?></td>
                        <td><?= htmlspecialchars($r['Sample_Number']) ?></td>
                        <td><?= htmlspecialchars($r['Test_Type']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($r['Start_Date']))) ?></td>
                        <td><?= htmlspecialchars($r['Send_By']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /Ensayos en Repetición -->
          <!-- Método Proctor (SP) -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start"><h6>Filtrar</h6></li>
                  <li><a class="dropdown-item" href="#">Hoy</a></li>
                  <li><a class="dropdown-item" href="#">Este mes</a></li>
                  <li><a class="dropdown-item" href="#">Este año</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Método para Proctor <span>| Últimos 31 días</span></h5>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th>Muestra</th>
                      <th>Método</th>
                      <th>Comentario</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Requisiciones últimas 31d
                    $ReqSP = find_by_sql("
                      SELECT Sample_ID, Sample_Number, Sample_Date, Test_Type
                      FROM lab_test_requisition_form
                      WHERE Registed_Date >= '{$week31}'
                      ORDER BY Registed_Date DESC
                    ");

                    foreach ($ReqSP as $req) {
                      if (empty($req['Test_Type'])) continue;
                      $sid   = $req['Sample_ID'];
                      $snum  = $req['Sample_Number'];
                      $types = array_filter(array_map(fn($t)=> normalize_str($t), explode(',', $req['Test_Type'])));

                      foreach ($types as $tt) {
                        if ($tt !== 'SP') continue;

                        // Solo pendientes (no en Preparation ni Review)
                        $k = make_key($sid,$snum,'SP');
                        if (isset($sets['Preparation'][$k]) || isset($sets['Review'][$k])) continue;

                        // Escapar para consulta
                        $sidEsc  = $db->escape($sid);
                        $snumEsc = $db->escape($snum);

                        // Traer la primera granulometría disponible: UNION ALL + LIMIT 1
                        $gs = find_by_sql("
                          SELECT 'general'  as src, CumRet11 as t34, CumRet13 as t38, CumRet14 as tNo4
                            FROM grain_size_general  WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1
                          UNION ALL
                          SELECT 'coarse',  CumRet9,  CumRet10, CumRet11
                            FROM grain_size_coarse   WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1
                          UNION ALL
                          SELECT 'fine',    CumRet9,  CumRet11, CumRet12
                            FROM grain_size_fine     WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1
                          UNION ALL
                          SELECT 'lpf',     CumRet5,  CumRet6,  CumRet7
                            FROM grain_size_lpf      WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1
                          UNION ALL
                          SELECT 'upstream',CumRet8,  CumRet10, CumRet11
                            FROM grain_size_upstream_transition_fill
                            WHERE Sample_ID='{$sidEsc}' AND Sample_Number='{$snumEsc}' LIMIT 1
                          LIMIT 1
                        ");

                        if (!empty($gs)) {
                          $g    = $gs[0];
                          $T3p4 = (float)($g['t34']  ?? 0);
                          $T3p8 = (float)($g['t38']  ?? 0);
                          $TNo4 = (float)($g['tNo4'] ?? 0);

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
                        } else {
                          echo '<tr>';
                          echo '<td>'.htmlspecialchars($sid.'-'.$snum.'-SP').'</td>';
                          echo '<td>No data</td>';
                          echo '<td>Sin resultados de granulometría para inferir método</td>';
                          echo '</tr>';
                        }
                      }
                    }
                    ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
          <!-- /Método Proctor (SP) -->

        </div>
      </div>
      <!-- /Left -->

      <!-- Right -->
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
        <!-- /Cantidades en Proceso -->

        <!-- Cantidad de Ensayos Pendientes -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Cantidad de Ensayos Pendientes</h5>
              <ul class="list-group">
                <?php
                // Reusa Requisitions y los sets de estado ya cargados
                $typeCount = [];
                $seen = [];

                foreach ($Requisitions as $req) {
                  if (empty($req['Test_Type'])) continue;
                  $sid = $req['Sample_ID']; $num = $req['Sample_Number'];
                  $types = array_filter(array_map(fn($t)=> normalize_str($t), explode(',', $req['Test_Type'])));
                  foreach ($types as $tt) {
                    $k = make_key($sid,$num,$tt);
                    if (isset($seen[$k])) continue;
                    $seen[$k] = true;

                    // Pendiente: no aparece en ninguno de los sets
                    if (
                      !isset($sets['Preparation'][$k]) &&
                      !isset($sets['Realization'][$k]) &&
                      !isset($sets['Delivery'][$k]) &&
                      !isset($sets['Review'][$k]) &&
                      !isset($sets['Repeat'][$k])
                    ) {
                      $typeCount[$tt] = ($typeCount[$tt] ?? 0) + 1;
                    }
                  }
                }

                if (empty($typeCount)) {
                  echo '<li class="list-group-item">✅ No hay ensayos pendientes</li>';
                } else {
                  foreach ($typeCount as $testType => $count) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    echo '<h5><code>'.htmlspecialchars($testType).'</code></h5>';
                    echo '<span class="badge bg-primary rounded-pill">'.(int)$count.'</span>';
                    echo '</li>';
                  }
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
        <!-- /Cantidad de Ensayos Pendientes -->
      </div>
      <!-- /Right -->

    </div>
  </section>
</main>

<!-- Búsqueda instantánea (si vuelves a activar módulos con tablas buscables) -->
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
