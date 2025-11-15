import { fetchData } from '../db-search/dbSearch.js';

/* fuera de uso por el momento
function sandCastle(i) {
    // Tomar todos los inputs tipo Collapsed
    const inputs = document.querySelectorAll('input[id^="Collapsed_"]');
    let totalSeconds = 0;

    inputs.forEach(input => {
        let value = input.value.trim();
        if (!value) return;

        // Reemplazar ":" por "." para unificar formato
        value = value.replace(/:/g, ".");

        // Dividir por puntos
        const parts = value.split(".");

        let minutes = parseInt(parts[0]) || 0;
        let seconds = parseInt(parts[1]) || 0;
        let milliseconds = parseInt(parts[2]) || 0;

        // Convertir todo a segundos
        totalSeconds += minutes * 60 + seconds + milliseconds / 100;
    });

    // Calcular minutos, segundos y milisegundos totales
    const totalMinutes = Math.floor(totalSeconds / 60);
    const remainingSeconds = Math.floor(totalSeconds % 60);
    const remainingMilliseconds = Math.round((totalSeconds - Math.floor(totalSeconds)) * 100);

    const formatted = `${totalMinutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}:${remainingMilliseconds.toString().padStart(2, '0')}`;

    const timeSet = document.getElementById("TimeSet");
    if (timeSet) timeSet.value = formatted;
}
fuera de uso por el momento */

function angle() {
    const FinalHeight = parseFloat(document.getElementById("FinalHeight").value);
    const degreesArray = [];

    for (let i = 1; i <= 5; i++) {
        const Radius = parseFloat(document.getElementById("Radius" + i).value);
        const Degrees = Math.atan(FinalHeight / Radius) * (180 / Math.PI);

        if (!isNaN(Degrees)) {
            degreesArray.push(Degrees);
        }

        document.getElementById("Angle" + i).value =
            isNaN(Degrees) || Degrees === 0 ? '' : Degrees.toFixed(2);
    }

    if (degreesArray.length > 0) {
        const sum = degreesArray.reduce((acc, val) => acc + val, 0);
        const average = sum / degreesArray.length;
        const result = average < 28 ? "Passed" : "Failed";
        document.getElementById("Average").value = average.toFixed(2);
        document.getElementById("testResult").value = result;
    }


}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form.row");
    if (form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", angle);
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("collapsedTable");
    const addButton = document.getElementById("addCollapsed");

    // 🔹 Contar cuántos Collapsed existen al cargar la página
    let collapsedCount = document.querySelectorAll('[id^="Collapsed_"]').length;

    // 🔹 Función para agregar evento a un input nuevo
    function attachSandCastleListener(input, index) {
        input.addEventListener("input", () => sandCastle(index));
    }

    // 🔹 Agregar el listener a todos los Collapsed existentes (no solo el primero)
    document.querySelectorAll('[id^="Collapsed_"]').forEach((input, i) => {
        attachSandCastleListener(input, i + 1);
    });

    // 🔹 Botón para agregar nuevas filas
    addButton.addEventListener("click", function () {
        collapsedCount++; // ahora empieza desde el número correcto

        // Determinar sufijo (1st, 2nd, 3rd, 4th, etc.)
        let suffix;
        if (collapsedCount === 1) suffix = "1st";
        else if (collapsedCount === 2) suffix = "2nd";
        else if (collapsedCount === 3) suffix = "3rd";
        else suffix = collapsedCount + "th";

        // Crear nueva fila
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <th scope="row">${suffix} Collapsed</th>
            <td><input type="text" style="border: none;" class="form-control" 
                name="Collapsed[]" id="Collapsed_${collapsedCount}"></td>
        `;

        // Insertar antes del "Time of Set"
        const timeSetRow = document.getElementById("timeSetRow");
        table.querySelector("tbody").insertBefore(newRow, timeSetRow);

        // Agregar evento de escucha al nuevo input
        const newInput = document.getElementById("Collapsed_" + collapsedCount);
        attachSandCastleListener(newInput, collapsedCount);
    });
});

document.querySelector('[name="search"]').addEventListener('click', () => {
    // Obtener valores de los inputs
    const sampleName = document.getElementById('SampleName').value;
    const sampleNumber = document.getElementById('SampleNumber').value;

    // Humedad natural
    fetchData('moisture_oven', { Sample_ID: sampleName, Sample_Number: sampleNumber }, { Moisture_Content_Porce: 'natMc' });

    // Humedad óptima
    fetchData('standard_proctor', { Sample_ID: sampleName, Sample_Number: sampleNumber }, { Optimun_MC_Porce: 'optimunMc' }
    );
});