<?php
  $page_title = 'Desempeño';
  require_once('../config/load.php');
  $rendimiento = 'show';
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Desempeño</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Desempeño</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <?php 

// Función para obtener los nombres de los ensayos
function obtenerNombresEnsayos($db) {
  $ensayos = [];
  for ($i = 1; $i <= 20; $i++) {
      $sql = "SELECT DISTINCT Test_Type$i FROM lab_test_requisition_form WHERE Test_Type$i IS NOT NULL";
      $result = $db->query($sql);
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              // Asignar los nombres a los índices correctos
              $ensayos[$i] = $row["Test_Type$i"];
          }
      }
  }
  return array_filter($ensayos); // Eliminar entradas vacías
}



// Función para obtener muestras registradas por tipo de ensayo
function obtenerMuestrasRegistradasPorTipo($db, $interval) {
  $sql = "SELECT Register_By, COUNT(*) as Total, ";
  
  // Generar las columnas de conteo por Test_Type (1 a 20)
  for ($i = 1; $i <= 20; $i++) {
      $sql .= "SUM(CASE WHEN Test_Type$i IS NOT NULL AND Test_Type$i != '' THEN 1 ELSE 0 END) AS Total_Test_Type$i";
      if ($i < 20) {
          $sql .= ", ";  // Para agregar comas entre las columnas, excepto después de la última
      }
  }
  
  $sql .= " FROM lab_test_requisition_form 
            WHERE DATE(Registed_Date) $interval
            GROUP BY Register_By";
  
  return $db->query($sql);
}


// Función para obtener muestras en preparación, realización y entrega por técnico y tipo de ensayo
function obtenerMuestrasPorTipo($db, $interval, $tabla, $fechaCampo) {
  $sql = "SELECT Technician, Test_Type, COUNT(*) as Total
          FROM $tabla
          WHERE DATE($fechaCampo) $interval
          GROUP BY Technician, Test_Type";
  return $db->query($sql);
}

// Función para obtener ensayos digitados por técnico y tipo de ensayo
function obtenerEnsayosDigitadosPorTipo($db, $interval, $tabla, $fechaCampo) {
  $sql = "SELECT Register_By, Test_Type, COUNT(*) as Total
          FROM $tabla
          WHERE DATE($fechaCampo) $interval
          GROUP BY Register_By, Test_Type";
  return $db->query($sql);
}

// Definir los intervalos (diario, semanal, mensual, anual)
$intervalos = [
  'Diario' => "= CURDATE()",
  'Semanal' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()",
  'Mensual' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()",
  'Anual' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()",
];

// Arrays para almacenar los resultados
$muestras_registradas = [];
$muestras_preparadas = [];
$muestras_realizadas = [];
$muestras_entregadas = [];
$muestras_digitadas = []; // Nueva sección para ensayos digitados

// Obtener los datos para los diferentes intervalos
foreach ($intervalos as $nombre_intervalo => $intervalo_sql) {
  $muestras_registradas[$nombre_intervalo] = obtenerMuestrasRegistradasPorTipo($db, $intervalo_sql);
  $muestras_preparadas[$nombre_intervalo] = obtenerMuestrasPorTipo($db, $intervalo_sql, 'test_preparation', 'Start_Date');
  $muestras_realizadas[$nombre_intervalo] = obtenerMuestrasPorTipo($db, $intervalo_sql, 'test_realization', 'Start_Date');
  $muestras_entregadas[$nombre_intervalo] = obtenerMuestrasPorTipo($db, $intervalo_sql, 'test_delivery', 'Start_Date');
  $muestras_digitadas[$nombre_intervalo] = obtenerEnsayosDigitadosPorTipo($db, $intervalo_sql, 'test_review', 'Start_Date'); // Cambia el nombre de la tabla y el campo según tu esquema
}

// Obtener los nombres de los ensayos
$nombres_ensayos = obtenerNombresEnsayos($db);


function mostrarTablaPorTipoEnsayo($resultado, $titulo, $nombres_ensayos) {
  // Array para almacenar las columnas que no tienen solo valores vacíos
  $columnasVisibles = [];

  // Primero, recorrer los resultados para verificar qué columnas no están vacías
  if ($resultado->num_rows > 0) {
      for ($i = 1; $i <= 20; $i++) {
          $tieneDatos = false;
          $resultado->data_seek(0); // Reiniciar puntero de resultados
          while ($row = $resultado->fetch_assoc()) {
              if (!empty($row["Total_Test_Type$i"])) {
                  $tieneDatos = true;
                  break;
              }
          }

          // Si la columna tiene datos, la añadimos a las columnas visibles
          if ($tieneDatos) {
              $columnasVisibles[] = $i;
          }
      }
  }

  // Mostrar tabla con las columnas visibles
  echo "<table class='table table-bordered'>";
  echo "<thead><tr><th>Registrador</th><th>Total</th>";

  // Mostrar solo las columnas visibles con los nombres personalizados
  foreach ($columnasVisibles as $i) {
      $nombreEnsayo = isset($nombres_ensayos[$i]) ? $nombres_ensayos[$i] : "Test_Type$i";
      echo "<th>$nombreEnsayo</th>";
  }
  echo "</tr></thead><tbody>";

  // Mostrar los datos de cada registrador
  if ($resultado->num_rows > 0) {
      $resultado->data_seek(0); // Reiniciar el puntero de resultados
      while ($row = $resultado->fetch_assoc()) {
          echo "<tr><td>{$row['Register_By']}</td><td>{$row['Total']}</td>";
          
          // Mostrar solo los valores de las columnas visibles
          foreach ($columnasVisibles as $i) {
              $totalTipo = isset($row["Total_Test_Type$i"]) ? $row["Total_Test_Type$i"] : 0;
              echo "<td>" . ($totalTipo > 0 ? $totalTipo : '') . "</td>";
          }
          
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='" . (count($columnasVisibles) + 2) . "'>No hay datos disponibles</td></tr>";
  }

  echo "</tbody></table>";
}



// Función para mostrar otras tablas (preparadas, realizadas, entregadas, digitadas)
function mostrarTablaMuestras($resultado, $titulo, $campoTecnico = 'Technician') {
  $datos = [];
  if ($resultado->num_rows > 0) {
      while ($row = $resultado->fetch_assoc()) {
          $tecnico = $row[$campoTecnico];
          $testType = $row['Test_Type'];
          $total = $row['Total'];
          $datos[$tecnico][$testType] = $total;
      }
  }

  $tiposEnsayo = [];
  foreach ($datos as $tecnico => $tests) {
      foreach ($tests as $testType => $total) {
          if (!in_array($testType, $tiposEnsayo)) {
              $tiposEnsayo[] = $testType;
          }
      }
  }
  sort($tiposEnsayo);

  echo "<table class='table table-bordered'>";
  echo "<thead><tr><th>Técnico</th>";
  foreach ($tiposEnsayo as $testType) {
      echo "<th>$testType</th>";
  }
  echo "</tr></thead><tbody>";

  if (!empty($datos)) {
      foreach ($datos as $tecnico => $tests) {
          echo "<tr><td>$tecnico</td>";
          foreach ($tiposEnsayo as $testType) {
              $total = isset($tests[$testType]) ? $tests[$testType] : 0;
              echo "<td>$total</td>";
          }
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='" . (count($tiposEnsayo) + 1) . "'>No hay datos disponibles</td></tr>";
  }
  echo "</tbody></table>";
}


// Función para mostrar secciones en un "accordion" por intervalo
function mostrarAccordionTabla($datos, $intervalos, $titulo, $nombres_ensayos = null) {
  echo "<div class='accordion' id='accordion_$titulo'>";
  foreach ($intervalos as $nombre_intervalo => $intervalo_sql) {
      $accordion_id = "accordion_".str_replace(' ', '_', $titulo)."_".$nombre_intervalo;
      echo "<div class='accordion-item'>";
      echo "<h2 class='accordion-header' id='heading_$accordion_id'>";
      echo "<button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapse_$accordion_id' aria-expanded='true' aria-controls='collapse_$accordion_id'>";
      echo "$nombre_intervalo";
      echo "</button></h2>";
      echo "<div id='collapse_$accordion_id' class='accordion-collapse collapse'>";
      echo "<div class='accordion-body'>";

      // Mostrar la tabla para este intervalo
      if ($nombres_ensayos) {
          mostrarTablaPorTipoEnsayo($datos[$nombre_intervalo], "Muestras Registradas $nombre_intervalo por Tipo de Ensayo", $nombres_ensayos);
      } else {
          mostrarTablaMuestras($datos[$nombre_intervalo], "Datos $nombre_intervalo");
      }

      echo "</div></div></div>";
  }
  echo "</div>";
}

// Mostrar las secciones para Muestras Registradas, Preparadas, Realizadas, Entregadas y Digitadas
echo "<div class='container'>";
echo "<ul class='nav nav-tabs' id='myTab' role='tablist'>";
echo "<li class='nav-item' role='presentation'><a class='nav-link active' id='registradas-tab' data-bs-toggle='tab' href='#registradas' role='tab' aria-controls='registradas' aria-selected='true'>Muestras Registradas</a></li>";
echo "<li class='nav-item' role='presentation'><a class='nav-link' id='preparadas-tab' data-bs-toggle='tab' href='#preparadas' role='tab' aria-controls='preparadas' aria-selected='false'>Ensayos Preparados</a></li>";
echo "<li class='nav-item' role='presentation'><a class='nav-link' id='realizadas-tab' data-bs-toggle='tab' href='#realizadas' role='tab' aria-controls='realizadas' aria-selected='false'>Ensayos Realizados</a></li>";
echo "<li class='nav-item' role='presentation'><a class='nav-link' id='entregadas-tab' data-bs-toggle='tab' href='#entregadas' role='tab' aria-controls='entregadas' aria-selected='false'>Ensayos Entregados</a></li>";
echo "<li class='nav-item' role='presentation'><a class='nav-link' id='digitados-tab' data-bs-toggle='tab' href='#digitados' role='tab' aria-controls='digitados' aria-selected='false'>Ensayos Digitados</a></li>"; // Nueva pestaña
echo "</ul>";

echo "<div class='tab-content' id='myTabContent'>";
echo "<div id='registradas' class='tab-pane fade show active' role='tabpanel' aria-labelledby='registradas-tab'>";
mostrarAccordionTabla($muestras_registradas, $intervalos, "Muestras Registradas", $nombres_ensayos);
echo "</div>";

echo "<div id='preparadas' class='tab-pane fade' role='tabpanel' aria-labelledby='preparadas-tab'>";
mostrarAccordionTabla($muestras_preparadas, $intervalos, "Ensayos Preparados");
echo "</div>";

echo "<div id='realizadas' class='tab-pane fade' role='tabpanel' aria-labelledby='realizadas-tab'>";
mostrarAccordionTabla($muestras_realizadas, $intervalos, "Ensayos Realizados");
echo "</div>";

echo "<div id='entregadas' class='tab-pane fade' role='tabpanel' aria-labelledby='entregadas-tab'>";
mostrarAccordionTabla($muestras_entregadas, $intervalos, "Ensayos Entregados");
echo "</div>";

echo "<div id='digitados' class='tab-pane fade' role='tabpanel' aria-labelledby='digitados-tab'>"; // Nuevo contenido para la pestaña
mostrarAccordionTabla($muestras_digitadas, $intervalos, "Ensayos Digitados", 'Register_By');
echo "</div>";
echo "</div>";
echo "</div>";



?>




  

  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>