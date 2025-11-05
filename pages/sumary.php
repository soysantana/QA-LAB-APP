<?php
$page_title = 'Gestor de Sumarios';
$Sumarios = 'show';
require_once('../config/load.php');

// Recuperar datos de diferentes tablas
$tables = [
  'atterberg_limit',
  'brazilian',
  'grain_size_coarse',
  'grain_size_fine',
  'grain_size_full',
  'grain_size_general',
  'grain_size_lpf',
  'grain_size_upstream_transition_fill',
  'hydrometer',
  'los_angeles_abrasion_large',
  'los_angeles_abrasion_small',
  'moisture_constant_mass',
  'moisture_microwave',
  'moisture_oven',
  'moisture_scale',
  'pinhole_test',
  'point_load',
  'reactivity',
  'soundness',
  'specific_gravity',
  'specific_gravity_coarse',
  'specific_gravity_fine',
  'standard_proctor',
  'unixial_compressive'
];

$data = [];

// Recuperar y combinar los datos de todas las tablas
foreach ($tables as $table) {
  $table_data = find_all($table);
  $data = array_merge($data, $table_data);
}

// Función para eliminar duplicados basados en una clave específica
function remove_duplicates($data, $key)
{
  $result = [];
  $seen = [];

  foreach ($data as $row) {
    if (!isset($seen[$row[$key]])) {
      $seen[$row[$key]] = true;
      $result[] = $row;
    }
  }

  return $result;
}

// Eliminar duplicados basados en 'Material_Type'
$unique_data = remove_duplicates($data, 'Material_Type');
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Gestor de Sumarios</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Paginas</li>
        <li class="breadcrumb-item active">Sumarios</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Information</h5>

            <!-- Table with Sumary -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th scope="col">Cliente</th>
                  <th scope="col">Material Type</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($unique_data as $row): ?>
                  <tr>
                    <td><?php echo $row['Client']; ?></td>
                    <td><?php echo $row['Material_Type']; ?></td>
                    <td>
                      <a class="btn btn-primary" href="../sumary/sumary-excel-client.php?Material_Type=<?php echo urlencode($row['Material_Type']); ?>&Client=<?php echo urlencode($row['Client']); ?>" target="_blank">
                        <i class="bi bi-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <!-- End Table with Sumary -->
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<!-- End #main -->

<?php include_once('../components/footer.php');  ?>