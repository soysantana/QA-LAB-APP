<?php
$page_title = "Expediente Técnico";
require_once "../config/load.php";
page_require_level(2);
include_once('../components/header.php');
?>

<style>
    .card-select {
        cursor: pointer;
        padding: 25px;
        border-radius: 15px;
        transition: 0.2s;
        text-align: center;
        font-size: 1.1rem;
        font-weight: 600;
        background: #ffffff;
        border: 1px solid #ddd;
    }
    .card-select:hover {
        transform: scale(1.04);
        background: #f0f0f0;
    }

    .chip {
        padding: 10px 20px;
        margin: 5px;
        border-radius: 20px;
        background: #e8e8e8;
        cursor: pointer;
        font-weight: 600;
        display: inline-block;
    }
    .chip:hover {
        background: #cfcfcf;
    }

    .sample-item {
        border: 1px solid #ddd;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: 0.2s;
    }
    .sample-item:hover {
        background: #e9f3ff;
    }
</style>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Expediente Técnico</h1>
        <p class="text-muted">Selecciona Cliente → Estructura → Muestra</p>
    </div>

    <!-- NIVEL 1: CLIENTE -->
    <section id="nivel1">
        <h4>1. Selecciona el Cliente</h4>
        <div class="row mt-3" id="clientesContainer"></div>
    </section>

    <!-- NIVEL 2: ESTRUCTURA -->
    <section id="nivel2" style="display:none;">
        <h4>2. Selecciona la Estructura</h4>
        <div id="estructurasContainer" class="mt-3"></div>
        <button onclick="volver1()" class="btn btn-secondary mt-4">← Volver</button>
    </section>

    <!-- NIVEL 3: MUESTRA -->
    <section id="nivel3" style="display:none;">
        <h4>3. Selecciona la Muestra</h4>

        <input type="text" id="buscarMuestra" class="form-control form-control-lg mt-2"
               placeholder="Escribe 2–3 caracteres para filtrar muestras...">

        <div id="muestrasContainer" class="mt-3"></div>

        <button onclick="volver2()" class="btn btn-secondary mt-4">← Volver</button>
    </section>

</main>

<script src="../js/expediente.js"></script>

<?php include_once('../components/footer.php'); ?>
