let DMmGlobal = [];
let PassingPerceTotalSampleGlobal = [];
let PassArray = [];
let DryMassHyPassingGlobal = [];

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
    DryMassHyPassingGlobal = [DryMassHyPassingNo200];
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
        document.getElementById(id).value = value !== null ? value : "";
    };

    const WtDrySoilTare = getValue("WtDrySoilTare");
    const Tare_GS = getValue("Tare_GS");
    const WtWashed = getValue("WtWashed");

    const WtDrySoil = WtDrySoilTare - Tare_GS;
    const WtWashPan = WtDrySoil - WtWashed;

    setValue("WtDrySoil", WtDrySoil.toFixed(2));
    setValue("WtWashPan", WtWashPan.toFixed(2));

    const cumRetArray = [0];
    PassArray = [];

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

    setValue("Ret" + i, WtRet !== null ? Ret.toFixed(2) : null);
    setValue("CumRet" + i, WtRet !== null ? CumRet.toFixed(2) : null);
    setValue("Pass" + i, WtRet !== null ? Pass.toFixed(2) : null);

        PassArray.push(Pass);
    }

    // Pan y Total Pan
    const PanWtRen = getValue("PanWtRen");

    const TotalWtRet = WtWashPan + PanWtRen;
    const PanRet = (PanWtRen / WtDrySoil) * 100;
    const TotalRet = (TotalWtRet / WtDrySoil) * 100;
    const TotalCumRet = cumRetArray[17] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    setValue("TotalWtRet", TotalWtRet.toFixed(2));
    setValue("PanRet", PanRet.toFixed(2));
    setValue("TotalRet", TotalRet.toFixed(2));
    setValue("TotalCumRet", TotalCumRet.toFixed(2));
    setValue("TotalPass", TotalPass.toFixed(2));


    // Summary Grain Size Distribution Parameter
    let fines = null, sand = null, gravel = null, CoarserGravel = null;

    if (PassArray.length === 17) {
        fines = PassArray[16];                           // No. 200 Sieve (índice 16)
        sand = PassArray[8] - PassArray[16];            // No. 4 Sieve (índice 8)
        gravel = PassArray[0] - PassArray[8];          // 3" Sieve (índice 0)
        CoarserGravel = 100 - PassArray[0];
    }

    setValue("CoarserGravel", CoarserGravel.toFixed(2));
    setValue("Gravel", gravel.toFixed(2));
    setValue("Sand", sand.toFixed(2));
    setValue("Fines", fines.toFixed(2));

    // Sumary Parameter
    const datos = [
        [PassingPerceTotalSampleGlobal[8], DMmGlobal[8], PassingPerceTotalSampleGlobal[7], DMmGlobal[7]],
        [PassingPerceTotalSampleGlobal[7], DMmGlobal[7], PassingPerceTotalSampleGlobal[6], DMmGlobal[6]],
        [PassingPerceTotalSampleGlobal[6], DMmGlobal[6], PassingPerceTotalSampleGlobal[5], DMmGlobal[5]],
        [PassingPerceTotalSampleGlobal[5], DMmGlobal[5], PassingPerceTotalSampleGlobal[4], DMmGlobal[4]],
        [PassingPerceTotalSampleGlobal[4], DMmGlobal[4], PassingPerceTotalSampleGlobal[3], DMmGlobal[3]],
        [PassingPerceTotalSampleGlobal[3], DMmGlobal[3], PassingPerceTotalSampleGlobal[2], DMmGlobal[2]],
        [PassingPerceTotalSampleGlobal[2], DMmGlobal[2], PassingPerceTotalSampleGlobal[1], DMmGlobal[1]],
        [PassingPerceTotalSampleGlobal[1], DMmGlobal[1], PassingPerceTotalSampleGlobal[0], DMmGlobal[0]],
        [PassingPerceTotalSampleGlobal[0], DMmGlobal[0], PassArray[16], 0.075],
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

    setValue("D10", D10.toFixed(3));
    setValue("D15", D15.toFixed(3));
    setValue("D30", D30.toFixed(3));
    setValue("D60", D60.toFixed(3));
    setValue("D85", D85.toFixed(3));

    const umbral = 0.001;

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

}

function clasificarSuelo() {
    const getValue = (id) => {
        const value = parseFloat(document.getElementById(id).value);
        return isNaN(value) ? null : value;
    };

    const setValue = (id, value) => {
        document.getElementById(id).value = value !== null ? value : "";
    };

    const gravel = getValue("Gravel");
    const sand = getValue("Sand");
    const fines = getValue("Fines");
    const Cu = getValue("Cu");
    const Cc = getValue("Cc");
    const LL = getValue("LiquidLimit");
    const PI = getValue("PlasticityIndex");

    const tipo = sand >= gravel ? "arena" : "grava";
    const mayoritario = tipo === "arena" ? "sand" : "gravel";
    const minoritario = tipo === "arena" ? "gravel" : "sand";

    let resultado = "";

    if (fines >= 50) {
        if (LL < 50) {
            if (PI > 7 && PI >= 0.73 * (LL - 20)) resultado = "CL - Lean clay";
            else if (PI >= 4 && PI <= 7 && PI >= 0.73 * (LL - 20)) resultado = "CL-ML - Silty clay";
            else resultado = "ML - Silt";
        } else {
            resultado = PI >= 0.73 * (LL - 20) ? "CH - Fat clay" : "MH - Elastic silt";
        }
    } else {
        const limpio = fines < 5;
        const conFinos = fines >= 12;

        if (limpio) {
            if (tipo === "arena") {
                resultado = (Cu !== null && Cc !== null && Cu >= 6 && Cc >= 1 && Cc <= 3)
                    ? "SW - Well graded sand"
                    : "SP - Poorly graded sand";
            } else {
                resultado = (Cu !== null && Cc !== null && Cu >= 4 && Cc >= 1 && Cc <= 3)
                    ? "GW - Well graded gravel"
                    : "GP - Poorly graded gravel";
            }
        } else if (conFinos) {
            let simbolo;
            if (LL >= 50) {
                simbolo = PI >= 0.73 * (LL - 20)
                    ? (tipo === "arena" ? "SC" : "GC")
                    : (tipo === "arena" ? "SM" : "GM");
            } else {
                simbolo = PI >= 7
                    ? (tipo === "arena" ? "SC" : "GC")
                    : (tipo === "arena" ? "SM" : "GM");
            }

            const nombre = simbolo.endsWith("C")
                ? `Clayey ${mayoritario} with ${minoritario}`
                : `Silty ${mayoritario} with ${minoritario}`;

            resultado = `${simbolo} - ${nombre}`;
        } else {
            // Transicional (entre 5% y 12% de finos)
            let simbolo1 = tipo === "arena" ? "SP" : "GP";
            let simbolo2 = PI >= 7 ? (tipo === "arena" ? "SC" : "GC") : (tipo === "arena" ? "SM" : "GM");
            resultado = `${simbolo1}-${simbolo2} - ${mayoritario} with some fines`;
        }
    }

    setValue("Classification1", resultado);
}

function hydrometer() {
  // Fechas
  const base = document.getElementById("Date1").value;
  if (!base) return;

  for (let i = 2; i <= 8; i++) {
    document.getElementById("Date" + i).value = base;
  }

  const date = new Date(base);
  date.setDate(date.getDate() + 1);
  document.getElementById("Date9").value = date.toISOString().split("T")[0];

  // Horas
    const baseTime = document.getElementById("Hour1").value;
  if (!baseTime) return;

  const [hours, minutes] = baseTime.split(":").map(Number);
  const baseDate = new Date();
  baseDate.setHours(hours, minutes, 0, 0);

  for (let i = 2; i <= 9; i++) {
    const readingInput = document.getElementById("ReadingTimeT" + i);
    if (!readingInput) continue;

    const timeToAdd = parseFloat(readingInput.value);
    if (isNaN(timeToAdd)) continue;

    const newDate = new Date(baseDate);
    newDate.setMinutes(newDate.getMinutes() + timeToAdd);

    const hh = String(newDate.getHours()).padStart(2, '0');
    const mm = String(newDate.getMinutes()).padStart(2, '0');
    document.getElementById("Hour" + i).value = `${hh}:${mm}`;
  }

// Calculation
let total = 0;
let count = 0;
let average = null;

const Hr1 = 11.0;
const Hr2 = 7.08;
const r1 = 65.0;
const r2 = 60.0;
const vhb = 60.0;
const Ac2 = 2*27.48;
const CM = 1.0;
const MassDensWater = 0.99821;
const Acceleration = 980.7;


  const SG_Result = parseFloat(document.getElementById("SG_Result").value);
  const Volumeofsuspension = parseFloat(document.getElementById("Volumeofsuspension").value);
  const DryMassHySpecimenPassing = parseFloat(document.getElementById("DryMassHySpecimenPassing").value);
  const MeniscusCorrection = parseFloat(document.getElementById("MeniscusCorrection").value);
  const Viscosityofwater = parseFloat(document.getElementById("Viscosityofwater").value);

  const DmmR2 = Viscosityofwater * 18;
  const MDWA = MassDensWater * Acceleration;
  const AMDW = MDWA*(SG_Result-1);
  const DmmHr1 = DmmR2/AMDW;

for (let i = 1; i <= 5; i++) {  // Solo del 1 al 5 para calcular el promedio
  const HyCalibrationTemp = parseFloat(document.getElementById("HyCalibrationTemp" + i).value);
  const HyCalibrationRead = parseFloat(document.getElementById("HyCalibrationRead" + i).value);

  if (!isNaN(HyCalibrationTemp) && !isNaN(HyCalibrationRead)) {
    const AorB = HyCalibrationRead + (0.01248 * HyCalibrationTemp) + (0.00795 * (HyCalibrationTemp ** 2));
    total += AorB;
    count++;
  }
}

if (count > 0) {
  average = (total / count);

  for (let i = 1; i <= 9; i++) {  // Se usa el promedio del 1-5 en todos los campos del 1-9
    const field = document.getElementById("ABdependingHy" + i);
    if (field) field.value = average.toFixed(1);
  }
}

// Aquí continúa tu otro bucle completo (1 al 9) para calcular y mostrar los resultados:
for (let i = 1; i <= 9; i++) {
  const Temp = parseFloat(document.getElementById("Temp" + i).value);
  const HyReading = parseFloat(document.getElementById("HyReading" + i).value);
  const ReadingTimeT = parseFloat(document.getElementById("ReadingTimeT" + i).value);

  const ReadingTimeMin = ReadingTimeT * 60;

  if (average !== null && !isNaN(Temp)) {
    const OffsetReading = average - (0.01248 * Temp) - (0.00795 * (Temp ** 2));
    document.getElementById("OffsetReading" + i).value = OffsetReading.toFixed(1);

    const MassPercentFiner = 0.6226 * ((SG_Result / (SG_Result - 1)) * ((Volumeofsuspension / DryMassHyPassingGlobal) * (HyReading - OffsetReading)) * (100 / 1000));
    document.getElementById("MassPercentFiner" + i).value = MassPercentFiner.toFixed(2);

    const EffectiveLength = Hr2 + ((Hr1 / r1) * (r2 - HyReading + MeniscusCorrection)) - (vhb / Ac2);
    document.getElementById("EffectiveLength" + i).value = EffectiveLength.toFixed(2);

    const DMm = Math.sqrt(DmmHr1 * (EffectiveLength / ReadingTimeMin)) * 10;
    DMmGlobal[i - 1] = DMm;
    document.getElementById("DMm" + i).value = DMm.toFixed(4);

    const PassingPerceTotalSample = (PassArray[16] * MassPercentFiner) / 100;
    PassingPerceTotalSampleGlobal[i - 1] = PassingPerceTotalSample;
    document.getElementById("PassingPerceTotalSample" + i).value = PassingPerceTotalSample.toFixed(2);
  } else {
    document.getElementById("OffsetReading" + i).value = "";
  }
}








}

  $("input").on("blur", function(event) {
    event.preventDefault();
    HY();
    GS();
    hydrometer();
    clasificarSuelo();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/hydrometer.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }