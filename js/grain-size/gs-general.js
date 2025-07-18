  /**
   * Function Grain Size Coarse Aggregate
   */

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

// Calcular resumen con protección nullish
const sieve3In = PassArray[5] ?? 100;
const sieveNo4 = PassArray[13] ?? 100;
const sieveNo200 = PassArray[21] ?? 100;

const CoarserGravel = 100 - sieve3In;
const gravel = sieve3In - sieveNo4;
const sand = sieveNo4 - sieveNo200;
const fines = sieveNo200;

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

document.getElementById("CoarserGravel").value = !isNaN(CoarserGravel) ? CoarserGravel.toFixed(2) : "";
document.getElementById("Gravel").value = !isNaN(gravel) ? gravel.toFixed(2) : "";
document.getElementById("Sand").value = !isNaN(sand) ? sand.toFixed(2) : "";
document.getElementById("Fines").value = !isNaN(fines) ? fines.toFixed(2) : "";

    // Sumary Parameter
    const datos = [
        [PassArray[21], 0.075, PassArray[20], 0.106],
        [PassArray[20], 0.106, PassArray[19], 0.15],
        [PassArray[19], 0.15, PassArray[18], 0.25],
        [PassArray[18], 0.25, PassArray[17], 0.3],
        [PassArray[17], 0.3, PassArray[16], 0.85],
        [PassArray[16], 0.85, PassArray[14], 1.18],
        [PassArray[15], 1.18, PassArray[13], 2],
        [PassArray[14], 2, PassArray[12], 4.75],
        [PassArray[13], 4.75, PassArray[11], 9.5],
        [PassArray[12], 9.5, PassArray[10], 12.5],
        [PassArray[11], 12.5, PassArray[9], 19],
        [PassArray[10], 19, PassArray[8], 25],
        [PassArray[9], 25, PassArray[7], 38.1],
        [PassArray[8], 38.1, PassArray[6], 50.8],
        [PassArray[7], 50.8, PassArray[5], 63.5],
        [PassArray[6], 63.5, PassArray[4], 76.2],
        [PassArray[5], 76.2, PassArray[3], 88.9],
        [PassArray[4], 88.9, PassArray[2], 101.6],
        [PassArray[3], 101.6, PassArray[1], 127],
        [PassArray[2], 127, PassArray[0], 0.0]
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

    document.getElementById("D10").value = D10.toFixed(2);
    document.getElementById("D15").value = D15.toFixed(2);
    document.getElementById("D30").value = D30.toFixed(2);
    document.getElementById("D60").value = D60.toFixed(2);
    document.getElementById("D85").value = D85.toFixed(2);

    let Cc;
    let Cu;

    const umbral = 0.01;

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

  // Dividir el texto basado en el primer guion
  let parts = classification.split(/-(.+)/); // Usa una expresión regular para dividir en el primer guion
  let part1 = parts[0]; // Parte antes del primer guion
  let part2 = parts[1] || ""; // Parte después del primer guion, o vacío si no existe

  // Asignar los valores a los inputs
  document.getElementById("ClassificationUSCS1").value = part2;
  document.getElementById("ClassificationUSCS2").value = part1;
    

    $("input").on("blur", function(event) {
        event.preventDefault();
        enviarData();
    });

    function enviarData() {
        $.ajax({
            url: "../libs/graph/Grain-Size-General.js",
            type: "GET",
            success: function() {}
        });
    }

    function actualizarImagen() {
        var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeGeneral'));

        var ImageURL = GrainSizeGeneral.getDataURL({
            pixelRatio: 1,
            backgroundColor: '#fff'
        });

        fetch(ImageURL)
            .then(response => response.blob())
            .then(GraphBlob => {
                // Convierte la imagen a base64
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(GraphBlob);
                });
            })
            .then(GraphBase64 => {
                document.getElementById('Graph').value = GraphBase64;
            })
            .catch(error => console.error('Error al convertir la imagen a Base64:', error));
    }
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('blur', actualizarImagen);
    });

}