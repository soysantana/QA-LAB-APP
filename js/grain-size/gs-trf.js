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
            totalInput.value = totals[key].toLocaleString("en-US"); // Formatea el total con separadores de miles
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
    const CorrectionMC = totalHumedo/(1+(promedio/100));
    const TotalPesoSecoSucio = totalMore3 + CorrectionMC;

    document.getElementById("TotalPesoSecoSucio").value = TotalPesoSecoSucio;

    //Grain Size Reducida
    document.getElementById("PesoSecoSucio").value = totalSecoSucio;
    document.getElementById("PesoLavado").value = totalLess3;

    const PanLavado = totalSecoSucio - totalLess3;

    document.getElementById("PanLavado").value = PanLavado;

    // Factor de conversion
    const FactorConversion = (totalSecoSucio/CorrectionMC)*100;

    //GS Combinada & Factor aplicado
    const cumRetArray = [0];
    let WtRet1x10 = 0;
    let FactorAplicado = 0;
    for (let i = 1; i <= 20; i++) {
        const WtRetExtendida = parseFloat(document.getElementById("sTotal_" + i).value.replace(/,/g, ""));

    if (i >= 1 && i <= 10) {
        WtRet1x10 += WtRetExtendida;
        const Ret = (WtRetExtendida / TotalPesoSecoSucio) * 100;
        const CumRet = cumRetArray[i - 1] + Ret; cumRetArray.push(CumRet);
        const Pass = 100 - CumRet;
    }

    if (i >= 11 && i <= 19) {
        const FactorAplicado = (WtRetExtendida * 100) / FactorConversion;
        const RetCorrection = (FactorAplicado / TotalPesoSecoSucio) * 100;
        const CumRet = cumRetArray[i - 1] + RetCorrection; cumRetArray.push(CumRet);
        const Pass = 100 - CumRet;
    }

    }

    const PanGS = parseFloat(document.getElementById("sTotal_20").value.replace(/,/g, ""));

    const TotalPesoLavado = WtRet1x10 + FactorAplicado + PanGS;
    const PerdidaPorLavado = TotalPesoSecoSucio - TotalPesoLavado;
    console.log(TotalPesoLavado);
    console.log(PerdidaPorLavado);

    const TotalPanGS = PerdidaPorLavado + PanGS;
    const TotalRetGS = (TotalPanGS / TotalPesoSecoSucio) * 100;
    const TotalCumRetGS = cumRetArray[19] + TotalRetGS;
    const TotalPassGS = 100 - TotalCumRetGS;

    

}

function average(numbers) {
    if (!Array.isArray(numbers) || numbers.length === 0) return 0;
    const sum = numbers.reduce((acc, val) => acc + val, 0);
    return sum / numbers.length;
}

  $("input").on("blur", function(event) {
    event.preventDefault();
    TRF();
    calcularTotales();
    moisture();
  });