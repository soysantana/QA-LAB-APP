import { calcularParametrosGranulometricos } from './gs-summary.js';
import { enviarImagenAlServidor } from '../export/export-chart.js';
import { UpdateGraph } from '../../charts/grain-size/gs-chart.js';
import { initSpecsSelect } from '../../js/grain-size/specs-gs.js';

function CoarseFilter() {

    const cumRetArray = [0];
    const PassArray = new Array(18).fill(null);

    const DrySoilTare = parseFloat(document.getElementById("DrySoilTare").value);
    const Tare = parseFloat(document.getElementById("Tare").value);
    const Washed = parseFloat(document.getElementById("Washed").value);
    const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);

    const DrySoil = DrySoilTare - Tare;
    const WashPan = DrySoil - Washed;

    let lastCumRet = 0;

    // Detectar primer índice con valor válido en WtRet
    let startIndex = 1;
    for (let i = 1; i <= 18; i++) {
        const val = parseFloat(document.getElementById("WtRet" + i).value);
        if (!isNaN(val)) {
            startIndex = i;
            break;
        }
    }

    // Limpiar campos anteriores a startIndex para que no muestren nada
    for (let i = 1; i < startIndex; i++) {
        document.getElementById("Ret" + i).value = "";
        document.getElementById("CumRet" + i).value = "";
        document.getElementById("Pass" + i).value = "";
        PassArray[i - 1] = null; // o NaN
    }

    // Calcular desde startIndex en adelante
    for (let i = startIndex; i <= 18; i++) {
        const WtRet = parseFloat(document.getElementById("WtRet" + i).value);

        let Ret = NaN;
        if (!isNaN(WtRet) && DrySoil > 0) {
            Ret = (WtRet / DrySoil) * 100;
        }

        let CumRet = NaN;
        if (!isNaN(Ret)) {
            CumRet = lastCumRet + Ret;
            lastCumRet = CumRet;
        } else {
            CumRet = lastCumRet;
        }
        cumRetArray.push(CumRet);

        const Pass = !isNaN(CumRet) ? 100 - CumRet : NaN;

        PassArray[i - 1] = Pass;

        document.getElementById("Ret" + i).value = isNaN(Ret) ? '' : Ret.toFixed(2);
        document.getElementById("CumRet" + i).value = isNaN(CumRet) ? '' : CumRet.toFixed(2);
        document.getElementById("Pass" + i).value = isNaN(Pass) ? '' : Pass.toFixed(2);
    }

    const PanRet = DrySoil > 0 ? (PanWtRen / DrySoil) * 100 : NaN;
    const TotalWtRet = PanWtRen + WashPan;
    const TotalRet = DrySoil > 0 ? (TotalWtRet / DrySoil) * 100 : NaN;
    const TotalCumRet = lastCumRet + TotalRet;
    const TotalPass = !isNaN(TotalCumRet) ? Math.abs(100 - TotalCumRet) : NaN;

    // Mostrar resultados generales
    document.getElementById("DrySoil").value = isNaN(DrySoil) ? '' : DrySoil.toFixed(2);
    document.getElementById("WashPan").value = isNaN(WashPan) ? '' : WashPan.toFixed(2);
    document.getElementById("PanRet").value = isNaN(PanRet) ? '' : PanRet.toFixed(2);
    document.getElementById("TotalWtRet").value = isNaN(TotalWtRet) ? '' : TotalWtRet.toFixed(2);
    document.getElementById("TotalRet").value = isNaN(TotalRet) ? '' : TotalRet.toFixed(2);
    document.getElementById("TotalCumRet").value = isNaN(TotalCumRet) ? '' : TotalCumRet.toFixed(2);
    document.getElementById("TotalPass").value = isNaN(TotalPass) ? '' : TotalPass.toFixed(2);

    // Reactivity Test Method FM13-006
    var total = 0;
    var count = 0;

    // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
    for (var i = 1; i <= 3; i++) {
        var elementId = "Particles" + i;
        var element = document.getElementById(elementId);

        if (element && !isNaN(parseFloat(element.value))) {
            // Convierte el contenido del input a un número y agrégalo al total
            total += parseFloat(element.value);
            count++;
        }
    }
    if (count >= 1) {
        var avgParticles = total / count;
        var reactionResult;
        var AcidResult;

        // Reaction Strength Result:
        if (avgParticles >= 30) {
            reactionResult = "Strong Reaction";
        } else if (avgParticles >= 16 && avgParticles <= 30) {
            reactionResult = "Moderate Reaction";
        } else if (avgParticles >= 1 && avgParticles <= 15) {
            reactionResult = "Weak Reaction";
        } else {
            reactionResult = "No Reaction";
        }
        // Acid Reactivity Test Result
        if (reactionResult === "No Reaction") {
            AcidResult = "Accepted";
        } else if (reactionResult === "Weak Reaction" || reactionResult === "Moderate Reaction") {
            AcidResult = "Accepted";
        } else {
            AcidResult = "Rejected";
        }

        document.getElementById("AcidResult").value = AcidResult;
        document.getElementById("ReactionResult").value = reactionResult;
        document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
    }

    // Sumary Parameter
    const datos = [
        [PassArray[17], 0.075, PassArray[16], 0.25],
        [PassArray[16], 0.25, PassArray[15], 0.30],
        [PassArray[15], 0.30, PassArray[14], 0.85],
        [PassArray[14], 0.85, PassArray[13], 1.18],
        [PassArray[13], 1.18, PassArray[12], 2.00],
        [PassArray[12], 2.00, PassArray[11], 4.75],
        [PassArray[11], 4.75, PassArray[10], 9.50],
        [PassArray[10], 9.50, PassArray[9], 12.70],
        [PassArray[9], 12.70, PassArray[8], 19.00],
        [PassArray[8], 19.00, PassArray[7], 25.00],
        [PassArray[7], 25.00, PassArray[6], 38.10],
        [PassArray[6], 38.10, PassArray[5], 50.80],
        [PassArray[5], 50.80, PassArray[4], 63.50],
        [PassArray[4], 63.50, PassArray[3], 76.20],
        [PassArray[3], 76.20, PassArray[2], 88.90],
        [PassArray[2], 88.90, PassArray[1], 101.6],
        [PassArray[1], 101.6, PassArray[0], 127],
        [PassArray[0], 127, 0.0, 0.0]
    ];

    calcularParametrosGranulometricos(datos);
    UpdateGraph();
}

document.addEventListener("DOMContentLoaded", () => {
    initSpecsSelect();
});

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form.row");
    if (form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", CoarseFilter);
        });
    }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
    el.addEventListener('click', () => {
        const tipo = el.dataset.exportar;
        enviarImagenAlServidor(tipo, ["GrainSizeChart"]);
    });
});