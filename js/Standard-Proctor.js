import { enviarImagenAlServidor } from './export/export-chart.js';

function SProctor() {
  let MaxDryDensity = -Infinity;
  let CorrectedDryUnitWeigt = null;
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
    const WetDensity = (WtSoil / VolMold) * 1000;
    const WtWater = WetSoilTare - WetDryTare;
    const DrySoil = WetDryTare - Tare;
    const MoisturePorce = (WtWater / DrySoil) * 100;
    const DryDensity = WetDensity / (1 + (MoisturePorce / 100));
    // Correction
    const Ydf = DryDensity / 98.1;
    const OptimMC = MoisturePorce / 100;
    const Ydt = 100 * (Ydf * Gm * YwKnm) / ((Ydf * PcPorce) + (Gm * YwKnm * PfPorce));
    const DensyCorrected = +Ydt * 98.1;
    const MCcorrected = (OptimMC * PfPorce + WcPorce / 100 * PcPorce);

    if (i === 3) {
      // Solo asignar si hay valores válidos
      if (WcPorce && Gm) { // o WcPorce !== "" && Gm !== "" si son strings
        document.getElementById("Ydf").value = isNaN(Ydf) || Ydf === 0 ? "" : Ydf.toFixed(3);
        document.getElementById("Ydt").value = isNaN(Ydt) || Ydt === 0 ? "" : Ydt.toFixed(3);
      } else {
        // Si no hay datos, dejar vacío
        document.getElementById("Ydf").value = "";
        document.getElementById("Ydt").value = "";
      }
    }

    // Verificar si DryDensity es un número válido
    if (!isNaN(DryDensity)) {
      MaxDryDensity = Math.max(MaxDryDensity, DryDensity);

      if (DryDensity === MaxDryDensity) {
        OptimumMoisture = MoisturePorce;
      }
    }
    if (!isNaN(DensyCorrected)) {
      CorrectedDryUnitWeigt = Math.max(CorrectedDryUnitWeigt, DensyCorrected);

      // Validar para no mostrar Infinity o valores inválidos
      if (!isFinite(CorrectedDryUnitWeigt) || CorrectedDryUnitWeigt === 0) {
        CorrectedDryUnitWeigt = 0; // o "" si quieres dejar el input vacío
      }

      if (DensyCorrected === CorrectedDryUnitWeigt) {
        CorrectedWaterContentFiner = MCcorrected;
      }
    }



    // Pasar el resultado
    document.getElementById("WtSoil" + i).value = isNaN(WtSoil) || WtSoil === 0 ? "" : WtSoil.toFixed(1);
    document.getElementById("WetDensity" + i).value = isNaN(WetDensity) || WetDensity === 0 ? "" : WetDensity.toFixed(1);
    document.getElementById("DryDensity" + i).value = isNaN(DryDensity) || DryDensity === 0 ? "" : DryDensity.toFixed(1);
    document.getElementById("WtWater" + i).value = isNaN(WtWater) || WtWater === 0 ? "" : WtWater.toFixed(2);
    document.getElementById("DrySoil" + i).value = isNaN(DrySoil) || DrySoil === 0 ? "" : DrySoil.toFixed(2);
    document.getElementById("MoisturePorce" + i).value = isNaN(MoisturePorce) || MoisturePorce === 0 ? "" : MoisturePorce.toFixed(2) + "%";

    document.getElementById("DensyCorrected" + i).value = isNaN(DensyCorrected) || DensyCorrected === 0 ? "" : DensyCorrected.toFixed(2);
    document.getElementById("MCcorrected" + i).value = isNaN(MCcorrected) || MCcorrected === 0 ? "" : MCcorrected.toFixed(2) + "%";

    document.getElementById("MaxDryDensity").value = isNaN(MaxDryDensity) || MaxDryDensity === 0 ? "" : MaxDryDensity.toFixed(0);
    document.getElementById("CorrectedDryUnitWeigt").value = CorrectedDryUnitWeigt ? CorrectedDryUnitWeigt.toFixed(0) : "";
    if (OptimumMoisture !== undefined) {
      document.getElementById("OptimumMoisture").value = isNaN(OptimumMoisture) || OptimumMoisture === 0 ? "" : OptimumMoisture.toFixed(2) + "%";
      document.getElementById("CorrectedWaterContentFiner").value = isNaN(CorrectedWaterContentFiner) || CorrectedWaterContentFiner === 0 ? "" : CorrectedWaterContentFiner.toFixed(2) + "%";
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.row");
  if (form) {
    form.querySelectorAll("input").forEach(input => {
      input.addEventListener("input", SProctor);
    });
  }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
  el.addEventListener('click', () => {
    const tipo = el.dataset.exportar;
    enviarImagenAlServidor(tipo, ["StandardProctor"]);
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.row");
  if (form) {
    // Seleccionamos solo los inputs con los nombres específicos
    const inputs = form.querySelectorAll('input[name="SearchMC"], input[name="SheacrSG"]');
    inputs.forEach(input => {
      input.addEventListener("input", search);
    });
  }
});


$("input").on("blur", function (event) {
  event.preventDefault();
  enviarData();
});

function enviarData() {
  $.ajax({
    url: "../libs/graph/Standard-Proctor.js",
    type: "GET",
    data: $("#nopasonada").serialize(),
    success: function (data) { }
  });
}

function search() {
  var ID = $('#SampleName').val();
  var Number = $('#SampleNumber').val();

  $.ajax({
    type: 'POST',
    url: '../php/ajax-search.php',
    data: { ID: ID, Number: Number },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        $('#mensaje-container').html(response.message).fadeIn();

        setTimeout(function () {
          $('#mensaje-container').fadeOut();
        }, 2000);

        $('#NatMc').val(response.mc_value);
        $('#SpecGravity').val(response.sg_value);
      } else {
        $('#mensaje-container').html(response.message).fadeIn();

        setTimeout(function () {
          $('#mensaje-container').fadeOut();
        }, 2000);
      }
    }
  });
}