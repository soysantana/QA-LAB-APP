  /**
   * Function Grain Size
   */

function GrainSize() {
  const cumRetArray = [0];
  const PassArray = [];

  for (let i = 1; i <= 22; i++) {
    // Obtener los valores
    const DrySoilTare = parseFloat(document.getElementById("DrySoilTare").value);
    const Tare = parseFloat(document.getElementById("Tare").value);
    const Washed = parseFloat(document.getElementById("Washed").value);
    const WtRet = parseFloat(document.getElementById("WtRet" + i).value) || 0;
    const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);

    // Calculation
    const DrySoil = DrySoilTare-Tare;
    const WashPan = DrySoil-Washed;

    // Grain Size Distribution
    const Ret = (WtRet / DrySoil) * 100;
    const CumRet = cumRetArray[i - 1] + Ret;
    cumRetArray.push(CumRet);
    const Pass = 100 - CumRet;
    const PanRet = (PanWtRen / DrySoil) * 100;
    const TotalWtRet = PanWtRen + WashPan;
    const TotalRet = (TotalWtRet / DrySoil) * 100;
    const TotalCumRet = cumRetArray[22] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    // Summary Grain Size Distribution Parameter
    PassArray.push(Pass);
    const CoarserGravel =  100 - PassArray[5];
    const Gravel =  PassArray[5] - PassArray[13];
    const Sand =  PassArray[13] - PassArray[21];
    const Fines =  PassArray[PassArray.length - 1];   

    // Result
    document.getElementById("DrySoil").value = isNaN(DrySoil) || DrySoil === 0 ? "" : DrySoil.toFixed(2);
    document.getElementById("WashPan").value = isNaN(WashPan) || WashPan === 0 ? "" : WashPan.toFixed(2);
    document.getElementById("Ret" + i).value = isNaN(Ret) || Ret === 0 ? "" : Ret.toFixed(2);
    document.getElementById("CumRet" + i).value = isNaN(CumRet) || CumRet === 0 ? "" : CumRet.toFixed(2);
    document.getElementById("Pass" + i).value = isNaN(Pass) || Pass === 0 ? "" : Pass.toFixed(2);
    document.getElementById("PanRet").value = isNaN(PanRet) || PanRet === 0 ? "" : PanRet.toFixed(2);
    document.getElementById("TotalWtRet").value = isNaN(TotalWtRet) || TotalWtRet === 0 ? "" : TotalWtRet.toFixed(2);
    document.getElementById("TotalRet").value = isNaN(TotalRet) || TotalRet === 0 ? "" : TotalRet.toFixed(2);
    document.getElementById("TotalCumRet").value = isNaN(TotalCumRet) || TotalCumRet === 0 ? "" : TotalCumRet.toFixed(2);
    document.getElementById("TotalPass").value = isNaN(TotalPass) || TotalPass === 0 ? "" : TotalPass.toFixed(2);
    document.getElementById("CoarserGravel").value = isNaN(CoarserGravel) || CoarserGravel === 0 ? "" : CoarserGravel.toFixed(2);
    document.getElementById("Gravel").value = isNaN(Gravel) || Gravel === 0 ? "" : Gravel.toFixed(2);
    document.getElementById("Sand").value = isNaN(Sand) || Sand === 0 ? "" : Sand.toFixed(2);
    document.getElementById("Fines").value = isNaN(Fines) || Fines === 0 ? "" : Fines.toFixed(2);
  }
  // Sumary Parameter
 const datos = [
    [PassArray[21], 0.075, PassArray[20], 0.106],
    [PassArray[20], 0.106, PassArray[19], 0.15],
    [PassArray[19], 0.15, PassArray[18], 0.25],
    [PassArray[18], 0.25, PassArray[17], 0.3],
    [PassArray[17], 0.3, PassArray[16], 0.85],
    [PassArray[16], 0.85, PassArray[15], 1.18],
    [PassArray[15], 1.18, PassArray[14], 2.00],
    [PassArray[14], 2.00, PassArray[13], 4.75],
    [PassArray[13], 4.75, PassArray[12], 9.50],
    [PassArray[12], 9.50, PassArray[11], 12.50],
    [PassArray[11], 12.50, PassArray[10], 19.00],
    [PassArray[10], 19.00, PassArray[9], 25.00],
    [PassArray[9], 25.00, PassArray[8], 38.10],
    [PassArray[8], 38.10, PassArray[7], 50.80],
    [PassArray[7], 50.80, PassArray[6], 63.50],
    [PassArray[6], 63.50, PassArray[5], 76.20],
    [PassArray[5], 76.20, PassArray[4], 88.90],
    [PassArray[4], 88.90, PassArray[3], 101.60],
    [PassArray[3], 101.60, PassArray[2], 127],
    [PassArray[2], 127, PassArray[1], 152.4],
    [PassArray[1], 152.4, PassArray[0], 200],
    [PassArray[0], 200, 0.0, 0.0]
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

  $("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Grain-Size-General.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
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