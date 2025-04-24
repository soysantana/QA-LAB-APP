/**
   * Function Sepecific Gravity Soil
   */

function SGSOIL() {
  // Obtener el valor de la temperatura del test
  const TestTemp = parseFloat(document.getElementById("TestTemp").value);
  const MassDryPycn = parseFloat(document.getElementById("MassDryPycn").value);
  const PycnWaterTemp = parseFloat(document.getElementById("PycnWaterTemp").value);
  const WeightTare = parseFloat(document.getElementById("WeightTare").value);
  const WeightPycnSoilWaterMpws = parseFloat(document.getElementById("WeightPycnSoilWaterMpws").value);
  const TestTempAfter = parseFloat(document.getElementById("TestTempAfter").value);

  // Temp, Density Temp Coefficient
  const Table = [
    [15.0, 0.9991, 1.00090],
    [15.1, 0.99909, 1.00088],
    [15.2, 0.99907, 1.00087],
    [15.3, 0.99906, 1.00085],
    [15.4, 0.99904, 1.00084],
    [15.5, 0.99902, 1.00082],
    [15.6, 0.99901, 1.00080],
    [15.7, 0.99899, 1.00079],
    [15.8, 0.99896, 1.00077],
    [15.9, 0.99896, 1.00076],
    [16.0, 0.99895, 1.00074],
    [16.1, 0.99893, 1.00072],
    [16.2, 0.99891, 1.00071],
    [16.3, 0.9989, 1.00069],
    [16.4, 0.99888, 1.00067],
    [16.5, 0.99886, 1.00066],
    [16.6, 0.99885, 1.00064],
    [16.7, 0.99883, 1.00062],
    [16.8, 0.99881, 1.00061],
    [16.9, 0.99879, 1.00059],
    [17.0, 0.99878, 1.00057],
    [17.1, 0.99876, 1.00055],
    [17.2, 0.99874, 1.00054],
    [17.3, 0.99872, 1.00052],
    [17.4, 0.99871, 1.00050],
    [17.5, 0.99869, 1.00048],
    [17.6, 0.99867, 1.00047],
    [17.7, 0.99865, 1.00045],
    [17.8, 0.99863, 1.00043],
    [17.9, 0.99862, 1.00041],
    [18.0, 0.9986, 1.00039],
    [18.1, 0.99858, 1.00037],
    [18.2, 0.99856, 1.00035],
    [18.3, 0.99854, 1.00034],
    [18.4, 0.99852, 1.00032],
    [18.5, 0.9985, 1.00030],
    [18.6, 0.99848, 1.00028],
    [18.7, 0.99847, 1.00026],
    [18.8, 0.99845, 1.00024],
    [18.9, 0.99843, 1.00022],
    [19.0, 0.99841, 1.0002],
    [19.1, 0.99839, 1.00018],
    [19.2, 0.99837, 1.00016],
    [19.3, 0.99835, 1.00014],
    [19.4, 0.99833, 1.00012],
    [19.5, 0.99831, 1.0001],
    [19.6, 0.99829, 1.00008],
    [19.7, 0.99827, 1.00006],
    [19.8, 0.99825, 1.00004],
    [19.9, 0.99823, 1.00002],
    [20.0, 0.99821, 1.00000],
    [20.1, 0.99819, 0.99998],
    [20.2, 0.99816, 0.99996],
    [20.3, 0.99814, 0.99994],
    [20.4, 0.99812, 0.99992],
    [20.5, 0.9981, 0.99990],
    [20.6, 0.99808, 0.99987],
    [20.7, 0.99806, 0.99850],
    [20.8, 0.99804, 0.99983],
    [20.9, 0.99802, 0.99981],
    [21.0, 0.99799, 0.99979],
    [21.1, 0.99797, 0.99977],
    [21.2, 0.99795, 0.99974],
    [21.3, 0.99793, 0.99972],
    [21.4, 0.99791, 0.99970],
    [21.5, 0.99789, 0.99968],
    [21.6, 0.99786, 0.99966],
    [21.7, 0.99784, 0.99963],
    [21.8, 0.99782, 0.99961],
    [21.9, 0.99780, 0.99959],
    [22.0, 0.99777, 0.99957],
    [22.1, 0.99775, 0.99954],
    [22.2, 0.99773, 0.99952],
    [22.3, 0.9977, 0.9995],
    [22.4, 0.99768, 0.99947],
    [22.5, 0.99766, 0.99945],
    [22.6, 0.99764, 0.99943],
    [22.7, 0.99761, 0.9994],
    [22.8, 0.99759, 0.99938],
    [22.9, 0.99756, 0.99936],
    [23.0, 0.99754, 0.99933],
    [23.1, 0.99752, 0.99931],
    [23.2, 0.99749, 0.99929],
    [23.3, 0.99747, 0.99926],
    [23.4, 0.99745, 0.99924],
    [23.5, 0.99742, 0.99921],
    [23.6, 0.99740, 0.99919],
    [23.7, 0.99737, 0.99917],
    [23.8, 0.99735, 0.99914],
    [23.9, 0.99732, 0.99912],
    [24.0, 0.9973, 0.99909],
    [24.1, 0.99727, 0.99907],
    [24.2, 0.99725, 0.99904],
    [24.3, 0.99723, 0.99902],
    [24.4, 0.9972, 0.99899],
    [24.5, 0.99717, 0.99897],
    [24.6, 0.99715, 0.99894],
    [24.7, 0.99712, 0.99892],
    [24.8, 0.9971, 0.99889],
    [24.9, 0.99707, 0.99889],
    [25.0, 0.99705, 0.99884],
    [25.1, 0.99702, 0.99881],
    [25.2, 0.99700, 0.99879],
    [25.3, 0.99697, 0.99876],
    [25.4, 0.99694, 0.99874],
    [25.5, 0.99692, 0.99871],
    [25.6, 0.99689, 0.99868],
    [25.7, 0.99687, 0.99866],
    [25.8, 0.99684, 0.99863],
    [25.9, 0.99681, 0.9986],
    [26.0, 0.99679, 0.99858],
    [26.1, 0.99676, 0.99855],
    [26.2, 0.99674, 0.99852],
    [26.3, 0.99671, 0.99850],
    [26.4, 0.99668, 0.99847],
    [26.5, 0.99665, 0.99844],
    [26.6, 0.99663, 0.99842],
    [26.7, 0.9966, 0.99839],
    [26.8, 0.99657, 0.99836],
    [26.9, 0.99654, 0.99833],
    [27.0, 0.99652, 0.99831],
    [27.1, 0.99649, 0.99828],
    [27.2, 0.99646, 0.99825],
    [27.3, 0.99643, 0.99822],
    [27.4, 0.99641, 0.99820],
    [27.5, 0.99638, 0.99817],
    [27.6, 0.99635, 0.99814],
    [27.7, 0.99632, 0.99811],
    [27.8, 0.99629, 0.99808],
    [27.9, 0.99627, 0.99806],
    [28.0, 0.99624, 0.99803],
    [28.1, 0.99621, 0.99800],
    [28.2, 0.99618, 0.99797],
    [28.3, 0.99615, 0.99794],
    [28.4, 0.99612, 0.99791],
    [28.5, 0.99609, 0.99788],
    [28.6, 0.99607, 0.99785],
    [28.7, 0.99604, 0.99783],
    [28.8, 0.99601, 0.99780],
    [28.9, 0.99598, 0.99777],
    [29.0, 0.99595, 0.99774],
    [29.1, 0.99592, 0.99771],
    [29.2, 0.99589, 0.99768],
    [29.3, 0.99586, 0.99765],
    [29.4, 0.99583, 0.99762],
    [29.5, 0.9958, 0.99759],
    [29.6, 0.99577, 0.99756],
    [29.7, 0.99574, 0.99753],
    [29.8, 0.99571, 0.99750],
    [29.9, 0.99568, 0.99747],
    [30.0, 0.99565, 0.99744],
    [30.1, 0.99562, 0.99741],
    [30.2, 0.99559, 0.99738],
    [30.3, 0.99556, 0.99735],
    [30.4, 0.99553, 0.99732],
    [30.5, 0.9955, 0.99729],
    [30.6, 0.99547, 0.99726],
    [30.7, 0.99544, 0.99723],
    [30.8, 0.99541, 0.99720],
    [30.9, 0.99538, 0.99716],
  ];
  
  // Calculation
  const DensityTable = Table.find(entry => entry[0] === TestTemp);
  const DensityWaterTempAfter = Table.find(entry => entry[0] === TestTempAfter);
  

  if (DensityTable && DensityWaterTempAfter) {
    const WeightSoil = WeightTare-MassDryPycn;
    const VolumePycn = (PycnWaterTemp-MassDryPycn)/DensityTable[1];
    const PycnWaterTempAfter = ((PycnWaterTemp - MassDryPycn) * (DensityWaterTempAfter[1] / DensityTable[1])) + MassDryPycn;
    const SgSoilTemp = WeightSoil/(PycnWaterTempAfter-(WeightPycnSoilWaterMpws-WeightSoil));
    const SgSolid = DensityWaterTempAfter[2]*SgSoilTemp;

    // Result
    document.getElementById("DensityWaterTemp").value = DensityTable[1].toFixed(5);
    document.getElementById("TempCoefficent").value = DensityWaterTempAfter[2].toFixed(5);
    document.getElementById("VolumePycn").value = VolumePycn.toFixed(0);
    document.getElementById("WeightSoil").value = WeightSoil.toFixed(2);
    document.getElementById("SgSoilTemp").value = SgSoilTemp.toFixed(2);
    document.getElementById("SgSolid").value = SgSolid.toFixed(2);
    document.getElementById("DensityWaterTempAfter").value = DensityWaterTempAfter[1].toFixed(5);
    document.getElementById("PycnWaterTempAfter").value = PycnWaterTempAfter.toFixed(2);
  } else {
    document.getElementById("DensityWaterTemp").value = "Oops! Hubo un error";
    document.getElementById("DensityWaterTempAfter").value = "Oops! Hubo un error";
  }
}

/**
   * Function Sepecific Gravity Coarse
   */

function SGCOARSE() {
  let totalOvenDry = 0;
  let totalSurfaceDry = 0;
  let totalSampImmers = 0;

  for (let i = 1; i <= 9; i++) {
    // Obtener los valores
    const ovenDry = parseFloat(document.getElementById("OvenDry" + i).value) || 0;
    const surfaceDry = parseFloat(document.getElementById("SurfaceDry" + i).value) || 0;
    const sampImmers = parseFloat(document.getElementById("SampImmers" + i).value) || 0;

    // Calcular totales
    totalOvenDry += ovenDry;
    totalSurfaceDry += surfaceDry;
    totalSampImmers += sampImmers;
    SpecificGravityOD = (totalOvenDry/(totalSurfaceDry-totalSampImmers));
    SpecificGravitySSD = (totalSurfaceDry/(totalSurfaceDry-totalSampImmers));
    ApparentSpecificGravity = (totalOvenDry/(totalOvenDry-totalSampImmers));
    PercentAbsortion = ((totalSurfaceDry-totalOvenDry)/totalOvenDry)*100;
  }

  // Dar formato a los resultados
  const formattedOvenDry = totalOvenDry.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  const formattedSurfaceDry = totalSurfaceDry.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  const formattedSampImmers = totalSampImmers.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

  // Asignar los resultados formateados a los elementos correspondientes
  document.getElementById("OvenDry10").value = formattedOvenDry;
  document.getElementById("SurfaceDry10").value = formattedSurfaceDry;
  document.getElementById("SampImmers10").value = formattedSampImmers;
  document.getElementById("SpecificGravityOD").value = SpecificGravityOD.toFixed(2);
  document.getElementById("SpecificGravitySSD").value = SpecificGravitySSD.toFixed(2);
  document.getElementById("ApparentSpecificGravity").value = ApparentSpecificGravity.toFixed(2);
  document.getElementById("PercentAbsortion").value = PercentAbsortion.toFixed(2);
}

/**
   * Function Sepecific Gravity Fine
   */

function SGFINE() {
  //
  const WeightDry = parseFloat(document.getElementById("WeightDry").value);
  const WeightSurfaceAir = parseFloat(document.getElementById("WeightSurfaceAir").value);
  const WeightPycnoWater = parseFloat(document.getElementById("WeightPycnoWater").value);
  const CalibrationPycno = parseFloat(document.getElementById("CalibrationPycno").value);

  // Calculation
  const SpecificGravityOD = (WeightDry/(CalibrationPycno+WeightSurfaceAir-WeightPycnoWater))*0.99994;
  const SpecificGravitySSD = (WeightSurfaceAir/(CalibrationPycno+WeightSurfaceAir-WeightPycnoWater))*0.99994;
  const ApparentSpecificGravity = (WeightDry/(CalibrationPycno+WeightDry-WeightPycnoWater))*0.99994;
  const PercentAbsortion = (WeightSurfaceAir-WeightDry)/WeightDry*100;

  // Asignar los resultados a los elementos correspondientes
  document.getElementById("SpecificGravityOD").value = SpecificGravityOD.toFixed(2);
  document.getElementById("SpecificGravitySSD").value = SpecificGravitySSD.toFixed(2);
  document.getElementById("ApparentSpecificGravity").value = ApparentSpecificGravity.toFixed(2);
  document.getElementById("PercentAbsortion").value = PercentAbsortion.toFixed(2);
}