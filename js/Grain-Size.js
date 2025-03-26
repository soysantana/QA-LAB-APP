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

  /**
   * Function Grain Size Fine Filter
   */

  function FineFilter() {
    const cumRetArray = [0];
    const PassArray = [];
 
    // Array para almacenar los IDs de los elementos del DOM
    const specElements = [
       "Specs11",
       "Specs12",
       "Specs13",
       "Specs14",
       "Specs16",
       "Specs17",
       "Specs18",
    ];
 
    const specsType = document.getElementById("specsType");
 
    // Especificaciones de investigación y agregado
    const specs = {
       I: {
          Specs11: "100",
          Specs12: "95-100",
          Specs13: "75-100",
          Specs14: "50-85",
          Specs16: "5-30",
          Specs17: "0-25",
          Specs18: "0-1.7",
       },
       C: {
          Specs11: "100",
          Specs12: "95-100",
          Specs13: "65-100",
          Specs14: "50-85",
          Specs16: "5-30",
          Specs17: "0-25",
          Specs18: "0-5",
       },
       N: {
          // Aquí puedes poner las especificaciones para "Naranjo"
       },
       A: {
          // Aquí puedes poner las especificaciones para "Acopio"
       },
    };
 
    // Función que actualiza los valores de las especificaciones dinámicamente
    function updateSpecs(selectedType) {
       const selectedSpecs = specs[selectedType];
 
       specElements.forEach((id) => {
          document.getElementById(id).value = selectedSpecs[id];
       });
    }
 
    // Evento que detecta cambios en el select
    specsType.addEventListener("change", function () {
       // Obtener el valor seleccionado ("Investigacion" o "agregado")
       const selectedValue = specsType.value;
 
       // Llamar a la función para actualizar las especificaciones
       updateSpecs(selectedValue);
    });
 
    for (let i = 1; i <= 18; i++) {
       // Obtener los valores
       const DrySoilTare = parseFloat(
          document.getElementById("DrySoilTare").value,
       );
       const Tare = parseFloat(document.getElementById("Tare").value);
       const Washed = parseFloat(document.getElementById("Washed").value);
       const WtRet = parseFloat(document.getElementById("WtRet" + i).value) || 0;
       const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);
 
       // Calculation
       const DrySoil = DrySoilTare - Tare;
       const WashPan = DrySoil - Washed;
 
       // Grain Size Distribution
       const Ret = (WtRet / DrySoil) * 100;
       const CumRet = cumRetArray[i - 1] + Ret;
       cumRetArray.push(CumRet);
       const Pass = 100 - CumRet;
       const PanRet = (PanWtRen / DrySoil) * 100;
       const TotalWtRet = PanWtRen + WashPan;
       const TotalRet = (TotalWtRet / DrySoil) * 100;
       const TotalCumRet = cumRetArray[18] + TotalRet;
       const TotalPass = 100 - TotalCumRet;
 
       // Summary Grain Size Distribution Parameter
       PassArray.push(Pass);
       const CoarserGravel = 100 - PassArray[3];
       const Gravel = PassArray[3] - PassArray[11];
       const Sand = PassArray[11] - PassArray[17];
       const Fines = PassArray[PassArray.length - 1];
 
       // Result
       document.getElementById("DrySoil").value = DrySoil.toFixed(2);
       document.getElementById("WashPan").value = WashPan.toFixed(2);
       document.getElementById("Ret" + i).value = Ret.toFixed(2);
       document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
       document.getElementById("Pass" + i).value = Pass.toFixed(2);
       document.getElementById("PanRet").value = PanRet.toFixed(2);
       document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
       document.getElementById("TotalRet").value = TotalRet.toFixed(2);
       document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
       document.getElementById("TotalPass").value = TotalPass.toFixed(2);
       document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
       document.getElementById("Gravel").value = Gravel.toFixed(2);
       document.getElementById("Sand").value = Sand.toFixed(2);
       document.getElementById("Fines").value = Fines.toFixed(2);
    }
 
    // Reactivity Test Method FM13-006
    var total = 0;
    var count = 0;
 
    // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
    for (var i = 1; i <= 5; i++) {
       var elementId = "Particles" + i;
       var element = document.getElementById(elementId);
 
       if (element && !isNaN(parseFloat(element.value))) {
          // Convierte el contenido del input a un número y agrégalo al total
          total += parseFloat(element.value);
          count++;
       }
    }
    if (count >= 1) {
       var avgParticles = total / count;
       var reactionResult;
       var AcidResult;
 
       // Reaction Strength Result:
       if (avgParticles >= 30) {
          reactionResult = "Strong Reaction";
       } else if (avgParticles >= 16 && avgParticles <= 30) {
          reactionResult = "Moderate Reaction";
       } else if (avgParticles >= 1 && avgParticles <= 15) {
          reactionResult = "Weak Reaction";
       } else {
          reactionResult = "No Reaction";
       }
       // Acid Reactivity Test Result
       if (reactionResult === "No Reaction") {
          AcidResult = "Accepted";
       } else if (
          reactionResult === "Weak Reaction" ||
          reactionResult === "Moderate Reaction"
       ) {
          AcidResult = "Accepted";
       } else {
          AcidResult = "Rejected";
       }
 
       document.getElementById("AcidResult").value = AcidResult;
       document.getElementById("ReactionResult").value = reactionResult;
       document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
    }
 
    // Sumary Parameter
    const datos = [
       [PassArray[17], 0.075, PassArray[16], 0.25],
       [PassArray[16], 0.25, PassArray[15], 0.3],
       [PassArray[15], 0.3, PassArray[14], 0.85],
       [PassArray[14], 0.85, PassArray[13], 1.18],
       [PassArray[13], 1.18, PassArray[12], 2.0],
       [PassArray[12], 2.0, PassArray[11], 4.75],
       [PassArray[11], 4.75, PassArray[10], 9.5],
       [PassArray[10], 9.5, PassArray[9], 12.5],
       [PassArray[9], 12.5, PassArray[8], 19.0],
       [PassArray[8], 19.0, PassArray[7], 25.0],
       [PassArray[7], 25.0, PassArray[6], 38.1],
       [PassArray[6], 38.1, PassArray[5], 50.8],
       [PassArray[5], 50.8, PassArray[4], 63.5],
       [PassArray[4], 63.5, PassArray[3], 76.2],
       [PassArray[3], 76.2, PassArray[2], 88.9],
       [PassArray[2], 88.9, PassArray[1], 101.6],
       [PassArray[1], 101.6, PassArray[0], 127],
       [PassArray[0], 127, 0.0, 0.0],
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
    var b =
       datosY10.reduce((a, b) => a + b, 0) / datosY10.length -
       (c * datosXln.reduce((a, b) => a + b, 0)) / datosXln.length;
 
    // Calcular la expresión
    const D10 = Math.exp((10 - b) / c);
 
    const datosY15 = [resultados[1][0], resultados[1][2]];
    const datosX15 = [resultados[1][1], resultados[1][3]];
 
    // Calcular el logaritmo natural de los datos X
    const datosXln15 = datosX15.map(Math.log);
 
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c15 = (datosY15[1] - datosY15[0]) / (datosXln15[1] - datosXln15[0]);
 
    // Calcular b
    const b15 =
       datosY15.reduce((a, b) => a + b, 0) / datosY15.length -
       (c15 * datosXln15.reduce((a, b) => a + b, 0)) / datosXln15.length;
 
    // Calcular la expresión
    const D15 = Math.exp((15 - b15) / c15);
 
    const datosY30 = [resultados[2][0], resultados[2][2]];
    const datosX30 = [resultados[2][1], resultados[2][3]];
 
    // Calcular el logaritmo natural de los datos X
    const datosXln30 = datosX30.map(Math.log);
 
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c30 = (datosY30[1] - datosY30[0]) / (datosXln30[1] - datosXln30[0]);
 
    // Calcular b
    const b30 =
       datosY30.reduce((a, b) => a + b, 0) / datosY30.length -
       (c30 * datosXln30.reduce((a, b) => a + b, 0)) / datosXln30.length;
 
    // Calcular la expresión
    const D30 = Math.exp((30 - b30) / c30);
 
    const datosY60 = [resultados[3][0], resultados[3][2]];
    const datosX60 = [resultados[3][1], resultados[3][3]];
 
    // Calcular el logaritmo natural de los datos X
    const datosXln60 = datosX60.map(Math.log);
 
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c60 = (datosY60[1] - datosY60[0]) / (datosXln60[1] - datosXln60[0]);
 
    // Calcular b
    const b60 =
       datosY60.reduce((a, b) => a + b, 0) / datosY60.length -
       (c60 * datosXln60.reduce((a, b) => a + b, 0)) / datosXln60.length;
 
    // Calcular la expresión
    const D60 = Math.exp((60 - b60) / c60);
 
    const datosY85 = [resultados[4][0], resultados[4][2]];
    const datosX85 = [resultados[4][1], resultados[4][3]];
 
    // Calcular el logaritmo natural de los datos X
    const datosXln85 = datosX85.map(Math.log);
 
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c85 = (datosY85[1] - datosY85[0]) / (datosXln85[1] - datosXln85[0]);
 
    // Calcular b
    const b85 =
       datosY85.reduce((a, b) => a + b, 0) / datosY85.length -
       (c85 * datosXln85.reduce((a, b) => a + b, 0)) / datosXln85.length;
 
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
       Cc = D30 ** 2 / (D60 * D10);
       Cu = D60 / D10;
    } else {
       Cc = "-";
       Cu = "-";
    }
 
    if (D30 <= umbral || D60 <= umbral || D10 <= umbral) {
       Cc = "-";
       Cu = "-";
    }
 
    document.getElementById("Cc").value =
       Cc !== "-" ? parseFloat(Cc.toFixed(2)) : "-";
    document.getElementById("Cu").value =
       Cu !== "-" ? parseFloat(Cu.toFixed(2)) : "-";
 
    $("input").on("blur", function (event) {
       event.preventDefault();
       enviarData();
    });
 
    function enviarData() {
       $.ajax({
          url: "../libs/graph/Grain-Size-Fine.js",
          type: "GET",
          data: $("#nopasonada").serialize(),
          success: function (data) {},
       });
    }
 
    let isUpdating = false; // Variable de control para evitar múltiples ejecuciones

    function actualizarImagen() {
        if (isUpdating) return; // Si ya se está actualizando, salir
        isUpdating = true; // Establecer que se está actualizando
    
        requestAnimationFrame(() => {
            var GrainSizeGeneral = echarts.getInstanceByDom(
                document.getElementById("GrainSizeFineFilter")
            );
    
            var ImageURL = GrainSizeGeneral.getDataURL({
                pixelRatio: 1,
                backgroundColor: "#fff",
            });
    
            fetch(ImageURL)
                .then((response) => response.blob())
                .then((GraphBlob) => {
                    // Convierte la imagen a base64
                    return new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.onerror = reject;
                        reader.readAsDataURL(GraphBlob);
                    });
                })
                .then((GraphBase64) => {
                    document.getElementById("Graph").value = GraphBase64;
                })
                .catch((error) => {
                    console.error("Error al convertir la imagen a Base64:", error);
                })
                .finally(() => {
                    isUpdating = false; // Restablecer el estado al finalizar la actualización
                });
        });
    }
    
    document.querySelectorAll("input").forEach((input) => {
        input.addEventListener("blur", actualizarImagen);
    });
    
 }

  /**
   * Function Grain Size Coarse Aggregate
   */

function CoarseAgg() {
  const cumRetArray = [0];
  const PassArray = [];

    var Specs1p5 = document.getElementById("Specs7");
    var Specs1 = document.getElementById("Specs8");
    var Specs3p4 = document.getElementById("Specs9");
    var Specs3p8 = document.getElementById("Specs10");
    var SpecsNo4 = document.getElementById("Specs11");
    var SpecsNo10 = document.getElementById("Specs12");
    var SpecsNo20 = document.getElementById("Specs14");
    var SpecsNo200 = document.getElementById("Specs17");
  
    Specs1p5.value = "100";
    Specs1.value = "87-100";
    Specs3p4.value = "80-100";
    Specs3p8.value = "40-100";
    SpecsNo4.value = "7-60";
    SpecsNo10.value = "0-15";
    SpecsNo20.value = "0-7";
    SpecsNo200.value = "0-1.7";

    for (let i = 1; i <= 17; i++) {
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
      const TotalCumRet = cumRetArray[17] + TotalRet;
      const TotalPass = 100 - TotalCumRet;
  
      // Summary Grain Size Distribution Parameter
      PassArray.push(Pass);
      const CoarserGravel =  100 - PassArray[3]; // 3"
      const Gravel =  PassArray[3] - PassArray[10]; // No4
      const Sand =  PassArray[10] - PassArray[16]; // No200
      const Fines =  PassArray[PassArray.length - 1];   
    
  
      // Result
      document.getElementById("DrySoil").value = DrySoil.toFixed(2);
      document.getElementById("WashPan").value = WashPan.toFixed(2);
      document.getElementById("Ret" + i).value = Ret.toFixed(2);
      document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
      document.getElementById("Pass" + i).value = Pass.toFixed(2);
      document.getElementById("PanRet").value = PanRet.toFixed(2);
      document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
      document.getElementById("TotalRet").value = TotalRet.toFixed(2);
      document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
      document.getElementById("TotalPass").value = TotalPass.toFixed(2);
      document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
      document.getElementById("Gravel").value = Gravel.toFixed(2);
      document.getElementById("Sand").value = Sand.toFixed(2);
      document.getElementById("Fines").value = Fines.toFixed(2);
    }

    // Reactivity Test Method FM13-006
    var total = 0;
    var count = 0;

    // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
    for (var i = 1; i <= 3; i++) {
      var elementId = "Particles" + i;
      var element = document.getElementById(elementId);

      if (element && !isNaN(parseFloat(element.value))) {
        // Convierte el contenido del input a un número y agrégalo al total
        total += parseFloat(element.value);
        count++;
      }
    }
    if (count >= 1) {
      var avgParticles = total / count;
      var reactionResult;
      var AcidResult;

      // Reaction Strength Result:
      if (avgParticles >= 30) {
        reactionResult = "Strong Reaction";
      } else if (avgParticles >= 16 && avgParticles <= 30) {
        reactionResult = "Moderate Reaction";
      } else if (avgParticles >= 1 && avgParticles <= 15) {
        reactionResult = "Weak Reaction";
      } else {
        reactionResult = "No Reaction";
      }
      // Acid Reactivity Test Result
      if (reactionResult === "No Reaction") {
        AcidResult = "Accepted";
      } else if (reactionResult === "Weak Reaction" || reactionResult === "Moderate Reaction") {
        AcidResult = "Accepted";
      } else {
        AcidResult = "Rejected";
      }

      document.getElementById("AcidResult").value = AcidResult;
      document.getElementById("ReactionResult").value = reactionResult;
      document.getElementById("AvgParticles").value = avgParticles;
    }

  // Sumary Parameter
  const datos = [
    [PassArray[16], 0.075, PassArray[15], 0.25],
    [PassArray[15], 0.25, PassArray[14], 0.30],
    [PassArray[14], 0.30, PassArray[13], 0.85],
    [PassArray[13], 0.85, PassArray[12], 1.18],
    [PassArray[12], 1.18, PassArray[11], 2.00],
    [PassArray[11], 2.00, PassArray[10], 4.75],
    [PassArray[10], 4.75, PassArray[9], 9.50],
    [PassArray[9], 9.50, PassArray[8], 19.00],
    [PassArray[8], 19.00, PassArray[7], 25.00],
    [PassArray[7], 25.00, PassArray[6], 38.10],
    [PassArray[6], 38.10, PassArray[5], 50.80],
    [PassArray[5], 50.80, PassArray[4], 63.50],
    [PassArray[4], 63.50, PassArray[3], 76.20],
    [PassArray[3], 76.20, PassArray[2], 88.90],
    [PassArray[2], 88.90, PassArray[1], 101.6],
    [PassArray[1], 101.6, PassArray[0], 127],
    [PassArray[0], 127, 0.0, 0.0]
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
      url: "../libs/graph/Grain-Size-Coarse.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeCoarseAggregate'));

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

  /**
   * Function Grain Size Coarsethan Aggregate
   */

  function CoarsethanAgg() {
    const cumRetArray = [0];
    const PassArray = [];
  
    for (let i = 1; i <= 13; i++) {
      // Obtener los valores
      const DrySoilTare = parseFloat(
        document.getElementById("DrySoilTare").value,
      );
      const Tare = parseFloat(document.getElementById("Tare").value);
      const Washed = parseFloat(document.getElementById("Washed").value);
      const WtRet = parseFloat(document.getElementById("WtRet" + i).value) || 0;
      const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);
  
      // Calculation
      const DrySoil = DrySoilTare - Tare;
      const WashPan = DrySoil - Washed;
  
      // Grain Size Distribution
      const Ret = (WtRet / DrySoil) * 100;
      const CumRet = cumRetArray[i - 1] + Ret;
      cumRetArray.push(CumRet);
      const Pass = 100 - CumRet;
      const PanRet = (PanWtRen / DrySoil) * 100;
      const TotalWtRet = PanWtRen + WashPan;
      const TotalRet = (TotalWtRet / DrySoil) * 100;
      const TotalCumRet = cumRetArray[13] + TotalRet;
      const TotalPass = 100 - TotalCumRet;
  
      // Summary Grain Size Distribution Parameter
      PassArray.push(Pass);
      const CoarserGravel = 100 - PassArray[3]; // 3"
      const Gravel = PassArray[3] - PassArray[10]; // No4
      const Sand = PassArray[10] - PassArray[12]; // No200
      const Fines = PassArray[PassArray.length - 1];
  
      // Result
      document.getElementById("DrySoil").value = DrySoil.toFixed(2);
      document.getElementById("WashPan").value = WashPan.toFixed(2);
      document.getElementById("Ret" + i).value = Ret.toFixed(2);
      document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
      document.getElementById("Pass" + i).value = Pass.toFixed(2);
      document.getElementById("PanRet").value = PanRet.toFixed(2);
      document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
      document.getElementById("TotalRet").value = TotalRet.toFixed(2);
      document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
      document.getElementById("TotalPass").value = TotalPass.toFixed(2);
      document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
      document.getElementById("Gravel").value = Gravel.toFixed(2);
      document.getElementById("Sand").value = Sand.toFixed(2);
      document.getElementById("Fines").value = Fines.toFixed(2);
    }
  
    // Reactivity Test Method FM13-006
    var total = 0;
    var count = 0;
  
    // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
    for (var i = 1; i <= 3; i++) {
      var elementId = "Particles" + i;
      var element = document.getElementById(elementId);
  
      if (element && !isNaN(parseFloat(element.value))) {
        // Convierte el contenido del input a un número y agrégalo al total
        total += parseFloat(element.value);
        count++;
      }
    }
    if (count >= 1) {
      var avgParticles = total / count;
      var reactionResult;
      var AcidResult;
  
      // Reaction Strength Result:
      if (avgParticles >= 30) {
        reactionResult = "Strong Reaction";
      } else if (avgParticles >= 16 && avgParticles <= 30) {
        reactionResult = "Moderate Reaction";
      } else if (avgParticles >= 1 && avgParticles <= 15) {
        reactionResult = "Weak Reaction";
      } else {
        reactionResult = "No Reaction";
      }
      // Acid Reactivity Test Result
      if (reactionResult === "No Reaction") {
        AcidResult = "Accepted";
      } else if (
        reactionResult === "Weak Reaction" ||
        reactionResult === "Moderate Reaction"
      ) {
        AcidResult = "Accepted";
      } else {
        AcidResult = "Rejected";
      }
  
      document.getElementById("AcidResult").value = AcidResult;
      document.getElementById("ReactionResult").value = reactionResult;
      document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
    }
  
    // Sumary Parameter
    const datos = [
      [PassArray[12], 0.075, PassArray[11], 2.0],
      [PassArray[11], 2.0, PassArray[10], 4.75],
      [PassArray[10], 4.75, PassArray[9], 9.5],
      [PassArray[9], 9.5, PassArray[8], 19.0],
      [PassArray[8], 19.0, PassArray[7], 25.0],
      [PassArray[7], 25.0, PassArray[6], 38.1],
      [PassArray[6], 38.1, PassArray[5], 50.8],
      [PassArray[5], 50.8, PassArray[4], 63.5],
      [PassArray[4], 63.5, PassArray[3], 76.2],
      [PassArray[3], 76.2, PassArray[2], 88.9],
      [PassArray[2], 88.9, PassArray[1], 101.6],
      [PassArray[1], 101.6, PassArray[0], 127],
      [PassArray[0], 127, 0.0, 0.0],
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
    var b =
      datosY10.reduce((a, b) => a + b, 0) / datosY10.length -
      (c * datosXln.reduce((a, b) => a + b, 0)) / datosXln.length;
  
    // Calcular la expresión
    const D10 = Math.exp((10 - b) / c);
  
    const datosY15 = [resultados[1][0], resultados[1][2]];
    const datosX15 = [resultados[1][1], resultados[1][3]];
  
    // Calcular el logaritmo natural de los datos X
    const datosXln15 = datosX15.map(Math.log);
  
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c15 = (datosY15[1] - datosY15[0]) / (datosXln15[1] - datosXln15[0]);
  
    // Calcular b
    const b15 =
      datosY15.reduce((a, b) => a + b, 0) / datosY15.length -
      (c15 * datosXln15.reduce((a, b) => a + b, 0)) / datosXln15.length;
  
    // Calcular la expresión
    const D15 = Math.exp((15 - b15) / c15);
  
    const datosY30 = [resultados[2][0], resultados[2][2]];
    const datosX30 = [resultados[2][1], resultados[2][3]];
  
    // Calcular el logaritmo natural de los datos X
    const datosXln30 = datosX30.map(Math.log);
  
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c30 = (datosY30[1] - datosY30[0]) / (datosXln30[1] - datosXln30[0]);
  
    // Calcular b
    const b30 =
      datosY30.reduce((a, b) => a + b, 0) / datosY30.length -
      (c30 * datosXln30.reduce((a, b) => a + b, 0)) / datosXln30.length;
  
    // Calcular la expresión
    const D30 = Math.exp((30 - b30) / c30);
  
    const datosY60 = [resultados[3][0], resultados[3][2]];
    const datosX60 = [resultados[3][1], resultados[3][3]];
  
    // Calcular el logaritmo natural de los datos X
    const datosXln60 = datosX60.map(Math.log);
  
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c60 = (datosY60[1] - datosY60[0]) / (datosXln60[1] - datosXln60[0]);
  
    // Calcular b
    const b60 =
      datosY60.reduce((a, b) => a + b, 0) / datosY60.length -
      (c60 * datosXln60.reduce((a, b) => a + b, 0)) / datosXln60.length;
  
    // Calcular la expresión
    const D60 = Math.exp((60 - b60) / c60);
  
    const datosY85 = [resultados[4][0], resultados[4][2]];
    const datosX85 = [resultados[4][1], resultados[4][3]];
  
    // Calcular el logaritmo natural de los datos X
    const datosXln85 = datosX85.map(Math.log);
  
    // Calcular la regresión logarítmica (usando el cálculo previo de c)
    const c85 = (datosY85[1] - datosY85[0]) / (datosXln85[1] - datosXln85[0]);
  
    // Calcular b
    const b85 =
      datosY85.reduce((a, b) => a + b, 0) / datosY85.length -
      (c85 * datosXln85.reduce((a, b) => a + b, 0)) / datosXln85.length;
  
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
        url: "../libs/graph/Grain-Size-Coarsethan.js",
        type: "GET",
        data: $("#nopasonada").serialize(),
        success: function(data) {}
      });
    }

    function actualizarImagen() {
      var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeCoarseThanAggregate'));
  
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

  /**
   * Function Grain Size LPF
   */

function LPF() {
  const cumRetArray = [0];
  const PassArray = [];

  var Specs12 = document.getElementById("Specs2");
  var Specs3p4 = document.getElementById("Specs5");
  var Specs3p8 = document.getElementById("Specs6");
  var SpecsN4 = document.getElementById("Specs7");
  var SpecsN10 = document.getElementById("Specs8");
  var SpecsN200 = document.getElementById("Specs13");

  Specs12.value = "100";
  Specs3p4.value = "80-100";
  Specs3p8.value = "70-100";
  SpecsN4.value = "60-100";
  SpecsN10.value = "50-100";
  SpecsN200.value = "25-94";

  for (let i = 1; i <= 13; i++) {
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
    const TotalCumRet = cumRetArray[13] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    // Summary Grain Size Distribution Parameter
    PassArray.push(Pass);
    const CoarserGravel =  100 - PassArray[1];
    const Gravel =  PassArray[1] - PassArray[6];
    const Sand =  PassArray[6] - PassArray[12];
    const Fines =  PassArray[PassArray.length - 1];  

    // Result
    document.getElementById("DrySoil").value = DrySoil.toFixed(2);
    document.getElementById("WashPan").value = WashPan.toFixed(2);
    document.getElementById("Ret" + i).value = Ret.toFixed(2);
    document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
    document.getElementById("Pass" + i).value = Pass.toFixed(2);
    document.getElementById("PanRet").value = PanRet.toFixed(2);
    document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
    document.getElementById("TotalRet").value = TotalRet.toFixed(2);
    document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
    document.getElementById("TotalPass").value = TotalPass.toFixed(2);
    document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
    document.getElementById("Gravel").value = Gravel.toFixed(2);
    document.getElementById("Sand").value = Sand.toFixed(2);
    document.getElementById("Fines").value = Fines.toFixed(2);
  }
  // Sumary Parameter
 const datos = [
    [PassArray[12], 0.075, PassArray[11], 0.25],
    [PassArray[11], 0.25, PassArray[10], 0.30],
    [PassArray[10], 0.30, PassArray[9], 0.85],
    [PassArray[9], 0.85, PassArray[8], 1.18],
    [PassArray[8], 1.18, PassArray[7], 2.00],
    [PassArray[7], 2.00, PassArray[6], 4.75],
    [PassArray[6], 4.75, PassArray[5], 9.50],
    [PassArray[5], 9.50, PassArray[4], 19.00],
    [PassArray[4], 19.00, PassArray[3], 25.00],
    [PassArray[3], 25.00, PassArray[2], 37.50],
    [PassArray[2], 37.50, PassArray[1], 75],
    [PassArray[1], 75, PassArray[0], 300],
    [PassArray[0], 300, 0.0, 0.0]
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
      url: "../libs/graph/Grain-Size-LPF.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeLPF'));

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

  /**
   * Function Grain Size CF
   */

function CF() {
  const cumRetArray = [0];
  const PassArray = [];

  var Specs1p5 = document.getElementById("Specs3");
  var Specs1 = document.getElementById("Specs4");
  var Specs3p4 = document.getElementById("Specs5");
  var Specs3p8 = document.getElementById("Specs6");
  var SpecsN4 = document.getElementById("Specs7");
  var SpecsN10 = document.getElementById("Specs8");
  var SpecsN20 = document.getElementById("Specs10");
  var SpecsN200 = document.getElementById("Specs13");

  Specs1p5.value = "100";
  Specs1.value = "87-100";
  Specs3p4.value = "70-100";
  Specs3p8.value = "33-100";
  SpecsN4.value = "7-60";
  SpecsN10.value = "0-15";
  SpecsN20.value = "0-7";
  SpecsN200.value = "0-5";

  for (let i = 1; i <= 13; i++) {
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
    const TotalCumRet = cumRetArray[13] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    // Summary Grain Size Distribution Parameter
    PassArray.push(Pass);
    const CoarserGravel =  100 - PassArray[1];
    const Gravel =  PassArray[1] - PassArray[6];
    const Sand =  PassArray[6] - PassArray[12];
    const Fines =  PassArray[PassArray.length - 1];  

    // Result
    document.getElementById("DrySoil").value = DrySoil.toFixed(2);
    document.getElementById("WashPan").value = WashPan.toFixed(2);
    document.getElementById("Ret" + i).value = Ret.toFixed(2);
    document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
    document.getElementById("Pass" + i).value = Pass.toFixed(2);
    document.getElementById("PanRet").value = PanRet.toFixed(2);
    document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
    document.getElementById("TotalRet").value = TotalRet.toFixed(2);
    document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
    document.getElementById("TotalPass").value = TotalPass.toFixed(2);
    document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
    document.getElementById("Gravel").value = Gravel.toFixed(2);
    document.getElementById("Sand").value = Sand.toFixed(2);
    document.getElementById("Fines").value = Fines.toFixed(2);
  }

      // Reactivity Test Method FM13-006
      var total = 0;
      var count = 0;
  
      // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
      for (var i = 1; i <= 3; i++) {
        var elementId = "Particles" + i;
        var element = document.getElementById(elementId);
  
        if (element && !isNaN(parseFloat(element.value))) {
          // Convierte el contenido del input a un número y agrégalo al total
          total += parseFloat(element.value);
          count++;
        }
      }
      if (count >= 1) {
        var avgParticles = total / count;
        var reactionResult;
        var AcidResult;
  
        // Reaction Strength Result:
        if (avgParticles >= 30) {
          reactionResult = "Strong Reaction";
        } else if (avgParticles >= 16 && avgParticles <= 30) {
          reactionResult = "Moderate Reaction";
        } else if (avgParticles >= 1 && avgParticles <= 15) {
          reactionResult = "Weak Reaction";
        } else {
          reactionResult = "No Reaction";
        }
        // Acid Reactivity Test Result
        if (reactionResult === "No Reaction") {
          AcidResult = "Accepted";
        } else if (reactionResult === "Weak Reaction" || reactionResult === "Moderate Reaction") {
          AcidResult = "Accepted";
        } else {
          AcidResult = "Rejected";
        }
  
        document.getElementById("AcidResult").value = AcidResult;
        document.getElementById("ReactionResult").value = reactionResult;
        document.getElementById("AvgParticles").value = avgParticles;
      }

  // Sumary Parameter
 const datos = [
    [PassArray[12], 0.075, PassArray[11], 0.25],
    [PassArray[11], 0.25, PassArray[10], 0.30],
    [PassArray[10], 0.30, PassArray[9], 0.85],
    [PassArray[9], 0.85, PassArray[8], 1.18],
    [PassArray[8], 1.18, PassArray[7], 2.00],
    [PassArray[7], 2.00, PassArray[6], 4.75],
    [PassArray[6], 4.75, PassArray[5], 9.50],
    [PassArray[5], 9.50, PassArray[4], 19.00],
    [PassArray[4], 19.00, PassArray[3], 25.00],
    [PassArray[3], 25.00, PassArray[2], 37.50],
    [PassArray[2], 37.50, PassArray[1], 75],
    [PassArray[1], 75, PassArray[0], 300],
    [PassArray[0], 300, 0.0, 0.0]
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
      url: "../libs/graph/Grain-Size-CF.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeCF'));

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

  /**
   * Function Grain Size FF
   */

  function FF() {
    const cumRetArray = [0];
    const PassArray = [];
  
      var Specs3p8 = document.getElementById("Specs6");
      var SpecsNo4 = document.getElementById("Specs7");
      var SpecsNo10 = document.getElementById("Specs8");
      var SpecsNo16 = document.getElementById("Specs9");
      var SpecsNo50 = document.getElementById("Specs11");
      var SpecsNo60 = document.getElementById("Specs12");
      var SpecsNo200 = document.getElementById("Specs13");
  
      Specs3p8.value = "100";
      SpecsNo4.value = "95-100";
      SpecsNo10.value = "65-100";
      SpecsNo16.value = "50-85";
      SpecsNo50.value = "5-30";
      SpecsNo60.value = "0-25";
      SpecsNo200.value = "0-5";
  
      for (let i = 1; i <= 13; i++) {
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
      const TotalCumRet = cumRetArray[13] + TotalRet;
      const TotalPass = 100 - TotalCumRet;
  
      // Summary Grain Size Distribution Parameter
      PassArray.push(Pass);
      const CoarserGravel =  100 - PassArray[1];
      const Gravel =  PassArray[1] - PassArray[6];
      const Sand =  PassArray[6] - PassArray[12];
      const Fines =  PassArray[PassArray.length - 1];
  
      // Result
      document.getElementById("DrySoil").value = DrySoil.toFixed(2);
      document.getElementById("WashPan").value = WashPan.toFixed(2);
      document.getElementById("Ret" + i).value = Ret.toFixed(2);
      document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
      document.getElementById("Pass" + i).value = Pass.toFixed(2);
      document.getElementById("PanRet").value = PanRet.toFixed(2);
      document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
      document.getElementById("TotalRet").value = TotalRet.toFixed(2);
      document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
      document.getElementById("TotalPass").value = TotalPass.toFixed(2);
      document.getElementById("CoarserGravel").value = CoarserGravel.toFixed(2);
      document.getElementById("Gravel").value = Gravel.toFixed(2);
      document.getElementById("Sand").value = Sand.toFixed(2);
      document.getElementById("Fines").value = Fines.toFixed(2);
    }
    
    
    // Reactivity Test Method FM13-006
    var total = 0;
    var count = 0;
  
    // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
    for (var i = 1; i <= 5; i++) {
      var elementId = "Particles" + i;
      var element = document.getElementById(elementId);
  
      if (element && !isNaN(parseFloat(element.value))) {
        // Convierte el contenido del input a un número y agrégalo al total
        total += parseFloat(element.value);
        count++;
      }
    }
    if (count >= 1) {
      var avgParticles = total / count;
      var reactionResult;
      var AcidResult;
  
      // Reaction Strength Result:
      if (avgParticles >= 30) {
        reactionResult = "Strong Reaction";
      } else if (avgParticles >= 16 && avgParticles <= 30) {
        reactionResult = "Moderate Reaction";
      } else if (avgParticles >= 1 && avgParticles <= 15) {
        reactionResult = "Weak Reaction";
      } else {
        reactionResult = "No Reaction";
      }
      // Acid Reactivity Test Result
      if (reactionResult === "No Reaction") {
        AcidResult = "Accepted";
      } else if (reactionResult === "Weak Reaction" || reactionResult === "Moderate Reaction") {
        AcidResult = "Accepted";
      } else {
        AcidResult = "Rejected";
      }
  
      document.getElementById("AcidResult").value = AcidResult;
      document.getElementById("ReactionResult").value = reactionResult;
      document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
  }
    
    
    // Sumary Parameter
    const datos = [
      [PassArray[12], 0.075, PassArray[11], 0.25],
      [PassArray[11], 0.25, PassArray[10], 0.3],
      [PassArray[10], 0.3, PassArray[9], 0.85],
      [PassArray[9], 0.85, PassArray[8], 1.18],
      [PassArray[8], 1.18, PassArray[7], 2],
      [PassArray[7], 2, PassArray[6], 4.75],
      [PassArray[6], 4.75, PassArray[5], 9.5],
      [PassArray[5], 9.5, PassArray[4], 19],
      [PassArray[4], 19, PassArray[3], 25],
      [PassArray[3], 25, PassArray[2], 37.5],
      [PassArray[2], 37.5, PassArray[1], 75],
      [PassArray[1], 75, PassArray[0], 300],
      [PassArray[0], 300, 0.0, 0.0]
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
        url: "../libs/graph/Grain-Size-FF.js",
        type: "GET",
        data: $("#nopasonada").serialize(),
        success: function(data) {}
      });
    }
  
    function actualizarImagen() {
      var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeFF'));
  
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

    /**
   * Function Grain Size UTF
   */

function UTF() {
  const cumRetArray = [0];
  const PassArray = [];
  let CoarserGravel;
  let gravel;
  let sand;
  let fines;

  var Specs2 = document.getElementById("Specs5");
  var SpecsN200 = document.getElementById("Specs13");

  Specs2.value = "100";
  SpecsN200.value = "0-5";

  for (let i = 1; i <= 13; i++) {
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
    const TotalCumRet = cumRetArray[13] + TotalRet;
    const TotalPass = 100 - TotalCumRet;

    // Summary Grain Size Distribution Parameter
    PassArray.push(Pass);
    if (PassArray.length > 12) {
      CoarserGravel = 100 - PassArray[2];
      gravel = PassArray[2] - PassArray[10];
      sand = PassArray[10] - PassArray[12];
      fines = PassArray[PassArray.length - 1];
    } else {
      CoarserGravel = null;
      gravel = null;
      sand = null;
      fines = null;
    }

    // Result
    document.getElementById("DrySoil").value = DrySoil.toFixed(2);
    document.getElementById("WashPan").value = WashPan.toFixed(2);
    document.getElementById("Ret" + i).value = Ret.toFixed(2);
    document.getElementById("CumRet" + i).value = CumRet.toFixed(2);
    document.getElementById("Pass" + i).value = Pass.toFixed(2);
    document.getElementById("PanRet").value = PanRet.toFixed(2);
    document.getElementById("TotalWtRet").value = TotalWtRet.toFixed(2);
    document.getElementById("TotalRet").value = TotalRet.toFixed(2);
    document.getElementById("TotalCumRet").value = TotalCumRet.toFixed(2);
    document.getElementById("TotalPass").value = TotalPass.toFixed(2);
    document.getElementById("CoarserGravel").value = CoarserGravel !== null && !isNaN(CoarserGravel) ? CoarserGravel.toFixed(2) : "N/A";
    document.getElementById("Gravel").value = gravel !== null && !isNaN(gravel) ? gravel.toFixed(2) : "N/A";
    document.getElementById("Sand").value = sand !== null && !isNaN(sand) ? sand.toFixed(2) : "N/A";
    document.getElementById("Fines").value = fines !== null && !isNaN(fines) ? fines.toFixed(2) : "N/A";
  }

      // Reactivity Test Method FM13-006
      var total = 0;
      var count = 0;
  
      // Itera sobre todos los elementos cuyos IDs comienzan con "Particles"
      for (var i = 1; i <= 3; i++) {
        var elementId = "Particles" + i;
        var element = document.getElementById(elementId);
  
        if (element && !isNaN(parseFloat(element.value))) {
          // Convierte el contenido del input a un número y agrégalo al total
          total += parseFloat(element.value);
          count++;
        }
      }
      if (count >= 1) {
        var avgParticles = total / count;
        var reactionResult;
        var AcidResult;
  
        // Reaction Strength Result:
        if (avgParticles >= 30) {
          reactionResult = "Strong Reaction";
        } else if (avgParticles >= 16 && avgParticles <= 30) {
          reactionResult = "Moderate Reaction";
        } else if (avgParticles >= 1 && avgParticles <= 15) {
          reactionResult = "Weak Reaction";
        } else {
          reactionResult = "No Reaction";
        }
        // Acid Reactivity Test Result
        if (reactionResult === "No Reaction") {
          AcidResult = "Accepted";
        } else if (reactionResult === "Weak Reaction" || reactionResult === "Moderate Reaction") {
          AcidResult = "Accepted";
        } else {
          AcidResult = "Rejected";
        }
  
        document.getElementById("AcidResult").value = AcidResult;
        document.getElementById("ReactionResult").value = reactionResult;
        document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
      }

  // Sumary Parameter
 const datos = [
    [PassArray[12], 0.075, PassArray[11], 2],
    [PassArray[11], 2, PassArray[10], 4.75],
    [PassArray[10], 4.75, PassArray[9], 9.5],
    [PassArray[9], 9.5, PassArray[8], 12.5],
    [PassArray[8], 12.5, PassArray[7], 19],
    [PassArray[7], 19, PassArray[6], 25],
    [PassArray[6], 25, PassArray[5], 37.5],
    [PassArray[5], 37.5, PassArray[4], 50.8],
    [PassArray[4], 50.8, PassArray[3], 63],
    [PassArray[3], 63, PassArray[2], 75],
    [PassArray[2], 75, PassArray[1], 89],
    [PassArray[1], 89, PassArray[0], 101.6],
    [PassArray[0], 101.6, 0.0, 0.0]
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
    } else if (gravel > sand && fines < 5 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        return "GW-Well graded gravel with sand";
    } else if (gravel > sand && fines < 5 && Cu < 4 && Cc < 1  && sand < 15) {
        return "GP-Poorly graded gravel";
      } else if (gravel > sand && fines < 5 && Cu < 4 &&  Cc > 3 && sand < 15) {
        return "GP-Poorly graded gravel";
    } else if (gravel > sand && fines < 5 && Cu < 4 && Cc < 1 && sand >= 15) {
        return "GP-Poorly graded gravel with sand";
      } else if (gravel > sand && fines < 5 && Cu < 4 &&  Cc > 3 && sand >= 15) {
        return "GP-Poorly graded gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && (fines == "ML" || fines == "MH") && sand < 15) {
        return "GW-GM-Well graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && (fines == "ML" || fines == "MH") && sand >= 15) {
        return "GW-GM-Well graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && (fines == "CL" || fines == "CH") && sand < 15) {
        return "GW-GC-Well graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && (fines == "CL" || fines == "CH") && sand >= 15) {
        return "GW-GC-Well graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && (fines == "ML" || fines == "MH") && sand < 15) {
        return "GP-GM-Poorly graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && (fines == "ML" || fines == "MH") && sand >= 15) {
        return "GP-GM-Poorly graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && (fines == "CL" || fines == "CH") && sand < 15) {
        return "GP-GC-Poorly graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && (fines == "CL" || fines == "CH") && sand >= 15) {
        return "GP-GC-Poorly graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "ML" || fines == "MH") && sand < 15) {
        return "GM-Silty gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "ML" || fines == "MH") && sand >= 15) {
        return "GM-Silty gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "CL" || fines == "CH") && sand < 15) {
        return "GC-Clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "CL" || fines == "CH") && sand >= 15) {
        return "GC-Clayey gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "CL" || fines == "ML") && sand < 15) {
        return "GC-GM-Silty clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && (fines == "CL" || fines == "ML") && sand >= 15) {
        return "GC-GM-Silty clayey gravel with sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        return "SW-Well graded sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        return "SW-Well graded sand with gravel";
    } else if (sand > gravel && fines < 5 && Cu < 6 && Cc < 1 && gravel < 15) {
        return "SP-Poorly graded sand";
      } else if (sand > gravel && fines < 5 && Cu < 6 &&  Cc > 3 && gravel < 15) {
        return "SP-Poorly graded sand";
    } else if (sand > gravel && fines < 5 && Cu < 6 && (Cc < 1 || Cc > 3) && gravel >= 15) {
        return "SP-Poorly graded sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && (fines == "ML" || fines == "MH") && gravel < 15) {
        return "SW-SM-Well graded sand with silt";
    } else if (sand >gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && (fines == "ML" || fines == "MH") && gravel>= 15) {
        return "SW-SM-Well graded sand with silt and sand";
      } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && (fines == "CL" || fines == "CH") && gravel< 15) {
        return "SW-SC-Well graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && (fines == "CL" || fines == "CH") && gravel >= 15) {
        return "SW-SC-Well graded sand with clay and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && (fines == "ML" || fines == "MH") && gravel < 15) {
        return "SP-SM-Poorly graded sand with silt";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && (fines == "ML" || fines == "MH") && gravel>= 15) {
        return "SP-SM-Poorly graded sand with silt and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && (fines == "CL" || fines == "CH") && gravel < 15) {
        return "SP-SC-Poorly graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && (fines == "CL" || fines == "CH") && gravel>= 15) {
        return "SP-SC-Poorly graded sand with clay and sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "ML" || fines == "MH") && gravel < 15) {
        return "SM-Silty sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "ML" || fines == "MH") && gravel >= 15) {
        return "SM-Silty sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "CL" || fines == "CH") && gravel < 15) {ss
        return "SC-Clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "CL" || fines == "CH") && gravel >= 15) {
        return "SC-Clayey sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "CL" || fines == "ML") && gravel < 15) {
        return "SC-GM-Silty clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && (fines == "CL" || fines == "ML") && gravel >= 15) {
        return "SC-GM-Silty clayey sand with gravel";
    } else {
        return "No se pudo clasificar el suelo.";
    }
}

// Obtener la clasificación
let classification = clasificarSuelo();

// Dividir el texto basado en el primer guion
let parts = classification.split(/-(.+)/); // Usa una expresión regular para dividir en el primer guion
let part1 = parts[0]; // Parte antes del primer guion
let part2 = parts[1] || ""; // Parte después del primer guion, o vacío si no existe

// Asignar los valores a los inputs
document.getElementById("ClassificationUSCS1").value = part2;
document.getElementById("ClassificationUSCS2").value = part1;

console.log(clasificarSuelo());


  $("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Grain-Size-UTF.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var GrainSizeGeneral = echarts.getInstanceByDom(document.getElementById('GrainSizeUTF'));

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