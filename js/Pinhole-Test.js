function Pinhole() {
    let mcBefore = 0;
    let mcAfter = 0;

    // Loop through each set of inputs
    for (let i = 1; i <= 2; i++) {
        // Obtener los valores
        const wetSoil = parseFloat(document.getElementById("wetSoil" + i).value);
        const drySoil = parseFloat(document.getElementById("drySoil" + i).value);
        const tare = parseFloat(document.getElementById("tare" + i).value);

        // Calculos
        const water = wetSoil - drySoil;
        const drySoilWs = drySoil - tare;
        const mc = (water / drySoilWs) * 100;

        // Result
        document.getElementById("water" + i).value = water.toFixed(2);
        document.getElementById("drySoilWs" + i).value = drySoilWs.toFixed(2);
        document.getElementById("mc" + i).value = mc.toFixed(2);

        // Moisture Content CON VALORES AGSINADOS
        if (i === 1) {
            mcBefore = mc;
        } else if (i === 2) {
            mcAfter = mc;
        }
    }

    // OBTENEMOS LOS VALORES DE LOS INPUTS
    const maxDryDensity = parseFloat(document.getElementById("maxDryDensity").value);
    const optimumMC = parseFloat(document.getElementById("optimumMC").value);
    const welSoilMold = parseFloat(document.getElementById("welSoilMold").value);
    const wtMold = parseFloat(document.getElementById("wtMold").value);
    const longitudSpecimen = parseFloat(document.getElementById("longitudSpecimen").value);

    // CALCULOS CON LOS VALORES OBTENIDOS
    const wtWetSoil = welSoilMold - wtMold;
    const volSpecimen = Math.PI * ((3.322 / 2) ** 2) * longitudSpecimen;
    const wetDensity = wtWetSoil / volSpecimen;
    const dryDensityGCM3 = wetDensity / (1 + (mcBefore / 100));
    const porceCompaction = (dryDensityGCM3 / maxDryDensity) * 100;


    // PASAMOS LOS VALORES A LOS INPUTS
    document.getElementById("mcBefore").value = mcBefore.toFixed(2);
    document.getElementById("wtWetSoil").value = wtWetSoil.toFixed(2);
    document.getElementById("volSpecimen").value = volSpecimen.toFixed(2);
    document.getElementById("wetDensity").value = wetDensity.toFixed(2);
    document.getElementById("dryDensityGCM3").value = dryDensityGCM3.toFixed(2);
    document.getElementById("porceCompaction").value = porceCompaction.toFixed(2);
    document.getElementById("mcAfter").value = mcAfter.toFixed(2);

    // Inicializamos el flujo anterior como 0
    let previousFlow = 0;

    // Loop through each set of inputs
    for (let i = 1; i <= 22; i++) {
        // ObTENEMOS LOS VALORES
        const ml = parseFloat(document.getElementById("ML_" + i).value);
        const seg = parseFloat(document.getElementById("Seg_" + i).value);
    
        // CALCULAMOS LOS VALORES
        const flowRate = ml / seg;
        const flow = (seg * 0.0166667) + previousFlow;

        // Actualizamos el flujo previo para la próxima iteración
        previousFlow = flow;

        // PASAMOS LOS RESULTADOS
        document.getElementById("Flow_Rate_" + i).value = flowRate.toFixed(2);
    }
}

$("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Pinhole-Test.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var Pinhole = echarts.getInstanceByDom(document.getElementById('Pinhole'));

    var ImageURL = Pinhole.getDataURL({
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