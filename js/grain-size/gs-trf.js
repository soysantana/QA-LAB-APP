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
    const WtRetExtendidaArray = [];
    const FactorAplicadoArray = [];
    const RetArray = [];
    const RetCorrectionArray = [];
    const PassArray = [];
    let WtRet1x10 = 0;
    let FactorAplicadoTotal = 0;

    for (let i = 1; i <= 20; i++) {
        WtRetExtendida = parseFloat(document.getElementById("sTotal_" + i).value.replace(/,/g, ""));
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
    document.getElementById("Pass" + i).value = PassArray[passIndex]?.toFixed(0) || "";
}

    document.getElementById("TotalWtRet").value = TotalPanGS?.toLocaleString('en-US') || '';
    document.getElementById("TotalRet").value = TotalRetGS.toFixed(2);
    document.getElementById("TotalCumRet").value = TotalCumRetGS.toFixed(2);
    document.getElementById("TotalPass").value = TotalPassGS.toFixed(0);

    // Summary Grain Size Distribution Parameter
    let fines = null, sand = null, gravel = null, CoarserGravel = null;

    if (PassArray.length === 20) {
        fines = PassArray[18];                           // No. 200 Sieve (índice 18)
        sand = PassArray[16] - PassArray[18];            // No. 4 Sieve (índice 16)
        gravel = PassArray[9] - PassArray[16];          // 3" Sieve (índice 9)
        CoarserGravel = 100 - PassArray[9];

        document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
        document.getElementById("Gravel").value = gravel.toFixed(2);
        document.getElementById("Sand").value = sand.toFixed(2);
        document.getElementById("Fines").value = fines.toFixed(2);
    }

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

    const valoresBuscados = [10, 15, 30, 60, 85];
    const indiceColumnaBusqueda = 0;

    const resultados = valoresBuscados.map((valorBuscado) => {
        return datos.reduce((anterior, fila) => {
            if (
                fila[indiceColumnaBusqueda] <= valorBuscado &&
                fila[indiceColumnaBusqueda] > anterior[indiceColumnaBusqueda]
            ) {
                return fila;
            }
            return anterior;
        }, datos[0]);
    });


    const datosY10 = [resultados[0][0], resultados[0][2]];
    const datosX10 = [resultados[0][1], resultados[0][3]];

    // Calcular el logaritmo natural de los datos X
    var datosXln = datosX10.map(Math.log);

    // Calcular la regresión lineal
    var c = (datosY10[1] - datosY10[0]) / (datosXln[1] - datosXln[0]);


    // Calcular el logaritmo natural de los datos X
    var datosXln = datosX10.map(Math.log);

    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    var c = (datosY10[1] - datosY10[0]) / (datosXln[1] - datosXln[0]);

    // Calcular b
    var b = datosY10.reduce((a, b) => a + b, 0) / datosY10.length - c * datosXln.reduce((a, b) => a + b, 0) / datosXln.length;


    // Calcular la expresión
    const D10 = Math.exp((10 - b) / c);


    const datosY15 = [resultados[1][0], resultados[1][2]];
    const datosX15 = [resultados[1][1], resultados[1][3]];

    // Calcular el logaritmo natural de los datos X
    const datosXln15 = datosX15.map(Math.log);

    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c15 = (datosY15[1] - datosY15[0]) / (datosXln15[1] - datosXln15[0]);

    // Calcular b
    const b15 = datosY15.reduce((a, b) => a + b, 0) / datosY15.length - c15 * datosXln15.reduce((a, b) => a + b, 0) / datosXln15.length;

    // Calcular la expresión
    const D15 = Math.exp((15 - b15) / c15);


    const datosY30 = [resultados[2][0], resultados[2][2]];
    const datosX30 = [resultados[2][1], resultados[2][3]];

    // Calcular el logaritmo natural de los datos X
    const datosXln30 = datosX30.map(Math.log);

    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c30 = (datosY30[1] - datosY30[0]) / (datosXln30[1] - datosXln30[0]);

    // Calcular b
    const b30 = datosY30.reduce((a, b) => a + b, 0) / datosY30.length - c30 * datosXln30.reduce((a, b) => a + b, 0) / datosXln30.length;

    // Calcular la expresión
    const D30 = Math.exp((30 - b30) / c30);


    const datosY60 = [resultados[3][0], resultados[3][2]];
    const datosX60 = [resultados[3][1], resultados[3][3]];

    // Calcular el logaritmo natural de los datos X
    const datosXln60 = datosX60.map(Math.log);

    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c60 = (datosY60[1] - datosY60[0]) / (datosXln60[1] - datosXln60[0]);

    // Calcular b
    const b60 = datosY60.reduce((a, b) => a + b, 0) / datosY60.length - c60 * datosXln60.reduce((a, b) => a + b, 0) / datosXln60.length;

    // Calcular la expresión
    const D60 = Math.exp((60 - b60) / c60);


    const datosY85 = [resultados[4][0], resultados[4][2]];
    const datosX85 = [resultados[4][1], resultados[4][3]];

    // Calcular el logaritmo natural de los datos X
    const datosXln85 = datosX85.map(Math.log);

    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c85 = (datosY85[1] - datosY85[0]) / (datosXln85[1] - datosXln85[0]);

    // Calcular b
    const b85 = datosY85.reduce((a, b) => a + b, 0) / datosY85.length - c85 * datosXln85.reduce((a, b) => a + b, 0) / datosXln85.length;

    // Calcular la expresión
    const D85 = Math.exp((85 - b85) / c85);

    const umbral = 0.001;

    function formatValue(value) {
        if (isNaN(value) || value < umbral) {
            return "-";
        }
        return value.toFixed(3);
    }

    document.getElementById("D10").value = formatValue(D10);
    document.getElementById("D15").value = formatValue(D15);
    document.getElementById("D30").value = formatValue(D30);
    document.getElementById("D60").value = formatValue(D60);
    document.getElementById("D85").value = formatValue(D85);

    let Cc, Cu;

    if (D30 > umbral && D60 > umbral && D10 > umbral) {
        Cc = (D30 ** 2) / (D60 * D10);
        Cu = D60 / D10;
    } else {
        Cc = '-';
        Cu = '-';
    }

    if (D30 <= umbral || D60 <= umbral || D10 <= umbral) {
        Cc = '-';
        Cu = '-';
    }

    document.getElementById("Cc").value = Cc !== '-' ? parseFloat(Cc.toFixed(2)) : '-';
    document.getElementById("Cu").value = Cu !== '-' ? parseFloat(Cu.toFixed(2)) : '-';

      function clasificarSuelo() {
    if (gravel > sand && fines < 5 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        return "GW-Well graded gravel";
    } else if (gravel > sand && fines < 5 && Cu >= 4 && Cc >= 0.5 && Cc <= 3 && sand >= 15) {
        return "GW-Well graded gravel with sand";
    } else if (gravel > sand && fines < 5 && Cu < 4 && Cc < 1 && Cc > 3  && sand < 15) {
        return "GP-Poorly graded gravel";
    } else if (gravel > sand && fines < 5 && Cu < 4 &&  Cc > 3 && sand < 15) {
        return "GP-Poorly graded gravel";
    } else if (gravel > sand && fines < 5 && Cu < 4 && Cc < 1 && Cc > 3 && sand >= 15) {
        return "GP-Poorly graded gravel with sand";
    } else if (gravel > sand && fines < 5 && Cu < 4 &&  Cc > 3 && sand >= 15) {
        return "GP-Poorly graded gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        return "GW-GM Well graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        return "GW-GM Well graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        return "GW-GC-Well graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        return "GW-GC-Well graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        return "GP-GM-Poorly graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand >= 15) {
        return "GP-GM-Poorly graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        return "GP-GC-Poorly graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand >= 15) {
        return "GP-GC-Poorly graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        return "GM-Silty gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        return "GM-Silty gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        return "GC-Clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        return "GC-Clayey gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        return "GC-GM-Silty clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        return "GC-GM-Silty clayey gravel with sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 0.5 && Cc <= 3 && gravel < 15) {
        return "SW-Well graded sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        return "SW-Well graded sand with gravel";
    } else if (sand > gravel && fines < 5 && Cu < 6.4 && Cc < 1 && gravel < 15) {
        return "SP-Poorly graded sand";
    } else if (sand > gravel && fines < 5 && Cu < 6 &&  Cc > 3 && gravel < 15) {
        return "SP-Poorly graded sand";
    } else if (sand > gravel && fines < 5 && Cu < 6 && (Cc < 1 || Cc > 3) && gravel >= 15) {
        return "SP-Poorly graded sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        return "SW-SM-Well graded sand with silt";
    } else if (sand >gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel>= 15) {
        return "SW-SM-Well graded sand with silt and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel< 15) {
        return "SW-SC-Well graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        return "SW-SC-Well graded sand with clay and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel < 15) {
        return "SP-SM-Poorly graded sand with silt";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel>= 15) {
        return "SP-SM-Poorly graded sand with silt and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu > 6 && Cc >= 1 && Cc <= 3.4 && gravel>= 15) {
        return "SP~SM-Poorly graded sand with silt and gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel < 15) {
        return "SP-SC-Poorly graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel>= 15) {
        return "SP-SC-Poorly graded sand with clay and sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        return "SM-Silty sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        return "SM-Silty sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        return "SC-Clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        return "SC-Clayey sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        return "SC-GM-Silty clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        return "SC-GM-Silty clayey sand with gravel";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        return "GW-Well graded gravel with fines";
    } else {
        return "No se pudo clasificar el suelo.";
    }
}
    // Obtener el valor del campo de texto
  let classification = clasificarSuelo();

  document.getElementById("classification").value = classification;

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
    enviarData();
  });

      function enviarData() {
        $.ajax({
            url: "../libs/graph/Grain-Size-Full.js",
            type: "GET",
            data: $("#nopasonada").serialize(),
            success: function(data) {}
        });
    }