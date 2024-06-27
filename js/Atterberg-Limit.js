function LLyPL() {
  // Obtener Los Valores Del liquid Limit
  const NatMc = parseFloat(document.getElementById("NatMc").value) || 0;
  const Blows1 = parseFloat(document.getElementById("Blows1").value) || 0;
  const Blows2 = parseFloat(document.getElementById("Blows2").value) || 0;
  const Blows3 = parseFloat(document.getElementById("Blows3").value) || 0;
  const LLWetSoil1 = parseFloat(document.getElementById("LLWetSoil1").value) || 0;
  const LLWetSoil2 = parseFloat(document.getElementById("LLWetSoil2").value) || 0;
  const LLWetSoil3 = parseFloat(document.getElementById("LLWetSoil3").value) || 0;
  const LLDrySoilTare1 = parseFloat(document.getElementById("LLDrySoilTare1").value) || 0;
  const LLDrySoilTare2 = parseFloat(document.getElementById("LLDrySoilTare2").value) || 0;
  const LLDrySoilTare3 = parseFloat(document.getElementById("LLDrySoilTare3").value) || 0;
  const LLTare1 = parseFloat(document.getElementById("LLTare1").value) || 0;
  const LLTare2 = parseFloat(document.getElementById("LLTare2").value) || 0;
  const LLTare3 = parseFloat(document.getElementById("LLTare3").value) || 0;

  // Obtener Los Valores Del Plastic Limit
  const PLWetSoil1 = parseFloat(document.getElementById("PLWetSoil1").value) || 0;
  const PLWetSoil2 = parseFloat(document.getElementById("PLWetSoil2").value) || 0;
  const PLWetSoil3 = parseFloat(document.getElementById("PLWetSoil3").value) || 0;
  const PLDrySoilTare1 = parseFloat(document.getElementById("PLDrySoilTare1").value) || 0;
  const PLDrySoilTare2 = parseFloat(document.getElementById("PLDrySoilTare2").value) || 0;
  const PLDrySoilTare3 = parseFloat(document.getElementById("PLDrySoilTare3").value) || 0;
  const PLTare1 = parseFloat(document.getElementById("PLTare1").value) || 0;
  const PLTare2 = parseFloat(document.getElementById("PLTare2").value) || 0;
  const PLTare3 = parseFloat(document.getElementById("PLTare3").value) || 0;

  // Calculos de Liquid Limit
  const LLWater1 = LLWetSoil1 - LLDrySoilTare1;
  const LLWater2 = LLWetSoil2 - LLDrySoilTare2;
  const LLWater3 = LLWetSoil3 - LLDrySoilTare3;

  const LLWtDrySoil1 = LLDrySoilTare1 - LLTare1;
  const LLWtDrySoil2 = LLDrySoilTare2 - LLTare2;
  const LLWtDrySoil3 = LLDrySoilTare3 - LLTare3;

  const LLMCPorce1 = (LLWater1 / LLWtDrySoil1) * 100;
  const LLMCPorce2 = (LLWater2 / LLWtDrySoil2) * 100;
  const LLMCPorce3 = (LLWater3 / LLWtDrySoil3) * 100;
  
  // Calculos de Plastic Limit
  const PLWater1 = PLWetSoil1 - PLDrySoilTare1;
  const PLWater2 = PLWetSoil2 - PLDrySoilTare2;
  const PLWater3 = PLWetSoil3 - PLDrySoilTare3;

  const PLWtDrySoil1 = PLDrySoilTare1 - PLTare1;
  const PLWtDrySoil2 = PLDrySoilTare2 - PLTare2;
  const PLWtDrySoil3 = PLDrySoilTare3 - PLTare3;

  const PLMCPorce1 = (PLWater1 / PLWtDrySoil1) * 100;
  const PLMCPorce2 = (PLWater2 / PLWtDrySoil2) * 100;
  const PLMCPorce3 = (PLWater3 / PLWtDrySoil3) * 100;

  // Regresion Linear
  const Blows = [Blows1, Blows2, Blows3];
  const Moisture = [LLMCPorce1, LLMCPorce2, LLMCPorce3];
  const Xvalue = 25;

  const data = []
  for (var i = 0; i < Blows.length; i++) {
    data.push([Math.log(Blows[i]), Moisture[i]]);
  }

  const regreline =  regression.linear(data);

  const c = regreline.equation[0];
  const b = regreline.equation[1];

  //var Rsquared = regreline.r2;

  const lnx = Math.log(Xvalue);

  // Inicializar un contador y un acumulador para calcular el promedio
  let contador = 0;
  let acumulador = 0;

  // Verificar si los valores son válidos y agregarlos al acumulador
  if (!isNaN(PLMCPorce1)) {
    contador++;
    acumulador += PLMCPorce1;
  }
  if (!isNaN(PLMCPorce2)) {
    contador++;
    acumulador += PLMCPorce2;
  }
  if (!isNaN(PLMCPorce3)) {
    contador++;
    acumulador += PLMCPorce3;
  }

  // Calcular el promedio
  let PLAvgMcPorce = 0;
  if (contador > 0) {
    PLAvgMcPorce = acumulador / contador;
  }
  
  // Sumary Atterberg Limit Parameter
  const LLPorce = c * lnx + b;
  const PLPorce = PLAvgMcPorce;
  const PLIndexPorce = LLPorce - PLPorce;
  const LLIndexPorce = (NatMc-PLPorce)/PLIndexPorce;

  // Clasificacion Suelo
  function classifysoil() {
    let classify = "error";
    if (!isNaN(LLPorce) && !isNaN(PLIndexPorce)) {
      if (LLPorce < 50) {
        if (PLIndexPorce > 7 && (0.73 * (LLPorce - 20)) <= PLIndexPorce) {
          classify = "CL OR OL";
        } else if (PLIndexPorce >= 4 && (0.73 * (LLPorce - 20)) <= PLIndexPorce) {
          classify = "CL OR ML";
        } else if (PLIndexPorce < 4 || (0.73 * (LLPorce - 20)) > PLIndexPorce) {
          classify = "ML OR OL";
        }
      } else {
        if ((0.73 * (LLPorce - 20)) <= PLIndexPorce) {
          classify = "CH OR OH";
        } else {
          classify = "MH OR OH";
        }
      }
    }
    return classify;
  }

  // Pasar el resultado al input
  document.getElementById("LLWater1").textContent = LLWater1.toFixed(2);
  document.getElementById("LLWater2").textContent = LLWater2.toFixed(2);
  document.getElementById("LLWater3").textContent = LLWater3.toFixed(2);

  document.getElementById("LLWtDrySoil1").textContent = LLWtDrySoil1.toFixed(2);
  document.getElementById("LLWtDrySoil2").textContent = LLWtDrySoil2.toFixed(2);
  document.getElementById("LLWtDrySoil3").textContent = LLWtDrySoil3.toFixed(2);

  document.getElementById("LLMCPorce1").textContent = LLMCPorce1.toFixed(2);
  document.getElementById("LLMCPorce2").textContent = LLMCPorce2.toFixed(2);
  document.getElementById("LLMCPorce3").textContent = LLMCPorce3.toFixed(2);

  document.getElementById("PLWater1").textContent = PLWater1.toFixed(2);
  document.getElementById("PLWater2").textContent = PLWater2.toFixed(2);
  document.getElementById("PLWater3").textContent = PLWater3.toFixed(2);

  document.getElementById("PLWtDrySoil1").textContent = PLWtDrySoil1.toFixed(2);
  document.getElementById("PLWtDrySoil2").textContent = PLWtDrySoil2.toFixed(2);
  document.getElementById("PLWtDrySoil3").textContent = PLWtDrySoil3.toFixed(2);

  document.getElementById("PLMCPorce1").textContent = PLMCPorce1.toFixed(2);
  document.getElementById("PLMCPorce2").textContent = PLMCPorce2.toFixed(2);
  document.getElementById("PLMCPorce3").textContent = PLMCPorce3.toFixed(2);

  document.getElementById("PLAvgMcPorce").textContent = PLAvgMcPorce.toFixed(2);

  document.getElementById("LLPorce").textContent = LLPorce.toFixed(0);
  document.getElementById("PLPorce").textContent = PLPorce.toFixed(0);
  document.getElementById("PLIndexPorce").textContent = PLIndexPorce.toFixed(0);
  document.getElementById("LLIndexPorce").textContent = LLIndexPorce.toFixed(4);
  document.getElementById("classifysoil").textContent = classifysoil();
}