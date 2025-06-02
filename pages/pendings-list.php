<?php
$page_title = 'Lista de Pendientes';
$Pending_List = 'show';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Lista de Pendientes</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Paginas</li>
        <li class="breadcrumb-item active">Lista de Pendientes</li>
      </ol>
    </nav>
  </div>

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">
      <form class="row">
        <div class="col-lg-9">
          <div class="card">
            <div class="card-body">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Muestra</th>
                    <th>Numero</th>
                    <th>Tipo de prueba</th>
                    <th>Fecha de muestra</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  function normalize($v)
                  {
                    return strtoupper(trim($v));
                  }

                  $requisitions = find_all("lab_test_requisition_form");
                  $tables_to_check = [
                    'test_preparation',
                    'test_delivery',
                    'test_realization',
                    'test_repeat',
                    'test_review',
                    'test_reviewed'
                  ];

                  $indexed_status = [];

                  // Cargar todas las tablas de seguimiento
                  foreach ($tables_to_check as $table) {
                    $data = find_all($table);
                    foreach ($data as $row) {
                      $key = normalize($row['Sample_Name']) . "|" . normalize($row['Sample_Number']) . "|" . normalize($row['Test_Type']);
                      $indexed_status[$key] = true;
                    }
                  }

                  $testTypes = [];

                  foreach ($requisitions as $requisition) {
                    if (empty($requisition['Test_Type'])) continue;

                    $sample_id = normalize($requisition['Sample_ID']);
                    $sample_num = normalize($requisition['Sample_Number']);
                    $date = $requisition['Sample_Date'];

                    // Separar los tipos de ensayo por comas y recorrer cada uno
                    $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));

                    foreach ($testTypesArray as $test_type) {
                      $test_type = normalize($test_type);
                      $key = $sample_id . "|" . $sample_num . "|" . $test_type;

                      if (!isset($indexed_status[$key])) {
                        $testTypes[] = [
                          'Sample_ID' => $requisition['Sample_ID'],
                          'Sample_Number' => $requisition['Sample_Number'],
                          'Test_Type' => $test_type,
                          'Sample_Date' => $date
                        ];
                      }
                    }
                  }


                  usort($testTypes, fn($a, $b) => strcmp($a['Test_Type'], $b['Test_Type']));

                  foreach ($testTypes as $index => $sample): ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_ID']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_Number']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Test_Type']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_Date']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Conteo</h5>
              <ul class="list-group">
                <?php
                $typeCount = [];
                $columnaTipo = [];

                foreach ($requisitions as $req) {
                  if (!empty($req['Test_Type'])) {
                    $testTypesArray = array_map('trim', explode(',', $req['Test_Type']));
                    foreach ($testTypesArray as $testType) {
                      $columnaTipo[$testType] = 'Test_Type'; // solo hay una columna ahora
                    }
                  }
                }

                foreach ($testTypes as $s) {
                  $t = $s['Test_Type'];
                  $typeCount[$t] = ($typeCount[$t] ?? 0) + 1;
                }

                foreach ($typeCount as $t => $count): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <code><?php echo htmlspecialchars($t); ?></code>
                      <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                    </div>
                    <?php if (isset($columnaTipo[$t])): ?>
                      <a href="../pdf/pendings.php?type=<?php echo urlencode($t); ?>"
                        class="btn btn-secondary btn-sm ms-2" title="Generar PDF"><i class="bi bi-printer"></i></a>
                    <?php else: ?>
                      <span class="badge bg-danger">Err</span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>


      </form>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>