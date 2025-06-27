<?php
$page_title = 'Reporte Semanal de Laboratorio';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reporte Varios</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Reportes</li>
        <li class="breadcrumb-item active">Reporte Semanal</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="card">
      <div class="card-body pt-3">
        <h5 class="card-title">Resumen de la Semana</h5>

        <div class="row mb-3">
          <div class="col-md-3">
            <label for="fecha" class="form-label">Seleccionar fecha base:</label>
            <input type="date" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="cargarDatosSemana()">Cargar</button>
          </div>
        </div>

        <div class="row" id="tarjetas">
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Muestras Registradas</h6>
                <h3 id="total_muestras">0</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Ensayos Realizados</h6>
                <h3 id="total_ensayos">0</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Clientes Atendidos</h6>
                <h3 id="total_clientes">0</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div id="grafico_dias" style="height: 300px;"></div>
          </div>
          <div class="col-md-6">
            <div id="grafico_ensayos" style="height: 300px;"></div>
          </div>
        </div>

        <div class="mt-4">
          <h5>Observaciones / No Conformidades</h5>
          <ul id="lista_observaciones"></ul>
        </div>

      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script>
function cargarDatosSemana() {
  const fecha = document.getElementById('fecha').value;
  fetch('data_semana.php?fecha=' + fecha)
    .then(res => res.json())
    .then(data => {
      document.getElementById('total_muestras').textContent = data.totales.muestras;
      document.getElementById('total_ensayos').textContent = data.totales.ensayos;
      document.getElementById('total_clientes').textContent = data.totales.clientes;

      const diasChart = echarts.init(document.getElementById('grafico_dias'));
      diasChart.setOption({
        title: { text: 'Muestras y Ensayos por Día' },
        tooltip: { trigger: 'axis' },
        legend: { data: ['Muestras', 'Ensayos'] },
        xAxis: { type: 'category', data: data.por_dia.map(d => d.fecha) },
        yAxis: { type: 'value' },
        series: [
          { name: 'Muestras', type: 'bar', data: data.por_dia.map(d => d.muestras) },
          { name: 'Ensayos', type: 'bar', data: data.por_dia.map(d => d.ensayos) }
        ]
      });

      const ensayoChart = echarts.init(document.getElementById('grafico_ensayos'));
      ensayoChart.setOption({
        title: { text: 'Ensayos por Tipo' },
        tooltip: {},
        xAxis: { type: 'category', data: data.por_ensayo.map(e => e.tipo) },
        yAxis: { type: 'value' },
        series: [{ name: 'Cantidad', type: 'bar', data: data.por_ensayo.map(e => e.cantidad) }]
      });

      const obsContainer = document.getElementById('lista_observaciones');
      obsContainer.innerHTML = '';
      if (data.observaciones.length === 0) {
        obsContainer.innerHTML = '<li>No se registraron observaciones.</li>';
      } else {
        data.observaciones.forEach(o => {
          const li = document.createElement('li');
          li.textContent = `${o.sample}: ${o.comentario}`;
          obsContainer.appendChild(li);
        });
      }
    });
}

window.onload = cargarDatosSemana;
</script>

<?php include_once('../components/footer.php'); ?>