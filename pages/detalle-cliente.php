<?php
  $page_title = 'Detalle del Cliente';
  require_once('../config/load.php');
  $dCliente = 'show';
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Detalle del cliente</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Detalle del cliente</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <?php $Requisitions = find_all('lab_test_requisition_form'); ?>



  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>
        <div class="table-responsive">
        <table class="table table-hover table-sm table-bordered">
      <thead>
        <tr>
          <th>Fecha de Muestreo</th>
          <th>Clientes</th>
          <th>ID de Muestras</th>
          <th>Numero de Muestras</th>
          <th>MC</th>
          <th>AL</th>
          <th>GS</th>
          <th>SP</th>
          <th>SG</th>
          <th>AR</th>
          <th>SCT</th>
          <th>LAA</th>
          <th>SND</th>
          <th>Con</th>
          <th>UCS</th>
          <th>PLT</th>
          <th>BTS</th>
          <th>HY</th>
          <th>DHY</th>
          <th>PH</th>
          <th>Per</th>
          <th>Sha</th>
          <th>Den</th>
          <th>Cru</th>
          <th>Ens. Solicitados</th>
          <th>Ens. Realizados</th>
          <th>Progreso de Ensayos</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($Requisitions as $Requisition): ?>
        
        <?php // Contar ensayos solicitados y entregados
         $count_solicitados = 0;
         $count_entregados = 0;

         // Array para almacenar los resultados de cada Test_Type
         $test_status = [];
         
         for ($i = 1; $i <= 20; $i++) {
          $column_name = 'Test_Type' . $i;
          if (isset($Requisition[$column_name]) && !empty($Requisition[$column_name])) {
            $count_solicitados++;
            // Verificar si el ensayo ha sido entregado
            $query = "SELECT COUNT(*) AS entregado FROM test_delivery WHERE Sample_Name = '{$Requisition['Sample_ID']}' AND Sample_Number = '{$Requisition['Sample_Number']}' AND Test_Type = '{$Requisition[$column_name]}'";
            $result = $db->query($query);
            $row = $result->fetch_assoc();
            if ($row['entregado'] > 0) {
              $test_status[$i] = 'entregado';  // Guardar si fue entregado
              $count_entregados++;
            } else {
              $test_status[$i] = 'no_entregado';  // Guardar si no fue entregado
              }
            } else {
              $test_status[$i] = 'no_test';  // Guardar si no hay test
              }
            }
            // Calcular el porcentaje de ensayos entregados
            $porce_entregados = round(($count_entregados / $count_solicitados) * 100);
        ?>
        <tr>
        <td class="text-center"><?php echo $Requisition['Sample_Date']; ?></td>
        <td class="text-center"><?php echo $Requisition['Client']; ?></td>
        <td class="text-center"><?php echo $Requisition['Sample_ID']; ?></td>
        <td class="text-center"><?php echo $Requisition['Sample_Number']; ?></td> 
        <?php for ($i = 1; $i <= 20; $i++): ?> <!-- For para la selecion de Check or X -->
          <td class="text-center">
            <?php if ($test_status[$i] === 'entregado'): ?>
              <i class="bi bi-check fs-4 text-success"></i> <!-- Cotejo azul -->
              <?php elseif ($test_status[$i] === 'no_entregado'): ?>
              <i class="bi bi-x fs-4 text-danger"></i> <!-- X roja -->
              <?php else: ?>
              <i>-</i> <!-- Sin test -->
              <?php endif; ?>
          </td>
        <?php endfor; ?>
          <td class="text-center"><span class="badge bg-primary rounded-pill"><?php echo $count_solicitados; ?></span></td>
          <td class="text-center"><span class="badge bg-success rounded-pill"><?php echo $count_entregados; ?></span></td>
          <td class="text-center">
            <div class="progress" role="progressbar" aria-label="Porce Entregados" aria-valuenow="<?php echo $porce_entregados; ?>" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $porce_entregados; ?>%"><?php echo $porce_entregados; ?>%</div>
            </div>
          </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
        </div>
      </div>
    </div>
  </div>
  
  
  <div class="col-lg-12">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">RESUMEN DE MUESTRAS POR CLIENTE</h5>
          <?php // Resumen de Ensayos y Muestras por Clientes
            $clientes_ensayos = [];
            $clientes_muestras = []; // Para almacenar las muestras únicas por cliente

            foreach ($Requisitions as $Requisition) {
              $cliente = $Requisition['Client'];

            // Inicializar el cliente en el array si no existe
            if (!isset($clientes_ensayos[$cliente])) {
              $clientes_ensayos[$cliente] = 0;
              $clientes_muestras[$cliente] = []; // Inicializar un array para las muestras
              }
                  
              // Contar ensayos solicitados para este cliente
              for ($i = 1; $i <= 20; $i++) {
                $column_name = 'Test_Type' . $i;
                if (!empty($Requisition[$column_name])) {
                  $clientes_ensayos[$cliente]++;
                }
              }
                  
              // Almacenar las muestras únicas por cliente (Sample_ID y Sample_Number)
              $clientes_muestras[$cliente][] = [
                'Sample_ID' => $Requisition['Sample_ID'],
                'Sample_Number' => $Requisition['Sample_Number']
              ];
            }
            
            // Calcular el total de muestras únicas por cliente
            $total_muestras = [];
            foreach ($clientes_muestras as $cliente => $muestras) {
            // Extraer las combinaciones únicas de Sample_ID y Sample_Number
              $muestras_unicas = array_unique($muestras, SORT_REGULAR);
              $total_muestras[$cliente] = count($muestras_unicas); // Contar muestras únicas
              }
              
              // Calcular totales generales
              $total_ensayos_generales = array_sum($clientes_ensayos);
              $total_muestras_generales = array_sum($total_muestras);

              // Preparar datos para la tabla
              $resumen_clientes = [];
                
              foreach ($clientes_ensayos as $cliente => $total_ensayos) {
                $porcentaje_ensayos = ($total_ensayos_generales > 0) ? ($total_ensayos / $total_ensayos_generales) * 100 : 0;
                $porcentaje_muestras = ($total_muestras_generales > 0) ? ($total_muestras[$cliente] / $total_muestras_generales) * 100 : 0;

                $resumen_clientes[$cliente] = [
                  'total_ensayos' => $total_ensayos,
                  'total_muestras' => isset($total_muestras[$cliente]) ? $total_muestras[$cliente] : 0,
                  'porcentaje_ensayos' => round($porcentaje_ensayos),
                  'porcentaje_muestras' => round($porcentaje_muestras),
                ];
              }

              $data = [];
              $data2 = [];
              foreach ($resumen_clientes as $cliente => $resumen) {
                $data[] = ['name' => $cliente, 'value' => $resumen['total_ensayos']];
                $data2[] = ['name' => $cliente, 'value' => $resumen['total_muestras']];
              }
          ?>
          <!-- Tabla de resumen de muestras por cliente -->
          <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">Cliente</th>
                <th scope="col">Total de ensayos</th>
                <th scope="col">Cantidad de muestras</th>
                <th scope="col">Porcentaje de ensayos</th>
                <th scope="col">Porcentaje de muestras</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($resumen_clientes as $cliente => $datos): ?>
              <tr>
                <td class="text-center"><?php echo htmlspecialchars($cliente); ?></td>
                <td class="text-center"><?php echo $datos['total_ensayos']; ?></td>
                <td class="text-center"><?php echo $datos['total_muestras']; ?></td>
                <td class="text-center"><?php echo $datos['porcentaje_ensayos'] . '%'; ?></td>
                <td class="text-center"><?php echo $datos['porcentaje_muestras'] . '%'; ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <!-- Fin de tabla resumen por cliente -->
        </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Distribución porcentual de Ensayos por Cliente</h5>
        
        <!-- Distribucion de ensayos por cliente -->
        <div id="eClientChart" style="min-height: 400px;" class="echart"></div>

        <script>
          const eclientData = <?php echo json_encode($data); ?>;

          document.addEventListener("DOMContentLoaded", () => {
            echarts.init(document.querySelector("#eClientChart")).setOption({
              title: {
                text: 'Distribucion de Ensayos por Cliente',
                left: 'center'
              },
              tooltip: {
                trigger: 'item'
              },
              legend: {
                orient: 'vertical',
                left: 'left'
              },
              series: [{
                type: 'pie',
                radius: '50%',
                data: eclientData,
                emphasis: {
                  itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                  }
                }
              }]
            });
          });
        </script>
        <!-- End Distribucion de ensayos por cliente -->

      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Distribución porcentual de Muestras por Cliente</h5>
        
        <!-- Distribucion de muestras por cliente -->
        <div id="mClientChart" style="min-height: 400px;" class="echart"></div>

        <script>
          const mClientData = <?php echo json_encode($data2); ?>;

          document.addEventListener("DOMContentLoaded", () => {
            echarts.init(document.querySelector("#mClientChart")).setOption({
              title: {
                text: 'Distribucion de Muestras por Cliente',
                left: 'center'
              },
              tooltip: {
                trigger: 'item'
              },
              legend: {
                orient: 'vertical',
                left: 'left'
              },
              series: [{
                type: 'pie',
                radius: '50%',
                data: mClientData,
                emphasis: {
                  itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                  }
                }
              }]
            });
          });
        </script>
        <!-- End Distribucion de muestras por cliente -->

      </div>
    </div>
  </div>

  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>