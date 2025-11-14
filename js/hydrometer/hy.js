import { calcularParametrosGranulometricos } from '../grain-size/gs-summary.js';
import { enviarImagenAlServidor } from '../export/export-chart.js';
import { fetchData } from '../db-search/dbSearch.js';

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
    const Moisture = (WaterWw / DrySoilWs) * 100;
    // Correcion
    const DryMassHy = (AirDriedMassHydrometer / (1 + (Moisture / 100)));
    const DryMassHyPassingNo200 = DryMassHy - MassRetainedAfterHy;
    DryMassHyPassingGlobal = [DryMassHyPassingNo200];
    const FineContentHy = 100 * (1 - (MassRetainedAfterHy / DryMassHy));

    document.getElementById("WaterWw").value = WaterWw === 0 ? '' : WaterWw.toFixed(2);
    document.getElementById("DrySoilWs").value = DrySoilWs === 0 ? '' : DrySoilWs.toFixed(2);
    document.getElementById("MC").value = isNaN(Moisture) ? '' : Moisture.toFixed(2) + "%";

    document.getElementById("DryMassHydrometer").value = isNaN(DryMassHy) ? '' : DryMassHy.toFixed(2);
    document.getElementById("DryMassHySpecimenPassing").value = isNaN(DryMassHyPassingNo200) ? '' : DryMassHyPassingNo200.toFixed(2);
    document.getElementById("FineContentHySpecimen").value = isNaN(FineContentHy) ? '' : FineContentHy.toFixed(2);
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

    setValue("WtDrySoil", WtDrySoil === 0 ? '' : WtDrySoil.toFixed(2));
    setValue("WtWashPan", WtWashPan === 0 ? '' : WtWashPan.toFixed(2));

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
    const TotalPass = Math.abs(100 - TotalCumRet);

    setValue("TotalWtRet", TotalWtRet === 0 ? '' : TotalWtRet.toFixed(2));
    setValue("PanRet", isNaN(PanRet) ? '' : PanRet.toFixed(2));
    setValue("TotalRet", isNaN(TotalRet) ? '' : TotalRet.toFixed(2));
    setValue("TotalCumRet", isNaN(TotalCumRet) ? '' : TotalCumRet.toFixed(2));
    setValue("TotalPass", isNaN(TotalPass) ? '' : TotalPass.toFixed(2));

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

    calcularParametrosGranulometricos(datos);
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

    /* Variables constantes
        const Hr1 = 11.0;
        const Hr2 = 7.08;
        const r1 = 65.0;
        const r2 = 60.0;
        const vhb = 60.0;
        const Ac2 = 2 * 27.48;
        const CM = 1.0;
        const MassDensWater = 0.99821;
        const Acceleration = 980.7;
    */

    const Hr1 = parseFloat(document.getElementById("Hr1").value);
    const Hr2 = parseFloat(document.getElementById("Hr2").value);
    const r1 = parseFloat(document.getElementById("r1").value);
    const r2 = parseFloat(document.getElementById("r2").value);
    const vhb = parseFloat(document.getElementById("vhb").value);
    const Ac2 = parseFloat(document.getElementById("Ac2").value);
    const CM = parseFloat(document.getElementById("CM").value);
    const MassDensWater = parseFloat(document.getElementById("MassdensityofwaterCalibrated").value);
    const Acceleration = parseFloat(document.getElementById("Acceleration").value);

    const SG_Result = parseFloat(document.getElementById("SG_Result").value);
    const Volumeofsuspension = parseFloat(document.getElementById("Volumeofsuspension").value);
    const DryMassHySpecimenPassing = parseFloat(document.getElementById("DryMassHySpecimenPassing").value);
    const MeniscusCorrection = parseFloat(document.getElementById("MeniscusCorrection").value);
    const Viscosityofwater = parseFloat(document.getElementById("Viscosityofwater").value);

    const DmmR2 = Viscosityofwater * 18;
    const MDWA = MassDensWater * Acceleration;
    const AMDW = MDWA * (SG_Result - 1);
    const DmmHr1 = DmmR2 / AMDW;

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



$("input").on("blur", function (event) {
    event.preventDefault();
    enviarData();
});

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form.row");
    if (form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", HY);
            input.addEventListener("input", GS);
            input.addEventListener("input", hydrometer);
            //input.addEventListener("input", clasificarSuelo);

        });
    }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
    el.addEventListener('click', () => {
        const tipo = el.dataset.exportar;
        enviarImagenAlServidor(tipo, ["HydrometerGraph"]);
    });
});

function enviarData() {
    $.ajax({
        url: "../libs/graph/hydrometer.js",
        type: "GET",
        data: $("#nopasonada").serialize(),
        success: function (data) { }
    });
}

document.querySelector('[name="search"]').addEventListener('click', () => {
    // Obtener valores de los inputs
    const sampleName = document.getElementById('SampleName').value;
    const sampleNumber = document.getElementById('SampleNumber').value;

    // Liquid Limit and Plasticity Index
    fetchData('atterberg_limit', { Sample_ID: sampleName, Sample_Number: sampleNumber }, { Liquid_Limit_Porce: 'LiquidLimit', Plasticity_Index_Porce: 'PlasticityIndex' });
    // Specific Gravity
    fetchData('specific_gravity', { Sample_ID: sampleName, Sample_Number: sampleNumber }, { Specific_Gravity_Soil_Solid: 'SG_Result' });

    // Grain Size Distribution
    fetchData('grain_size_general', { Sample_ID: sampleName, Sample_Number: sampleNumber },
        {
            Container: 'Container', Wet_Soil_Tare: 'WtWetSoilTare', Wet_Dry_Tare: 'WtDrySoilTare', Tare: 'Tare_GS', Wt_Washed: 'WtWashed',
            WtRet6: 'WtRet1', WtRet7: 'WtRet2', WtRet8: 'WtRet3', WtRet9: 'WtRet4', WtRet10: 'WtRet5', WtRet11: 'WtRet6', WtRet12: 'WtRet7',
            WtRet13: 'WtRet8', WtRet14: 'WtRet9', WtRet15: 'WtRet10', WtRet16: 'WtRet11', WtRet17: 'WtRet12', WtRet18: 'WtRet13', WtRet19: 'WtRet14',
            WtRet20: 'WtRet15', WtRet21: 'WtRet16', WtRet22: 'WtRet17', PanWtRen: 'PanWtRen'
        });
});