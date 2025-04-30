function HY() {

    const TareWetSoil = document.getElementById("TareWetSoil").value;
    const TareDrySoil = document.getElementById("TareDrySoil").value;
    const TareMc = document.getElementById("TareMc").value;
    const AirDriedMassHydrometer = document.getElementById("AirDriedMassHydrometer").value;
    const MassRetainedAfterHy = document.getElementById("MassRetainedAfterHy").value;

    // Humedad
    const WaterWw = TareWetSoil - TareDrySoil;
    const DrySoilWs = TareDrySoil - TareMc;
    const Moisture = (WaterWw / DrySoilWs)*100;
    // Correcion
    const DryMassHy = (AirDriedMassHydrometer/(1+(Moisture/100)));
    const DryMassHyPassingNo200 = DryMassHy - MassRetainedAfterHy;
    const FineContentHy = 100*(1-(MassRetainedAfterHy/DryMassHy));

    document.getElementById("WaterWw").value = WaterWw.toFixed(2);
    document.getElementById("DrySoilWs").value = DrySoilWs.toFixed(2);
    document.getElementById("MC").value = Moisture.toFixed(2) + "%";

    document.getElementById("DryMassHydrometer").value = DryMassHy.toFixed(2);
    document.getElementById("DryMassHySpecimenPassing").value = DryMassHyPassingNo200.toFixed(2);
    document.getElementById("FineContentHySpecimen").value = FineContentHy.toFixed(2);
}

function GS() {
    const getValue = (id) => {
        const value = parseFloat(document.getElementById(id).value);
        return isNaN(value) ? null : value;
    };

    const setValue = (id, value) => {
        document.getElementById(id).value = value !== null ? value.toFixed(2) : "";
    };

    const WtDrySoilTare = getValue("WtDrySoilTare");
    const Tare_GS = getValue("Tare_GS");
    const WtWashed = getValue("WtWashed");

    const WtDrySoil = WtDrySoilTare - Tare_GS;
    const WtWashPan = WtDrySoil - WtWashed;

    setValue("WtDrySoil", WtDrySoil);
    setValue("WtWashPan", WtWashPan);

    const cumRetArray = [0];
    const PassArray = [];

    for (let i = 1; i <= 17; i++) {
        const WtRet = getValue("WtRet" + i);

        let Ret = 0, CumRet = 0, Pass = 100;

        if (WtDrySoil && WtRet !== null) {
            Ret = (WtRet / WtDrySoil) * 100;
            CumRet = cumRetArray[i - 1] + Ret;
            cumRetArray.push(CumRet);
            Pass = 100 - CumRet;
        } else {
            cumRetArray.push(cumRetArray[i - 1]);
        }

    setValue("Ret" + i, WtRet !== null ? Ret : null);
    setValue("CumRet" + i, WtRet !== null ? CumRet : null);
    setValue("Pass" + i, WtRet !== null ? Pass : null);

        PassArray.push(Pass);
    }

    // Pan y Total Pan
    const PanWtRen = getValue("PanWtRen");

    const TotalWtRet = WtWashPan + PanWtRen;
    const PanRet = (PanWtRen / WtDrySoil) * 100;
    const TotalRet = (TotalWtRet / WtDrySoil) * 100;
    const TotalCumRet = cumRetArray[17] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    setValue("TotalWtRet", TotalWtRet);
    setValue("PanRet", PanRet);
    setValue("TotalRet", TotalRet);
    setValue("TotalCumRet", TotalCumRet);
    setValue("TotalPass", TotalPass);

    
    // Summary Grain Size Distribution Parameter
    let fines = null, sand = null, gravel = null, CoarserGravel = null;

    if (PassArray.length === 17) {
        fines = PassArray[16];                           // No. 200 Sieve (índice 16)
        sand = PassArray[8] - PassArray[16];            // No. 4 Sieve (índice 8)
        gravel = PassArray[0] - PassArray[8];          // 3" Sieve (índice 0)
        CoarserGravel = 100 - PassArray[0];
    }

    setValue("CoarserGravel", CoarserGravel);
    setValue("Gravel", gravel);
    setValue("Sand", sand);
    setValue("Fines", fines);

    // Sumary Parameter
    const datos = [
        [PassArray[16], 0.075, PassArray[15], 0.106],
        [PassArray[15], 0.106, PassArray[14], 0.15],
        [PassArray[14], 0.15, PassArray[13], 0.25],
        [PassArray[13], 0.25, PassArray[12], 0.3],
        [PassArray[12], 0.3, PassArray[11], 0.85],
        [PassArray[11], 0.85, PassArray[10], 1.18],
        [PassArray[10], 1.18, PassArray[9], 2.00],
        [PassArray[9], 2.00, PassArray[8], 4.75],
        [PassArray[8], 4.75, PassArray[7], 9.5],
        [PassArray[7], 9.5, PassArray[6], 12.50],
        [PassArray[6], 12.50, PassArray[5], 19.0],
        [PassArray[5], 19.0, PassArray[4], 25.0],
        [PassArray[4], 25.0, PassArray[3], 37.5],
        [PassArray[3], 37.5, PassArray[2], 50.8],
        [PassArray[2], 50.8, PassArray[1], 63],
        [PassArray[1], 63, PassArray[0], 75],
        [PassArray[0], 75, 0, 0]
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

    setValue("D10", D10);
    setValue("D15", D15);
    setValue("D30", D30);
    setValue("D60", D60);
    setValue("D85", D85);

    const Cc = (D30 ** 2) / (D60 * D10);
    const Cu = D60 / D10;

    setValue("Cc", Cc);
    setValue("Cu", Cu);

}

  function clasificarSuelo() {
        const getValue = (id) => {
        const value = parseFloat(document.getElementById(id).value);
        return isNaN(value) ? null : value;
    };

    const setValue = (id, value) => {
        document.getElementById(id).value = value !== null ? value.toFixed(2) : "";
    };

    const gravel = getValue("Gravel");
    const sand = getValue("Sand");
    const fines = getValue("Sand");
    const Cu = getValue("Cu");
    const Cc = getValue("Cc");

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

  console.log(classification); // Imprimir la clasificación en la consola

  // Dividir el texto basado en el primer guion
  let parts = classification.split(/-(.+)/); // Usa una expresión regular para dividir en el primer guion
  let part1 = parts[0]; // Parte antes del primer guion
  let part2 = parts[1] || ""; // Parte después del primer guion, o vacío si no existe