function SProctor() {
    let MaxDryDensity = -Infinity;
    let CorrectedDryUnitWeigt = -Infinity;
    let OptimumMoisture;
    let CorrectedWaterContentFiner = 0;

    for (let i = 1; i <= 6; i++) {
        // Obtener los valores del liquid limit
        const WetSoilMod = document.getElementById("WetSoilMod" + i).value;
        const WtMold = document.getElementById("WtMold" + i).value;
        const VolMold = document.getElementById("VolMold" + i).value;
        const WetSoilTare = document.getElementById("WetSoilTare" + i).value;
        const WetDryTare = document.getElementById("WetDryTare" + i).value;
        const Tare = document.getElementById("Tare" + i).value;
        // correcion obtener valores
        const WcPorce = document.getElementById("WcPorce").value;
        const PcPorce = document.getElementById("PcPorce").value;
        const PfPorce = document.getElementById("PfPorce").value;
        const Gm = document.getElementById("Gm").value;
        const YwKnm = document.getElementById("YwKnm").value;

        // Calcular
        const WtSoil = WetSoilMod - WtMold;
        const WetDensity = (WtSoil/VolMold)*1000;
        const WtWater = WetSoilTare - WetDryTare;
        const DrySoil = WetDryTare - Tare;
        const MoisturePorce = (WtWater/DrySoil)*100;
        const DryDensity = WetDensity/(1+(MoisturePorce/100));
        // Correction
        const Ydf = DryDensity/98.1;
        const OptimMC = MoisturePorce/100;
        const Ydt = 100*(Ydf*Gm*YwKnm)/((Ydf*PcPorce)+(Gm*YwKnm*PfPorce));
        const DensyCorrected = +Ydt*98.1;
        const MCcorrected = (OptimMC*PfPorce+WcPorce/100*PcPorce);


        //console.log("Ydt" + i + ":", Ydt);

        // Verificar si DryDensity es un número válido
        if (!isNaN(DryDensity)) {
            MaxDryDensity = Math.max(MaxDryDensity, DryDensity);
            
            if (DryDensity === MaxDryDensity) {
                OptimumMoisture = MoisturePorce;
            }
        }
        if (!isNaN(DensyCorrected)) {
            CorrectedDryUnitWeigt = Math.max(CorrectedDryUnitWeigt, DensyCorrected);

            if (DensyCorrected === CorrectedDryUnitWeigt) {
                CorrectedWaterContentFiner = MCcorrected;
            }
        }
     
        

        // Pasar el resultado
        document.getElementById("WtSoil" + i).value = WtSoil.toFixed(1);
        document.getElementById("WetDensity" + i).value = WetDensity.toFixed(1);
        document.getElementById("DryDensity" + i).value = DryDensity.toFixed(1);
        document.getElementById("WtWater" + i).value = WtWater.toFixed(2);
        document.getElementById("DrySoil" + i).value = DrySoil.toFixed(2);
        document.getElementById("MoisturePorce" + i).value = MoisturePorce.toFixed(2) + "%";

        document.getElementById("DensyCorrected"  + i).value = DensyCorrected.toFixed(2);
        document.getElementById("MCcorrected"  + i).value = MCcorrected.toFixed(2) + "%";
        
        document.getElementById("MaxDryDensity").value = MaxDryDensity.toFixed(0);
        document.getElementById("CorrectedDryUnitWeigt").value = CorrectedDryUnitWeigt.toFixed(0);
        if (OptimumMoisture !== undefined) {
            document.getElementById("OptimumMoisture").value = OptimumMoisture.toFixed(2) + "%";
            document.getElementById("CorrectedWaterContentFiner").value = CorrectedWaterContentFiner.toFixed(2) + "%";
        }
    }
}

$("input").on("blur", function(event) {
    event.preventDefault();
    enviarData();
  });

  function enviarData() {
    $.ajax({
      url: "../libs/graph/Standard-Proctor.js",
      type: "GET",
      data: $("#nopasonada").serialize(),
      success: function(data) {}
    });
  }

  function actualizarImagen() {
    var StandardProctor = echarts.getInstanceByDom(document.getElementById('StandardProctor'));

    var ImageURL = StandardProctor.getDataURL({
        pixelRatio: 0.8,
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

function search() {
    var ID = $('#SampleName').val();
    var Number = $('#SampleNumber').val();
  
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
          $('#SpecGravity').val(response.sg_value);
        } else {
          $('#mensaje-container').html(response.message).fadeIn();
  
          setTimeout(function() {
            $('#mensaje-container').fadeOut();
          }, 2000);
        }
      }
    });
  }