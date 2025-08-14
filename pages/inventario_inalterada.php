<?php
$page_title = 'Inventario de Muestras';
require_once('../config/load.php');
page_require_level(3);
?>

<?php include_once('../components/header.php'); ?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Inventarios</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../pages/home.php">Home</a></li>
      <li class="breadcrumb-item">Pages</li>
      <li class="breadcrumb-item active" ><a href="../components/menu_inventarios.php">Inventarios</a></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-box-seam"></i> Inventario de Muestras</h1> 
  </div>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">
        <div class="mb-3 d-flex justify-content-end"> 
  <a href="../pages/sumary/export_inventario_muestras.php" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Exportar Inventario
  </a>
</div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered datatable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nombre de Muestra</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Prof. Desde</th>
                <th>Hasta</th>
                <th>Fecha</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fecha_limite = date('Y-m-d', strtotime('-12 month'));

              $query = "SELECT r.*, i.sample_length, i.sample_weight, i.store_in, i.comment
    FROM lab_test_requisition_form r
    LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
    WHERE (
        r.Sample_Type IN ('Shelby', 'Mazier', 'Lexan', 'Ring', 'Rock') 
        OR FIND_IN_SET('Envio', r.Test_Type)
    )
    AND r.Sample_Date >= '{$fecha_limite}'
    ORDER BY r.Sample_Date DESC";


              $result = $db->query($query);
              $i = 1;
              $modals = "";
              while ($row = $db->fetch_assoc($result)) :
              ?>
                <tr>
                  <td><?= $i++; ?></td>
                  <td><?= htmlspecialchars($row['Sample_ID']) ?></td>
                  <td><?= htmlspecialchars($row['Sample_Number']) ?></td>
                  <td><?= htmlspecialchars($row['Sample_Type']) ?></td>
                  <td><?= htmlspecialchars($row['Depth_From']) ?></td>
                  <td><?= htmlspecialchars($row['Depth_To']) ?></td>
                  <td><?= htmlspecialchars($row['Sample_Date']) ?></td>
                  <td>
                    <?php if (!empty($row['sample_length']) || !empty($row['sample_weight']) || !empty($row['store_in']) || !empty($row['comment'])): ?>
                      <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">
                        <i class="bi bi-check-circle"></i> Editado
                      </button>
                    <?php else: ?>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">
                        <i class="bi bi-pencil"></i> Editar
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php
                ob_start();
                ?>
                <div class="modal fade" id="modalEditar<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $row['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="../database/inventario/guardar_inalterada.php" method="POST">
                        <input type="hidden" name="requisition_id" value="<?= $row['id'] ?>">

                        <div class="modal-header">
                          <h5 class="modal-title" id="modalEditarLabel<?= $row['id'] ?>">Editar Información de Muestra</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                          <div class="form-group mb-2">
                            <label for="Sample_ID">Nombre de Muestra</label>
                            <input type="text" class="form-control" name="Sample_ID" value="<?= $row['Sample_ID'] ?>" readonly>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Sample_Number">Número de Muestra</label>
                            <input type="text" class="form-control" name="Sample_Number" value="<?= $row['Sample_Number'] ?>" readonly>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Sample_Type">Tipo de Muestra</label>
                            <input type="text" class="form-control" name="Sample_Type" value="<?= $row['Sample_Type'] ?>" readonly>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Depth_From">Profundidad Desde (m)</label>
                            <input type="text" class="form-control" name="Depth_From" value="<?= $row['Depth_From'] ?>" readonly>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Depth_To">Profundidad Hasta (m)</label>
                            <input type="text" class="form-control" name="Depth_To" value="<?= $row['Depth_To'] ?>" readonly>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Sample_Length">Longitud (m)</label>
                            <input type="number" step="any" class="form-control" name="Sample_Length" value="<?= htmlspecialchars($row['sample_length']) ?>">
                          </div>
                          <div class="form-group mb-2">
                            <label for="Sample_Weight">Peso (kg)</label>
                            <input type="number" step="any" class="form-control" name="Sample_Weight" value="<?= htmlspecialchars($row['sample_weight']) ?>">
                          </div>
                          <div class="form-group mb-2">
                            <label for="Store_In">Ubicación</label>
                            <select class="form-control" name="Store_In">
                              <option value="Stored_PVLab" <?= $row['store_in'] === 'Stored_PVLab' ? 'selected' : '' ?>>Almacenado en PVLab</option>
                              <option value="Sended_To" <?= $row['store_in'] === 'Sended_To' ? 'selected' : '' ?>>Muestra Enviada</option>
                            </select>
                          </div>
                          <div class="form-group mb-2">
                            <label for="Comment">Comentario</label>
                            <input type="text" class="form-control" name="Comment" value="<?= htmlspecialchars($row['Comment']) ?>">
                          </div>
                          <input type="hidden" name="Sample_Date" value="<?= $row['Sample_Date'] ?>">
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php
                $modals .= ob_get_clean();
              endwhile;
              ?>
            </tbody>
          </table>
          <?= $modals ?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>