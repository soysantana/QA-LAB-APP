import { enviarImagenAlServidor } from '../export/export-chart.js';

let DryMassHyPassingGlobal = [];
let SuspensionFactor25g = 0;
let SuspensionFactor50g = 0;
let CT1 = 0;
let CT2 = 0;
let HyConstK = 0;
let correctedViscosity = 0;
let unitWeightWater = 0;
let DensityRatio = 0;
let effectiveUnitWeight = 0;
let viscosityWeightRatio = 0;
let Hr1 = 0;
let Hr2 = 0;
let Vhb = 0;
let twoAc = 0;
let correctedHydrometer = 0;
let Vsp = 0;
let CM = 0;

function dhy() {
  const SG = parseFloat(document.getElementById("SG_Result").value);
  const viscosityWater = parseFloat(document.getElementById("Viscosityofwater").value);
  Vsp = parseFloat(document.getElementById("Volumeofsuspension").value);
  CM = parseFloat(document.getElementById("MeniscusCorrection").value);

  CT1 = 0.01248; // corrección térmica lineal
  CT2 = 0.00795; // corrección térmica cuadrática
  HyConstK = 0.6226; // constante del hidrómetro

  correctedViscosity = viscosityWater * 18; // Viscosity of water (g*s/cm2)
  unitWeightWater = 0.99821 * 980.7; // mass dens water * acceleration
  DensityRatio = SG / (SG - 1); // relación sg
  effectiveUnitWeight = unitWeightWater * (SG - 1); // unitWeightWater * SG -1
  viscosityWeightRatio = correctedViscosity / effectiveUnitWeight; // correctedViscosity / effectiveUnitWeight

  Hr1 = 10.5;
  Hr2 = 14;
  Vhb = 67;
  twoAc = 2 * 27.48;
  correctedHydrometer = Hr1 - Hr2;
}

function mcdhy() {
  DryMassHyPassingGlobal = [];
  for (let i = 1; i <= 2; i++) {
    const TareWetSoil = document.getElementById("TareWetSoil" + i).value;
    const TareDrySoil = document.getElementById("TareDrySoil" + i).value;
    const TareMc = document.getElementById("TareMc" + i).value;
    const AirDriedMassHydrometer = document.getElementById("AirDriedMassHydrometer" + i).value;
    const MassRetainedAfterHy = document.getElementById("MassRetainedAfterHy" + i).value;

    // Humedad
    const WaterWw = TareWetSoil - TareDrySoil;
    const DrySoilWs = TareDrySoil - TareMc;
    const Moisture = (WaterWw / DrySoilWs) * 100;

    // Correcion
    const DryMassHy = (AirDriedMassHydrometer / (1 + (Moisture / 100)));
    const DryMassHyPassingNo200 = DryMassHy - MassRetainedAfterHy;
    const FineContentHy = 100 * (1 - (MassRetainedAfterHy / DryMassHy));

    DryMassHyPassingGlobal.push(DryMassHyPassingNo200);

    document.getElementById("WaterWw" + i).value = WaterWw === 0 ? '' : WaterWw.toFixed(2);
    document.getElementById("DrySoilWs" + i).value = DrySoilWs === 0 ? '' : DrySoilWs.toFixed(2);
    document.getElementById("MC" + i).value = isNaN(Moisture) ? '' : Moisture.toFixed(2) + "%";
    document.getElementById("DryMassHydrometer" + i).value = isNaN(DryMassHy) ? '' : DryMassHy.toFixed(2);
    document.getElementById("DryMassHySpecimenPassing" + i).value = isNaN(DryMassHyPassingNo200) ? '' : DryMassHyPassingNo200.toFixed(2);
    document.getElementById("FineContentHySpecimen" + i).value = isNaN(FineContentHy) ? '' : FineContentHy.toFixed(2);
  }
  SuspensionFactor25g = Vsp / DryMassHyPassingGlobal[0]; // volumen / masa seca 25g
  SuspensionFactor50g = Vsp / DryMassHyPassingGlobal[1]; // volumen / masa seca 50g
}

function dhy25g() {
  const HyReadingArray25g = [];

  for (let i = 1; i <= 9; i++) {
    const HyReadingValue25g = parseFloat(document.getElementById("HyReading" + i).value);
    if (!isNaN(HyReadingValue25g)) HyReadingArray25g.push(HyReadingValue25g);
  }

  const r1 = Math.max(...HyReadingArray25g);
  const r2 = Math.min(...HyReadingArray25g);
  const rangeReading25g = r2 - r1;

  for (let i = 1; i <= 9; i++) {
    const ReadingTimeT25g = parseFloat(document.getElementById("ReadingTimeT" + i).value);
    const HyCaliTemp25g = parseFloat(document.getElementById("HyCalibrationTemp" + i).value);
    const HyCaliRead25g = parseFloat(document.getElementById("HyCalibrationRead" + i).value);
    const Temp25g = parseFloat(document.getElementById("Temp" + i).value);
    const HyReading25g = parseFloat(document.getElementById("HyReading" + i).value);

    const ABdependingHy25g = HyCaliRead25g + (CT1 * HyCaliTemp25g) + (CT2 * Math.pow(HyCaliTemp25g, 2));
    const OffsetReading25g = ABdependingHy25g - (CT1 * Temp25g) - (CT2 * Math.pow(Temp25g, 2));
    const MassPercentFiner25g = Math.abs(HyConstK * DensityRatio * (SuspensionFactor25g * (HyReading25g - OffsetReading25g)) * (100 / 1000));
    const EffectiveLength25g = Hr2 + ((correctedHydrometer / rangeReading25g) * (r2 - HyReading25g + CM)) - (Vhb / twoAc);
    const DMm25g = Math.sqrt(viscosityWeightRatio * (EffectiveLength25g / (ReadingTimeT25g * 60))) * 10;

    document.getElementById("ABdependingHy" + i).value = isNaN(ABdependingHy25g) ? '' : ABdependingHy25g.toFixed(1);
    document.getElementById("OffsetReading" + i).value = isNaN(OffsetReading25g) ? '' : OffsetReading25g.toFixed(1);
    document.getElementById("MassPercentFiner" + i).value = isNaN(MassPercentFiner25g) ? '' : MassPercentFiner25g.toFixed(2);
    document.getElementById("EffectiveLength" + i).value = isNaN(EffectiveLength25g) ? '' : EffectiveLength25g.toFixed(2);
    document.getElementById("DMm" + i).value = isNaN(DMm25g) ? '' : DMm25g.toFixed(4);
  }
}

function dhy50g() {
  const HyReadingArray50g = [];

  for (let i = 1; i <= 9; i++) {
    const HyReadingValue50g = parseFloat(document.getElementById("HyReading50g" + i).value);
    if (!isNaN(HyReadingValue50g)) HyReadingArray50g.push(HyReadingValue50g);
  }

  const r1 = Math.max(...HyReadingArray50g);
  const r2 = Math.min(...HyReadingArray50g);
  const rangeReading50g = r2 - r1;

  for (let i = 1; i <= 9; i++) {
    const ReadingTimeT50g = parseFloat(document.getElementById("ReadingTimeT50g" + i).value);
    const HyCaliTemp50g = parseFloat(document.getElementById("HyCalibrationTemp50g" + i).value);
    const HyCaliRead50g = parseFloat(document.getElementById("HyCalibrationRead50g" + i).value);
    const Temp50g = parseFloat(document.getElementById("Temp50g" + i).value);
    const HyReading50g = parseFloat(document.getElementById("HyReading50g" + i).value);

    const ABdependingHy50g = HyCaliRead50g + (CT1 * HyCaliTemp50g) + (CT2 * Math.pow(HyCaliTemp50g, 2));
    const OffsetReading50g = ABdependingHy50g - (CT1 * Temp50g) - (CT2 * Math.pow(Temp50g, 2));
    const MassPercentFiner50g = Math.abs(HyConstK * DensityRatio * (SuspensionFactor50g * (HyReading50g - OffsetReading50g)) * (100 / 1000));
    const EffectiveLength50g = Hr2 + ((correctedHydrometer / rangeReading50g) * (r2 - HyReading50g + CM)) - (Vhb / twoAc);
    const DMm50g = Math.sqrt(viscosityWeightRatio * (EffectiveLength50g / (ReadingTimeT50g * 60))) * 10;

    document.getElementById("ABdependingHy50g" + i).value = isNaN(ABdependingHy50g) ? '' : ABdependingHy50g.toFixed(1);
    document.getElementById("OffsetReading50g" + i).value = isNaN(OffsetReading50g) ? '' : OffsetReading50g.toFixed(1);
    document.getElementById("MassPercentFiner50g" + i).value = isNaN(MassPercentFiner50g) ? '' : MassPercentFiner50g.toFixed(2);
    document.getElementById("EffectiveLength50g" + i).value = isNaN(EffectiveLength50g) ? '' : EffectiveLength50g.toFixed(2);
    document.getElementById("DMm50g" + i).value = isNaN(DMm50g) ? '' : DMm50g.toFixed(4);
  }
}

function dateHour(prefix = "") {
  const baseDateInput = document.getElementById(`Date${prefix}1`);
  if (!baseDateInput || !baseDateInput.value) return;

  const baseDate = new Date(baseDateInput.value);

  // Asignar misma fecha del 1 al 8
  for (let i = 2; i <= 8; i++) {
    const dateInput = document.getElementById(`Date${prefix}${i}`);
    if (dateInput) dateInput.value = baseDateInput.value;
  }

  // Para el noveno día sumamos un día más
  const nextDay = new Date(baseDate);
  nextDay.setDate(nextDay.getDate() + 1);
  const lastDateInput = document.getElementById(`Date${prefix}9`);
  if (lastDateInput) lastDateInput.value = nextDay.toISOString().split("T")[0];

  // --- Horas ---
  const baseHourInput = document.getElementById(`Hour${prefix}1`);
  if (!baseHourInput || !baseHourInput.value) return;

  const [hours, minutes] = baseHourInput.value.split(":").map(Number);
  const baseTime = new Date();
  baseTime.setHours(hours, minutes, 0, 0);

  for (let i = 2; i <= 9; i++) {
    const readingInput = document.getElementById(`ReadingTimeT${prefix}${i}`);
    if (!readingInput) continue;

    const minutesToAdd = parseFloat(readingInput.value);
    if (isNaN(minutesToAdd)) continue;

    const newTime = new Date(baseTime);
    newTime.setMinutes(newTime.getMinutes() + minutesToAdd);

    const hh = String(newTime.getHours()).padStart(2, "0");
    const mm = String(newTime.getMinutes()).padStart(2, "0");
    const hourInput = document.getElementById(`Hour${prefix}${i}`);
    if (hourInput) hourInput.value = `${hh}:${mm}`;
  }
}

function classification() {
  const Nm2um25g = parseFloat(document.getElementById("MassPercentFiner9").value);
  const Nm2um50g = parseFloat(document.getElementById("MassPercentFiner50g9").value);

  const dispersion = (Nm2um25g / Nm2um50g) * 100;

  let classification = "";

  if (isNaN(dispersion)) {
    classification = "";
  } else if (dispersion <= 30) {
    classification = "No Dispersive";
  } else if (dispersion >= 50) {
    classification = "Dispersive";
  } else {
    classification = "Intermediate";
  }

  document.getElementById("Nm2umDispersed1").value = isNaN(Nm2um25g) ? '' : Nm2um25g.toFixed(0);
  document.getElementById("Nm2umDispersed2").value = isNaN(Nm2um50g) ? '' : Nm2um50g.toFixed(0);
  document.getElementById("Nm2umDispersed3").value = isNaN(dispersion) ? '' : dispersion.toFixed(0);
  document.getElementById("Nm2umDispersed4").value = classification;
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.row");
  if (form) {
    form.querySelectorAll("input").forEach(input => {
      input.addEventListener("input", dhy);
      input.addEventListener("input", mcdhy);
      input.addEventListener("input", dhy25g);
      input.addEventListener("input", dhy50g);
      input.addEventListener("input", classification);
    });
  }

  document.getElementById("Date1").addEventListener("change", () => dateHour(""));
  document.getElementById("Hour1").addEventListener("change", () => dateHour(""));

  document.getElementById("Date50g1").addEventListener("change", () => dateHour("50g"));
  document.getElementById("Hour50g1").addEventListener("change", () => dateHour("50g"));

  document.querySelectorAll('[data-exportar]').forEach((el) => {
    el.addEventListener('click', () => {
      const tipo = el.dataset.exportar;
      enviarImagenAlServidor(tipo);
    });
  });
});