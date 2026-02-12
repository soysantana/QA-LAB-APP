import { calcularParametrosGranulometricos } from './gs-summary.js';
import { enviarImagenAlServidor } from '../export/export-chart.js';
import { UpdateGraph } from '../../charts/grain-size/gs-chart.js';

function GrainSize() {
    const cumRetArray = [0];
    const PassArray = new Array(22).fill(null);

    const DrySoilTare = parseFloat(document.getElementById("DrySoilTare").value);
    const Tare = parseFloat(document.getElementById("Tare").value);
    const Washed = parseFloat(document.getElementById("Washed").value);
    const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);

    const DrySoil = DrySoilTare - Tare;
    const WashPan = DrySoil - Washed;

    let lastCumRet = 0;

    // Detectar primer índice con valor válido en WtRet
    let startIndex = 1;
    for (let i = 1; i <= 22; i++) {
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
    for (let i = startIndex; i <= 22; i++) {
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

    // Sumary Parameter
    const datos = [
        [PassArray[21], 0.075, PassArray[20], 0.106],
        [PassArray[20], 0.106, PassArray[19], 0.15],
        [PassArray[19], 0.15, PassArray[18], 0.25],
        [PassArray[18], 0.25, PassArray[17], 0.3],
        [PassArray[17], 0.3, PassArray[16], 0.85],
        [PassArray[16], 0.85, PassArray[15], 1.18],
        [PassArray[15], 1.18, PassArray[14], 2],
        [PassArray[14], 2, PassArray[13], 4.75],
        [PassArray[13], 4.75, PassArray[12], 9.5],
        [PassArray[12], 9.5, PassArray[11], 12.5],
        [PassArray[11], 12.5, PassArray[10], 19],
        [PassArray[10], 19, PassArray[9], 25],
        [PassArray[9], 25, PassArray[8], 38.1],
        [PassArray[8], 38.1, PassArray[7], 50.8],
        [PassArray[7], 50.8, PassArray[6], 63.5],
        [PassArray[6], 63.5, PassArray[5], 75],
        [PassArray[5], 75, PassArray[4], 88.9],
        [PassArray[4], 88.9, PassArray[3], 101.6],
        [PassArray[3], 101.6, PassArray[2], 127],
        [PassArray[2], 127, PassArray[1], 152.4],
        [PassArray[1], 152.4, PassArray[0], 200],
        [PassArray[0], 200, 0, 0.0]
    ];

    calcularParametrosGranulometricos(datos);
    UpdateGraph()
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form.row");
    if (form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", GrainSize);
        });
    }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
    el.addEventListener('click', () => {
        const tipo = el.dataset.exportar;
        enviarImagenAlServidor(tipo, ["GrainSizeChart"]);
    });
});