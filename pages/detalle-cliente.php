<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Reporte por Cliente: Solicitados vs Entregados</h1>
  </div>
  <section class="section">
    <div class="card">
      <div class="card-body pt-4">
        <table class="table table-bordered table-striped table-sm">
          <thead class="table-dark">
            <tr>
              <th>Cliente</th>
              <th>Año</th>
              <th>Mes</th>
              <th>Solicitados</th>
              <th>Entregados</th>
              <th>% Entrega</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "
              SELECT 
                r.Client,
                YEAR(r.Sample_Date) AS anio,
                MONTH(r.Sample_Date) AS mes,
                COUNT(*) AS solicitados,
                SUM(
                  EXISTS (
                    SELECT 1 FROM test_delivery d 
                    WHERE d.Sample_ID = r.Sample_ID 
                      AND d.Test_Type = r.Test_Type
                  )
                ) AS entregados
              FROM lab_test_requisition_form r
              GROUP BY r.Client, anio, mes
              ORDER BY anio DESC, mes, r.Client
            ";
            $result = $db->query($sql);
            while ($row = $result->fetch_assoc()):
              $pct = $row['solicitados'] > 0 ? round($row['entregados'] / $row['solicitados'] * 100, 1) : 0;
            ?>
              <tr>
                <td><?= $row['Client'] ?></td>
                <td><?= $row['anio'] ?></td>
                <td><?= str_pad($row['mes'], 2, '0', STR_PAD_LEFT) ?></td>
                <td><?= $row['solicitados'] ?></td>
                <td><?= $row['entregados'] ?></td>
                <td><?= $pct ?>%</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
