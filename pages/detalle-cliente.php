<?php
$page_title = 'Detalle del Cliente';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Detalle del Cliente</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item active">Detalle del Cliente</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card mb-4">
      <div class="card-body">
        <form id="filtros" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Año</label>
            <select name="anio" class="form-select" required>
              <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Mes</label>
            <select name="mes" class="form-select" required>
              <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Ensayos por Cliente</h5>
            <div id="chartEnsayos" style="height: 400px;"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Muestras por Cliente</h5>
            <div id="chartMuestras" style="height: 400px;"></div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Muestras Registradas Esta Semana</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th>ID Muestra</th>
                    <th>Número</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody id="tablaSemana"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Solicitados vs Entregados (Mensual)</h5>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-sm">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th>Solicitados</th>
                    <th>Entregados</th>
                    <th>Progreso</th>
                  </tr>
                </thead>
                <tbody id="tablaProgreso"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 text-end">
        <button class="btn btn-danger" onclick="window.open('../pdf/generar_reporte_cliente.php?anio=' + currentAnio + '&mes=' + currentMes, '_blank')">
          <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
        </button>
      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/echarts"></script>
<script>
  let currentAnio = '',
    currentMes = '';

  document.getElementById('filtros').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = new FormData(this);
    currentAnio = form.get('anio');
    currentMes = form.get('mes');

    fetch('../php/detalle_cliente_data.php', {
        method: 'POST',
        body: form
      })
      .then(res => res.json())
      .then(data => {
        const chartEnsayos = echarts.init(document.getElementById('chartEnsayos'));
        const chartMuestras = echarts.init(document.getElementById('chartMuestras'));

        chartEnsayos.setOption({
          title: {
            text: 'Ensayos por Cliente',
            left: 'center'
          },
          tooltip: {
            trigger: 'item'
          },
          series: [{
            type: 'pie',
            radius: '60%',
            data: data.grafico.map(i => ({
              name: i.cliente,
              value: i.ensayos
            }))
          }]
        });

        chartMuestras.setOption({
          title: {
            text: 'Muestras por Cliente',
            left: 'center'
          },
          tooltip: {
            trigger: 'item'
          },
          series: [{
            type: 'pie',
            radius: '60%',
            data: data.grafico.map(i => ({
              name: i.cliente,
              value: i.muestras
            }))
          }]
        });

        const tbody = document.getElementById('tablaSemana');
        tbody.innerHTML = '';
        data.muestras_semana.forEach(row => {
          tbody.innerHTML += `<tr><td>${row.Client}</td><td>${row.Sample_ID}</td><td>${row.Sample_Number}</td><td>${row.Sample_Date}</td></tr>`;
        });

        const tprogreso = document.getElementById('tablaProgreso');
        tprogreso.innerHTML = '';
        data.progreso.forEach(item => {
          tprogreso.innerHTML += `
          <tr>
            <td>${item.Client}</td>
            <td>${item.Solicitados}</td>
            <td>${item.Entregados}</td>
            <td>
              <div class='progress'>
                <div class='progress-bar bg-success' role='progressbar' style='width: ${item.Porcentaje}%;'>${item.Porcentaje}%</div>
              </div>
            </td>
          </tr>
        `;
        });
      });
  });
</script>

<?php include_once('../components/footer.php'); ?>