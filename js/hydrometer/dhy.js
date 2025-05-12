let DMmGlobal = [];
let PassingPerceTotalSampleGlobal = [];
let PassArray = [];
let DryMassHyPassingGlobal = [];

function HY() {
    for (let i = 1; i <= 2; i++) {
    const TareWetSoil = document.getElementById("TareWetSoil" + i).value;
    const TareDrySoil = document.getElementById("TareDrySoil" + i).value;
    const TareMc = document.getElementById("TareMc" + i).value;
    const AirDriedMassHydrometer = document.getElementById("AirDriedMassHydrometer" + i).value;
    const MassRetainedAfterHy = document.getElementById("MassRetainedAfterHy" + i).value;

    // Humedad
    const WaterWw = TareWetSoil - TareDrySoil;
    const DrySoilWs = TareDrySoil - TareMc;
    const Moisture = (WaterWw / DrySoilWs)*100;

    // Correcion
   const DryMassHy = (AirDriedMassHydrometer/(1+(Moisture/100)));
   const DryMassHyPassingNo200 = DryMassHy - MassRetainedAfterHy;
   DryMassHyPassingGlobal = [DryMassHyPassingNo200];
   const FineContentHy = 100*(1-(MassRetainedAfterHy/DryMassHy));

    document.getElementById("WaterWw" + i).value = WaterWw.toFixed(2);
    document.getElementById("DrySoilWs" + i).value = DrySoilWs.toFixed(2);
    document.getElementById("MC" + i).value = Moisture.toFixed(2) + "%";
    document.getElementById("DryMassHydrometer" + i).value = DryMassHy.toFixed(2);
    document.getElementById("DryMassHySpecimenPassing" + i).value = DryMassHyPassingNo200.toFixed(2);
    document.getElementById("FineContentHySpecimen" + i).value = FineContentHy.toFixed(2);
    }
}

function hydrometer50g() {
  // Fechas
  const base = document.getElementById("Date50g1").value;
  if (!base) return;

  for (let i = 2; i <= 8; i++) {
    document.getElementById("Date50g" + i).value = base;
  }

  const date = new Date(base);
  date.setDate(date.getDate() + 1);
  document.getElementById("Date50g9").value = date.toISOString().split("T")[0];

  // Horas
    const baseTime = document.getElementById("Hour50g1").value;
  if (!baseTime) return;

  const [hours, minutes] = baseTime.split(":").map(Number);
  const baseDate = new Date();
  baseDate.setHours(hours, minutes, 0, 0);

  for (let i = 2; i <= 9; i++) {
    const readingInput = document.getElementById("ReadingTimeT50g" + i);
    if (!readingInput) continue;

    const timeToAdd = parseFloat(readingInput.value);
    if (isNaN(timeToAdd)) continue;

    const newDate = new Date(baseDate);
    newDate.setMinutes(newDate.getMinutes() + timeToAdd);

    const hh = String(newDate.getHours()).padStart(2, '0');
    const mm = String(newDate.getMinutes()).padStart(2, '0');
    document.getElementById("Hour50g" + i).value = `${hh}:${mm}`;
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
  const MeniscusCorrection = parseFloat(document.getElementById("MeniscusCorrection").value);
  const Viscosityofwater = parseFloat(document.getElementById("Viscosityofwater").value);

  const DmmR2 = Viscosityofwater * 18;
  const MDWA = MassDensWater * Acceleration;
  const AMDW = MDWA*(SG_Result-1);
  const DmmHr1 = DmmR2/AMDW;

for (let i = 1; i <= 5; i++) {  // Solo del 1 al 5 para calcular el promedio
  const HyCalibrationTemp = parseFloat(document.getElementById("HyCalibrationTemp50g" + i).value);
  const HyCalibrationRead = parseFloat(document.getElementById("HyCalibrationRead50g" + i).value);

  if (!isNaN(HyCalibrationTemp) && !isNaN(HyCalibrationRead)) {
    const AorB = HyCalibrationRead + (0.01248 * HyCalibrationTemp) + (0.00795 * (HyCalibrationTemp ** 2));
    total += AorB;
    count++;
  }
}

if (count > 0) {
  average = (total / count);

  for (let i = 1; i <= 9; i++) {  // Se usa el promedio del 1-5 en todos los campos del 1-9
    const field = document.getElementById("ABdependingHy50g" + i);
    if (field) field.value = average.toFixed(1);
  }
}

// Aquí continúa tu otro bucle completo (1 al 9) para calcular y mostrar los resultados:
for (let i = 1; i <= 9; i++) {
  const Temp = parseFloat(document.getElementById("Temp50g" + i).value);
  const HyReading = parseFloat(document.getElementById("HyReading50g" + i).value);
  const ReadingTimeT = parseFloat(document.getElementById("ReadingTimeT50g" + i).value);

  const ReadingTimeMin = ReadingTimeT * 60;

  if (average !== null && !isNaN(Temp)) {
    const OffsetReading = average - (0.01248 * Temp) - (0.00795 * (Temp ** 2));
    document.getElementById("OffsetReading50g" + i).value = OffsetReading.toFixed(1);

    const MassPercentFiner = 0.6226 * ((SG_Result / (SG_Result - 1)) * ((Volumeofsuspension / DryMassHyPassingGlobal) * (HyReading - OffsetReading)) * (100 / 1000));
    document.getElementById("MassPercentFiner50g" + i).value = MassPercentFiner.toFixed(2);

    const EffectiveLength = Hr2 + ((Hr1 / r1) * (r2 - HyReading + MeniscusCorrection)) - (vhb / Ac2);
    document.getElementById("EffectiveLength50g" + i).value = EffectiveLength.toFixed(2);

    const DMm = Math.sqrt(DmmHr1 * (EffectiveLength / ReadingTimeMin)) * 10;
    DMmGlobal[i - 1] = DMm;
    document.getElementById("DMm50g" + i).value = DMm.toFixed(4);

    const PassingPerceTotalSample = (PassArray[16] * MassPercentFiner) / 100;
    PassingPerceTotalSampleGlobal[i - 1] = PassingPerceTotalSample;
    document.getElementById("PassingPerceTotalSample50g" + i).value = PassingPerceTotalSample.toFixed(2);
  } else {
    document.getElementById("OffsetReading50g" + i).value = "";
  }
}

}

  $("input").on("blur", function(event) {
    event.preventDefault();
    HY();
    hydrometer50g();
  });