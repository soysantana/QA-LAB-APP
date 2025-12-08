<?php
$page_title = 'Muestras en Repetición';
$tracking_show = 'show';
$class_tracking = ' ';
$repeat = 'active';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');

// --- Obtener muestras ---
$reviewed_check = "(SELECT 1 FROM test_reviewed trw 
                    WHERE trw.Sample_ID = p.Sample_ID 
                    AND trw.Sample_Number = p.Sample_Number 
                    AND trw.Test_Type = p.Test_Type 
                    AND trw.Signed = 1)";

$Search = find_by_sql("
    SELECT *
    FROM test_repeat p 
    WHERE NOT EXISTS $reviewed_check
    ORDER BY Start_Date DESC
");
?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Muestras en Repetición</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Formularios</li>
      <li class="breadcrumb-item active">Repetición</li>
    </ol>
  </nav>
</div>

<!-- ============================== -->
<!-- CARDS RESUMEN (ESTILO 2)      -->
<!-- ============================== -->
<div class="row mb-3">

  <div class="col-md-4">
    <div class="card shadow-sm border-0" style="border-left: 4px solid #dc2626;">
      <div class="card-body">
        <h6 class="text-muted mb-1">Total en repetición</h6>
        <h2 class="fw-bold text-danger"><?= count($Search); ?></h2>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-0" style="border-left: 4px solid #f59e0b;">
      <div class="card-body">
        <h6 class="text-muted mb-1">Promedio días</h6>
        <h2 class="fw-bold text-warning">
          <?php
            if (count($Search) > 0) {
                $sumDays = 0;
                foreach ($Search as $item) {
                    $sumDays += floor((time() - strtotime($item['Start_Date'])) / 86400);
                }
                echo round($sumDays / count($Search), 1);
            } else {
                echo "0";
            }
          ?>
        </h2>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-0" style="border-left: 4px solid #2563eb;">
      <div class="card-body">
        <h6 class="text-muted mb-1">Ensayos distintos</h6>
        <h2 class="fw-bold text-primary">
          <?php
            $types = array_unique(array_column($Search, 'Test_Type'));
            echo count($types);
          ?>
        </h2>
      </div>
    </div>
  </div>

</div>

<!-- ============================== -->
<!-- TABLA MIX (1 + 3)             -->
<!-- ============================== -->
<div class="card shadow-sm">
  <div class="card-body">

    <h5 class="card-title">Listado de Muestras en Repetición</h5>

    <table class="table table-hover align-middle table-bordered" id="repeatTable">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Muestra</th>
          <th>Número</th>
          <th>Ensayo</th>
          <th>Registrado</th>
          <th>Fecha</th>
          <th>Urgencia</th>
          <th>Comentario</th>
          <th class="text-center">Reprocesar</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($Search as $i => $item): ?>
          <?php 
            $days = floor((time() - strtotime($item['Start_Date'])) / 86400);

            // Indicadores tipo ESTILO 3
            if ($days >= 5) $urg = "badge bg-danger";
            elseif ($days >= 3) $urg = "badge bg-warning text-dark";
            else $urg = "badge bg-success";
          ?>

          <tr>
            <td><?= $i + 1; ?></td>

            <td><strong><?= htmlspecialchars($item['Sample_ID']); ?></strong></td>

            <td><?= htmlspecialchars($item['Sample_Number']); ?></td>

            <td>
              <span class="badge bg-info text-dark">
                <?= htmlspecialchars($item['Test_Type']); ?>
              </span>
            </td>

            <td><?= htmlspecialchars($item['Register_By']); ?></td>

            <td><?= date('Y-m-d', strtotime($item['Start_Date'])); ?></td>

            <td><span class="<?= $urg ?>"><?= $days ?> días</span></td>

            <td>
              <?php
                $comment = $item['Comment'] ?? '';
                $short = strlen($comment) > 25 ? substr($comment, 0, 25) . "..." : $comment;
              ?>

              <span 
                class="badge bg-secondary"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="<?= htmlspecialchars($comment) ?>"
              >
                <?= htmlspecialchars($short) ?>
              </span>
            </td>

            <td class="text-center">
              <form method="POST" action="test-repeat.php">
                <input type="hidden" name="Sample_ID" value="<?= htmlspecialchars($item['Sample_ID']); ?>">
                <input type="hidden" name="Sample_Number" value="<?= htmlspecialchars($item['Sample_Number']); ?>">
                <input type="hidden" name="Test_Type" value="<?= htmlspecialchars($item['Test_Type']); ?>">
                <input type="hidden" name="Register_By" value="<?= htmlspecialchars($item['Register_By']); ?>">
                <input type="hidden" name="Start_Date" value="<?= htmlspecialchars($item['Start_Date']); ?>">
                <input type="hidden" name="Comment" value="<?= htmlspecialchars($comment); ?>">

                <button type="submit" 
                        name="send-delivery" 
                        class="btn btn-primary btn-sm shadow-sm">
                  <i class="bi bi-arrow-clockwise"></i>
                </button>
              </form>
            </td>
          </tr>

        <?php endforeach; ?>
      </tbody>

    </table>

  </div>
</div>

</main>

<script>
  // activar tooltips
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  [...tooltipTriggerList].map(t => new bootstrap.Tooltip(t));
</script>

<?php include_once('../components/footer.php'); ?>
