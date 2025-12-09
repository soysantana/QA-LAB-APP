// ==========================
// 1) CARGAR CLIENTES
// ==========================
async function cargarClientes() {
    let res = await fetch("../api/expediente_api.php?accion=clientes");
    let data = await res.json();

    const cont = document.getElementById("clientesContainer");

    cont.innerHTML = data.map(c =>
        `
        <div class='col-md-4'>
            <div class='card-select shadow' onclick="seleccionarCliente('${c.Client}')">
                ${c.Client}
            </div>
        </div>
        `
    ).join("");
}

cargarClientes();

let clienteSeleccionado = "";
let estructuraSeleccionada = "";

// ==========================
// 2) SELECCIONAR CLIENTE
// ==========================
async function seleccionarCliente(cliente) {
    clienteSeleccionado = cliente;

    document.getElementById("nivel1").style.display = "none";
    document.getElementById("nivel2").style.display = "block";

    let res = await fetch(`../api/expediente_api.php?accion=estructuras&cliente=${cliente}`);
    let data = await res.json();

    const cont = document.getElementById("estructurasContainer");
    cont.innerHTML = data.map(e =>
        `<span class='chip' onclick="seleccionarEstructura('${e.Structure}')">${e.Structure}</span>`
    ).join("");
}

function volver1() {
    document.getElementById("nivel2").style.display = "none";
    document.getElementById("nivel1").style.display = "block";
}

// ==========================
// 3) SELECCIONAR ESTRUCTURA
// ==========================
function seleccionarEstructura(estructura) {
    estructuraSeleccionada = estructura;

    document.getElementById("nivel2").style.display = "none";
    document.getElementById("nivel3").style.display = "block";

    document.getElementById("buscarMuestra").value = "";
    cargarMuestras("");
}

function volver2() {
    document.getElementById("nivel3").style.display = "none";
    document.getElementById("nivel2").style.display = "block";
}

// ==========================
// 4) MOSTRAR MUESTRAS FILTRADAS
// ==========================
async function cargarMuestras(q) {
    let res = await fetch(
        `../api/expediente_api.php?accion=muestras&cliente=${clienteSeleccionado}&estructura=${estructuraSeleccionada}&q=${q}`
    );
    let data = await res.json();

    const cont = document.getElementById("muestrasContainer");

    cont.innerHTML = data.map(m =>
        `
        <div class="sample-item" onclick="verExpediente('${m.Sample_ID}', '${m.Sample_Number}')">
            <b>${m.Sample_ID}</b><br>
            <span class="text-muted">#${m.Sample_Number}</span><br>
            <span>${m.Material_Type}</span>
        </div>
        `
    ).join("");
}

document.getElementById("buscarMuestra").addEventListener("input", e => {
    cargarMuestras(e.target.value);
});

// ==========================
// 5) ABRIR EXPEDIENTE
// ==========================
function verExpediente(sample_id, sample_number) {
    window.location.href = `../pages/expediente_detalle.php?sample=${sample_id}&num=${sample_number}`;
}
