<?php
$page_title = 'Desempeño';
require_once('../config/load.php');
$rendimiento = 'show';
page_require_level(2);
include_once('../components/header.php');
?>
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
</div>
<section class="section">
<div class="row">
<?php

function obtenerNombresEnsayos($db) {
    $sql = "SELECT DISTINCT Test_Type FROM lab_test_requisition_form WHERE Test_Type IS NOT NULL AND Test_Type != ''";
    $result = $db->query($sql);
    $ensayos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ensayos[] = $row['Test_Type'];
        }
    }
    return $ensayos;
}


function obtenerMuestrasRegistradasPorTipo($db, $interval) {
  $sql = "SELECT Register_By, Test_Type, COUNT(*) as Total
          FROM lab_test_requisition_form
          WHERE DATE(Registed_Date) $interval
          GROUP BY Register_By, Test_Type";
  
  return $db->query($sql);
}


function obtenerMuestrasPorTipo($db, $interval, $tabla, $fechaCampo) {
    $sql = "SELECT Technician, Test_Type, COUNT(*) as Total FROM $tabla WHERE DATE($fechaCampo) $interval GROUP BY Technician, Test_Type";
    return $db->query($sql);
}

function obtenerEnsayosDigitadosPorTipo($db, $interval, $tabla, $fechaCampo) {
    $sql = "SELECT Register_By AS Technician, Test_Type, COUNT(*) as Total FROM $tabla WHERE DATE($fechaCampo) $interval GROUP BY Register_By, Test_Type";
    return $db->query($sql);
}

$intervalos = [
  'Diario' => "= CURDATE()",
  'Semanal' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()",
  'Mensual' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()",
  'Anual' => "BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()",
];

$muestras_registradas = [];
$muestras_preparadas = [];
$muestras_realizadas = [];
$muestras_entregadas = [];
$muestras_digitadas = [];

foreach ($intervalos as $nombre => $intervalo) {
    $muestras_registradas[$nombre] = obtenerMuestrasRegistradasPorTipo($db, $intervalo);
    $muestras_preparadas[$nombre] = obtenerMuestrasPorTipo($db, $intervalo, 'test_preparation', 'Start_Date');
    $muestras_realizadas[$nombre] = obtenerMuestrasPorTipo($db, $intervalo, 'test_realization', 'Start_Date');
    $muestras_entregadas[$nombre] = obtenerMuestrasPorTipo($db, $intervalo, 'test_delivery', 'Start_Date');
    $muestras_digitadas[$nombre] = obtenerEnsayosDigitadosPorTipo($db, $intervalo, 'test_review', 'Start_Date');
}

$nombres_ensayos = obtenerNombresEnsayos($db);

function mostrarTablaPorTipoEnsayo($resultado, $titulo, $nombres_ensayos) {
    $columnasVisibles = [];
    if ($resultado->num_rows > 0) {
        for ($i = 1; $i <= 20; $i++) {
            $resultado->data_seek(0);
            while ($row = $resultado->fetch_assoc()) {
                if (!empty($row["Total_Test_Type$i"])) {
                    $columnasVisibles[] = $i;
                    break;
                }
            }
        }
    }
    echo "<div class='table-responsive'><table class='table table-bordered'>";
    echo "<thead><tr><th>Registrador</th><th>Total</th>";
    foreach ($columnasVisibles as $i) {
        $nombre = isset($nombres_ensayos[$i - 1]) ? $nombres_ensayos[$i - 1] : "Tipo $i";
        echo "<th>$nombre</th>";
    }
    echo "</tr></thead><tbody>";
    $resultado->data_seek(0);
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr><td>{$row['Register_By']}</td><td>{$row['Total']}</td>";
        foreach ($columnasVisibles as $i) {
            $val = $row["Total_Test_Type$i"] ?? '';
            echo "<td>" . ($val > 0 ? $val : '') . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}

function mostrarTablaMuestras($resultado) {
    $datos = [];
    while ($row = $resultado->fetch_assoc()) {
        $tecnico = $row['Technician'];
        $tipo = $row['Test_Type'];
        $datos[$tecnico][$tipo] = $row['Total'];
    }
    $tipos = array_unique(array_merge(...array_values(array_map('array_keys', $datos))));
    sort($tipos);
    echo "<div class='table-responsive'><table class='table table-bordered'>";
    echo "<thead><tr><th>Técnico</th>";
    foreach ($tipos as $tipo) echo "<th>$tipo</th>";
    echo "<th>Total</th></tr></thead><tbody>";
    foreach ($datos as $tec => $ensayos) {
        echo "<tr><td>$tec</td>";
        $suma = 0;
        foreach ($tipos as $tipo) {
            $val = $ensayos[$tipo] ?? 0;
            echo "<td>$val</td>";
            $suma += $val;
        }
        echo "<td><strong>$suma</strong></td></tr>";
    }
    echo "</tbody></table></div>";
}

function mostrarAccordionTabla($datos, $intervalos, $titulo, $callback, $extra = null) {
    $titulo_id = preg_replace('/[^a-zA-Z0-9_]/', '_', $titulo);
    echo "<div class='accordion' id='accordion_$titulo_id'>";
    foreach ($intervalos as $nombre => $sql) {
        $id = preg_replace('/[^a-zA-Z0-9_]/', '_', "{$titulo_id}_{$nombre}");
        echo "<div class='accordion-item'>
                <h2 class='accordion-header' id='heading_$id'>
                  <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse_$id' aria-expanded='false' aria-controls='collapse_$id'>
                    $nombre
                  </button>
                </h2>
                <div id='collapse_$id' class='accordion-collapse collapse' aria-labelledby='heading_$id' data-bs-parent='#accordion_$titulo_id'>
                  <div class='accordion-body'>";
                  
        if (!empty($datos[$nombre])) {
            $callback($datos[$nombre], $titulo, $extra);
        } else {
            echo "<div class='text-muted'>No hay datos para mostrar en este intervalo.</div>";
        }

        echo "</div></div></div>";
    }
    echo "</div>";
}



echo "<ul class='nav nav-tabs'>
<li class='nav-item'><a class='nav-link active' data-bs-toggle='tab' href='#registradas'>Muestras Registradas</a></li>
<li class='nav-item'><a class='nav-link' data-bs-toggle='tab' href='#preparadas'>Preparadas</a></li>
<li class='nav-item'><a class='nav-link' data-bs-toggle='tab' href='#realizadas'>Realizadas</a></li>
<li class='nav-item'><a class='nav-link' data-bs-toggle='tab' href='#entregadas'>Entregadas</a></li>
<li class='nav-item'><a class='nav-link' data-bs-toggle='tab' href='#digitadas'>Digitadas</a></li>
</ul><div class='tab-content'>";

$secciones = [
  'registradas' => [$muestras_registradas, $nombres_ensayos],
  'preparadas'  => [$muestras_preparadas],
  'realizadas'  => [$muestras_realizadas],
  'entregadas'  => [$muestras_entregadas],
  'digitados'   => [$muestras_digitadas],
];



echo "<div class='tab-content'>";

foreach ($secciones as $id => $valores) {
    $datos = $valores[0];
    $extra = $valores[1] ?? null;

    echo "<div id='$id' class='tab-pane fade" . ($id == 'registradas' ? ' show active' : '') . "'>";

    // Definir qué función usar como callback para cada sección
    switch ($id) {
        case 'registradas':
            $callback = 'mostrarTablaPorTipoEnsayo';
            break;
        default:
            $callback = 'mostrarTablaMuestras';
            break;
    }

    // Título capitalizado
    $titulo = ucfirst($id);

    // Mostrar acordeón con callback y extra si aplica
    mostrarAccordionTabla($datos, $intervalos, "Ensayos $titulo", $callback, $extra);

    echo "</div>";
}

echo "</div>"; // Cierre de tab-content




echo "</div></div></section></main>";
include_once('../components/footer.php');
?>
