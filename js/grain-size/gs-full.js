import { calcularParametrosGranulometricos } from '../gs-summary.js';
import { clasificarSueloExtra } from '../gs-classification.js';
import { UpdateGraph } from '../../libs/graph/Grain-Size-Full.js';
import { enviarImagenAlServidor } from '../export/export-chart.js';

let totalHumedo = 0;
let totalSecoSucio = 0;
let totalMore3 = 0;
let totalLess3 = 0;

function TRF() {
    const screenKeys = [
        "screen40",
        "screen30",
        "screen20",
        "screen13",
        "screen12",
        "screen10",
        "screen8",
        "screen6",
        "screen4",
        "screen3",
        "screen2",
        "screen1p5",
        "screen1",
        "screen3p4",
        "screen1p2",
        "screen3p8",
        "screenNo4",
        "screenNo20",
        "screenNo200",
        "screenPan"
    ];

    // Inicializa un objeto para acumular los totales
    const totals = {};
    screenKeys.forEach(key => (totals[key] = 0)); // Inicializa cada clave con 0

    // Itera sobre cada set de valores (del 1 al 10)
    for (let i = 1; i <= 10; i++) {
        screenKeys.forEach(key => {
            const element = document.getElementById(`${key}_${i}`);
            if (element) {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    totals[key] += value; // Suma el valor al total correspondiente
                }
            }
        });
    }

    // Asigna los totales formateados a los inputs correspondientes (sTotal_1, sTotal_2, etc.)
    screenKeys.forEach((key, index) => {
        const totalInput = document.getElementById(`sTotal_${index + 1}`); // Mapear a sTotal_1, sTotal_2, etc.
        if (totalInput) {
            totalInput.value = totals[key].toFixed(1);
        }
    });

}

function calcularTotales() {
    // Calcular total de WtPhumedo (1 al 55)
    totalHumedo = 0;
    for (let i = 1; i <= 55; i++) {
        const el = document.getElementById(`WtPhumedo_${i}`);
        if (el) {
            const val = parseFloat(el.value);
            if (!isNaN(val)) totalHumedo += val;
        }
    }
    const inputHumedo = document.getElementById("TDMPHumedo");
    const inputLess3Ex = document.getElementById("Less3Ex");
    if (inputHumedo) inputHumedo.value = totalHumedo.toLocaleString("en-US");
    if (inputLess3Ex) inputLess3Ex.value = totalHumedo.toLocaleString("en-US");

    // Calcular total de WtReSecoSucio (1 al 8)
    totalSecoSucio = 0;
    for (let i = 1; i <= 8; i++) {
        const el = document.getElementById(`WtReSecoSucio_${i}`);
        if (el) {
            const val = parseFloat(el.value);
            if (!isNaN(val)) totalSecoSucio += val;
        }
    }
    const inputSecoSucio = document.getElementById("TDMRSecoSucio");
    if (inputSecoSucio) inputSecoSucio.value = totalSecoSucio.toLocaleString("en-US");

    // Calcular total de sTotal (1 al 10)
    totalMore3 = 0;
    for (let i = 1; i <= 10; i++) {
        const el = document.getElementById(`sTotal_${i}`);
        if (el) {
            const val = parseFloat(el.value.replace(/,/g, '')); // Elimina comas
            if (!isNaN(val)) totalMore3 += val;
        }
    }
    const inputMore3 = document.getElementById("More3Ex");
    if (inputMore3) inputMore3.value = totalMore3.toLocaleString("en-US");

    // Calcular total de sTotal (11 al 20)
    totalLess3 = 0;
    for (let i = 11; i <= 20; i++) {
        const el = document.getElementById(`sTotal_${i}`);
        if (el) {
            const val = parseFloat(el.value.replace(/,/g, '')); // Elimina comas
            if (!isNaN(val)) totalLess3 += val;
        }
    }
}

function moisture() {
    const MoisturePercetArray = [];
    for (let i = 1; i <= 4; i++) {
        const WetSoil = parseFloat(document.getElementById("WetSoil" + i).value);
        const WetDry = parseFloat(document.getElementById("WetDry" + i).value);
        const TareMC = parseFloat(document.getElementById("TareMC" + i).value);

        const WetWater = WetSoil - WetDry;
        const WtDrySoil = WetDry - TareMC;
        const MoisturePercet = WetWater / WtDrySoil * 100;

        if (!isNaN(MoisturePercet)) {
            MoisturePercetArray.push(MoisturePercet);
        }

        document.getElementById("WetWater" + i).value = WetWater.toFixed(1);
        document.getElementById("WtDrySoil" + i).value = WtDrySoil.toFixed(1);
        document.getElementById("MoisturePercet" + i).value = MoisturePercet.toFixed(2);
    }

    const promedio = average(MoisturePercetArray);
    const CorrectionMC = totalHumedo / (1 + (promedio / 100));
    const TotalPesoSecoSucio = totalMore3 + CorrectionMC;

    const inputTotalPesoSecoSucio = document.getElementById("TotalPesoSecoSucio");
    if (inputTotalPesoSecoSucio) inputTotalPesoSecoSucio.value = TotalPesoSecoSucio.toLocaleString("en-US");

    document.getElementById("MoistureContentAvg").value = promedio.toFixed(2);
    document.getElementById("TotalDryWtSampleLess3g").value = CorrectionMC.toFixed(1);

    //Grain Size Reducida
    const inputPesoSecoSucio = document.getElementById("PesoSecoSucio");
    if (inputPesoSecoSucio) inputPesoSecoSucio.value = totalSecoSucio.toLocaleString("en-US");
    const inputPesoLavado = document.getElementById("PesoLavado");
    if (inputPesoLavado) inputPesoLavado.value = totalLess3.toLocaleString("en-US");

    const PanLavado = totalSecoSucio - totalLess3;

    const inputPanLavado = document.getElementById("PanLavado");
    if (inputPanLavado) inputPanLavado.value = PanLavado.toLocaleString("en-US");

    // Factor de conversion
    const FactorConversion = (totalSecoSucio / CorrectionMC) * 100;
    document.getElementById("ConvertionFactor").value = FactorConversion.toFixed(2);


    //GS Combinada & Factor aplicado
    const cumRetArray = [0];
    const WtRetExtendidaArray = [];
    const FactorAplicadoArray = [];
    const RetArray = [];
    const RetCorrectionArray = [];
    const PassArray = [];
    let WtRet1x10 = 0;
    let FactorAplicadoTotal = 0;

    for (let i = 1; i <= 20; i++) {
        let WtRetExtendida = parseFloat(document.getElementById("sTotal_" + i).value.replace(/,/g, ""));
        WtRetExtendidaArray.push(WtRetExtendida);

        if (i >= 1 && i <= 10) {
            WtRet1x10 += WtRetExtendida;
            const Ret = (WtRetExtendida / TotalPesoSecoSucio) * 100;
            RetArray.push(Ret);
            const CumRet = cumRetArray[i - 1] + Ret; cumRetArray.push(CumRet);
            const Pass = 100 - CumRet;
            PassArray.push(Pass);
        }

        if (i >= 11 && i <= 20) {
            const FactorAplicado = (WtRetExtendida * 100) / FactorConversion;
            FactorAplicadoTotal += FactorAplicado;
            FactorAplicadoArray.push(FactorAplicado);
            const RetCorrection = (FactorAplicado / TotalPesoSecoSucio) * 100;
            RetCorrectionArray.push(RetCorrection);
            const CumRet = cumRetArray[i - 1] + RetCorrection; cumRetArray.push(CumRet);
            const Pass = 100 - CumRet;
            PassArray.push(Pass);
        }

    }

    const TotalPesoLavado = WtRet1x10 + FactorAplicadoTotal;
    const PerdidaPorLavado = TotalPesoSecoSucio - TotalPesoLavado;

    document.getElementById("TotalPesoLavado").value = TotalPesoLavado.toLocaleString('en-US');
    document.getElementById("PerdidaPorLavado").value = PerdidaPorLavado.toLocaleString('en-US');

    document.getElementById("PanWtRet").value = FactorAplicadoArray[9]?.toFixed(1) || '';

    const TotalPanGS = PerdidaPorLavado + FactorAplicadoArray[9];
    const TotalRetGS = (TotalPanGS / TotalPesoSecoSucio) * 100;
    const TotalCumRetGS = cumRetArray[19] + TotalRetGS;
    const TotalPassGS = Math.abs(100 - TotalCumRetGS);

    for (let i = 1; i <= 19; i++) {
        if (i <= 10) {
            document.getElementById("WtRet" + i).value = WtRetExtendidaArray[i - 1]?.toFixed(1) || '';
            document.getElementById("Ret" + i).value = RetArray[i - 1]?.toFixed(2) || '';
        } else {
            document.getElementById("WtRet" + i).value = FactorAplicadoArray[i - 11]?.toFixed(1) || '';
            document.getElementById("Ret" + i).value = RetCorrectionArray[i - 11]?.toFixed(2) || '';
        }
    }
    for (let i = 1; i <= 19; i++) {
        // CumRet: mostrar desde el índice 20 hacia el 2
        const cumRetIndex = i - 0;
        document.getElementById("CumRet" + i).value = cumRetArray[cumRetIndex]?.toFixed(2);

        // Pass: mostrar desde el índice 0 hacia el 18
        const passIndex = i - 1;
        document.getElementById("Pass" + i).value = PassArray[passIndex]?.toFixed(2) || "";
    }

    document.getElementById("TotalWtRet").value = TotalPanGS?.toLocaleString('en-US') || '';
    document.getElementById("TotalRet").value = TotalRetGS.toFixed(2);
    document.getElementById("TotalCumRet").value = TotalCumRetGS.toFixed(2);
    document.getElementById("TotalPass").value = TotalPassGS.toFixed(0);

    // Sumary Parameter
    const datos = [
        [PassArray[18], 0.075, PassArray[17], 0.85],
        [PassArray[17], 0.85, PassArray[16], 4.75],
        [PassArray[16], 4.75, PassArray[15], 9.50],
        [PassArray[15], 9.50, PassArray[14], 12.50],
        [PassArray[14], 12.50, PassArray[13], 19.00],
        [PassArray[13], 19.00, PassArray[12], 25.00],
        [PassArray[12], 25.00, PassArray[11], 37.50],
        [PassArray[11], 37.50, PassArray[10], 50.00],
        [PassArray[10], 50.00, PassArray[9], 75.00],
        [PassArray[9], 75.00, PassArray[8], 100.00],
        [PassArray[8], 100.00, PassArray[7], 150.00],
        [PassArray[7], 150.00, PassArray[6], 200.00],
        [PassArray[6], 200.00, PassArray[5], 250.00],  
        [PassArray[5], 250.00, PassArray[4], 300.00],
        [PassArray[4], 300.00, PassArray[3], 325.00],
        [PassArray[3], 325.00, PassArray[2], 500.00],
        [PassArray[2], 500.00, PassArray[1], 750.00],
        [PassArray[1], 750.00, PassArray[0], 1000.00],
        [PassArray[0], 1000.00, 0, 0],
    ];
    const val1 = document.getElementById('ClassificationUSCS1').value;
    const clasificacionExtra = clasificarSueloExtra(WtRetExtendidaArray);
    document.getElementById('classificationCombined').value = val1 + ' ' + clasificacionExtra;

    calcularParametrosGranulometricos(datos);
    UpdateGraph();
}

function average(numbers) {
    if (!Array.isArray(numbers) || numbers.length === 0) return 0;
    const sum = numbers.reduce((acc, val) => acc + val, 0);
    return sum / numbers.length;
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form.row");
    if (form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", TRF);
            input.addEventListener("input", calcularTotales);
            input.addEventListener("input", moisture);
        });
    }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
    el.addEventListener('click', () => {
        const tipo = el.dataset.exportar;
        enviarImagenAlServidor(tipo, ["GrainSizeRockGraph"]);
    });
});