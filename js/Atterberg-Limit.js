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
        if (PLIndexPorce > 7 && (0.73 * (LLPorce - 30)) <= PLIndexPorce) {
          classify = "CL OR OL";
        } else if (PLIndexPorce >= 4 && (0.73 * (LLPorce - 30)) <= PLIndexPorce) {
          classify = "CL OR ML";
        } else if (PLIndexPorce < 4 || (0.73 * (LLPorce - 30)) > PLIndexPorce) {
          classify = "ML OR OL";
        }
      } else {
        if ((0.73 * (LLPorce - 30)) <= PLIndexPorce) {
          classify = "CH OR OH";
        } else {
          classify = "MH OR OH";
        }
      }
    }
    return classify;
  }

  var xValues = [Blows1, Blows2, Blows3];
  var yValues = [LLMCPorce1, LLMCPorce2, LLMCPorce3];
 
  var lnXValues = xValues.map(Math.log);
  var sumlnX2 = lnXValues.reduce((acc, val) => acc + Math.pow(val, 2), 0);
  var sumYlnX = yValues.reduce((acc, val, i) => acc + val * lnXValues[i], 0);
  var m = (xValues.length * sumYlnX - lnXValues.reduce((acc, val) => acc + val, 0) * yValues.reduce((acc, val) => acc + val, 0)) /
         (xValues.length * sumlnX2 - Math.pow(lnXValues.reduce((acc, val) => acc + val, 0), 2));
  var n = (yValues.reduce((acc, val) => acc + val, 0) - m * lnXValues.reduce((acc, val) => acc + val, 0)) / xValues.length;
 
  var SSE = 0;
  var SST = 0;
 
  for (var i = 0; i < xValues.length; i++) {
   var yPredicted = m * Math.log(xValues[i]) + n;
   SSE += Math.pow(yValues[i] - yPredicted, 2);
   SST += Math.pow(yValues[i] - yValues.reduce((acc, val) => acc + val, 0) / xValues.length, 2);
  }
  var rSquare = 1 - SSE / SST;

  // Pasar el resultado al input
  document.getElementById("LLWater1").value = LLWater1.toFixed(2);
  document.getElementById("LLWater2").value = LLWater2.toFixed(2);
  document.getElementById("LLWater3").value = LLWater3.toFixed(2);

  document.getElementById("LLWtDrySoil1").value = LLWtDrySoil1.toFixed(2);
  document.getElementById("LLWtDrySoil2").value = LLWtDrySoil2.toFixed(2);
  document.getElementById("LLWtDrySoil3").value = LLWtDrySoil3.toFixed(2);

  document.getElementById("LLMCPorce1").value = LLMCPorce1.toFixed(2);
  document.getElementById("LLMCPorce2").value = LLMCPorce2.toFixed(2);
  document.getElementById("LLMCPorce3").value = LLMCPorce3.toFixed(2);

  document.getElementById("PLWater1").value = PLWater1.toFixed(2);
  document.getElementById("PLWater2").value = PLWater2.toFixed(2);
  document.getElementById("PLWater3").value = PLWater3.toFixed(2);

  document.getElementById("PLWtDrySoil1").value = PLWtDrySoil1.toFixed(2);
  document.getElementById("PLWtDrySoil2").value = PLWtDrySoil2.toFixed(2);
  document.getElementById("PLWtDrySoil3").value = PLWtDrySoil3.toFixed(2);

  document.getElementById("PLMCPorce1").value = PLMCPorce1.toFixed(2);
  document.getElementById("PLMCPorce2").value = PLMCPorce2.toFixed(2);
  document.getElementById("PLMCPorce3").value = PLMCPorce3.toFixed(2);

  document.getElementById("PLAvgMcPorce").value = PLAvgMcPorce.toFixed(2);

  document.getElementById("LLPorce").value = LLPorce.toFixed(0);
  document.getElementById("PLPorce").value = PLPorce.toFixed(0);
  document.getElementById("PLIndexPorce").value = PLIndexPorce.toFixed(0);
  document.getElementById("LLIndexPorce").value = LLIndexPorce.toFixed(4);
  document.getElementById("classifysoil").value = classifysoil();
  document.getElementById("Rsquared").value = rSquare.toFixed(4);
}

  $("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Liquid-Limit-Plot.js",
      type: "POST",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
    $.ajax({
      url: "../libs/graph/Plasticity-Chart.js",
      type: "POST",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function search() {
    var ID = $('#SampleName').val();
    var Number = $('#SampleNumber').val();

    console.log("ID:", ID); // Imprime el valor de ID en la consola
    console.log("Number:", Number); 

    $.ajax({
      type: 'POST',
      url: '../php/ajax-search.php',
      data: { ID: ID, Number: Number },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#mensaje-container').html(response.message).fadeIn();
          
          setTimeout(function() {
            $('#mensaje-container').fadeOut();
          }, 2000);
          
          $('#NatMc').val(response.mc_value);
        } else {
          $('#mensaje-container').html(response.message).fadeIn();

          setTimeout(function() {
            $('#mensaje-container').fadeOut();
          }, 2000);
        }
      }
    });
  }


  function actualizarImagen() {
    var liquidLimit = echarts.getInstanceByDom(document.getElementById('liquid-limit'));
    var plasticityChart = echarts.getInstanceByDom(document.getElementById('PlasticityChart'));
  
    var liquidLimitImageURL = liquidLimit.getDataURL({
      pixelRatio: 1,
      backgroundColor: '#fff'
    });
  
    var plasticityChartImageURL = plasticityChart.getDataURL({
      pixelRatio: 1,
      backgroundColor: '#fff'
    });
  
    Promise.all([
        fetch(liquidLimitImageURL).then(response => response.blob()),
        fetch(plasticityChartImageURL).then(response => response.blob())
      ])
      .then(([liquidLimitBlob, plasticityChartBlob]) => {
        return Promise.all([
          new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(liquidLimitBlob);
          }),
          new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(plasticityChartBlob);
          })
        ]);
      })
      .then(([liquidLimitBase64, plasticityChartBase64]) => {
        document.getElementById('PlotLimit').value = liquidLimitBase64;
        document.getElementById('PlotPlasticity').value = plasticityChartBase64;
      })
      .catch(error => console.error('Error al convertir la imagen a Base64:', error));
  }
  document.querySelectorAll('input').forEach(input => {
    input.addEventListener('blur', actualizarImagen);
  });