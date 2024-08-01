<?php
  $page_title = 'Gestor de Sumarios';
  $Sumarios = 'show';
  require_once('../config/load.php');

  // Recuperar datos de diferentes tablas
  $tables = ['atterberg_limit', 'brazilian', 'grain_size_coarse', 'grain_size_coarsethan', 'grain_size_fine', 'grain_size_general', 'los_angeles_abrasion_coarse_aggregate', 
  'los_angeles_abrasion_coarse_filter', 'moisture_constant_mass', 'moisture_microwave', 'moisture_oven', 'point_load', 'specific_gravity', 'specific_gravity_coarse', 
  'specific_gravity_fine', 'standard_proctor', 'unixial_compressive' ];
  $data = [];

  foreach ($tables as $table) {
      $data[$table] = find_all($table);
  }

  // Combinar los datos en un solo array
  $combined_data = [];
  foreach ($data as $table_data) {
      foreach ($table_data as $row) {
          $combined_data[] = $row;
      }
  }

    // Función para eliminar duplicados
    function remove_duplicates($data, $key) {
        $result = [];
        $seen = [];
  
        foreach ($data as $row) {
            if (!in_array($row[$key], $seen)) {
                $seen[] = $row[$key];
                $result[] = $row;
            }
        }
  
        return $result;
    }
  
    // Eliminar duplicados basados en 'Material_Type'
    $unique_data = remove_duplicates($combined_data, 'Material_Type');
?>

<?php page_require_level(3); ?>
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
                  <th scope="col">Material Type</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($unique_data as $row): ?>
                <tr>
                  <td><?php echo $row['Material_Type']; ?></td>
                  <td><a class="btn btn-primary" href="#"><i class="bi bi-eye"></i></a></td>
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