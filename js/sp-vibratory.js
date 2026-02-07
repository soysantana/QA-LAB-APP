// Saturado
function Saturado() {
  let valueAverage = [];
  const Gravity = parseFloat(document.getElementById("SpecGravity").value);
  for (let i = 1; i <= 3; i++) {
    const VolMoldSatured = parseFloat(document.getElementById("VolMoldSatured" + i).value);
    const MassTareSatured = parseFloat(document.getElementById("MassTareSatured" + i).value);
    const MassTareDrySatured = parseFloat(document.getElementById("MassTareDrySatured" + i).value);

    if (!VolMoldSatured || !MassTareSatured || !MassTareDrySatured) {
      continue;
    }

    const MassSoilSatured = MassTareDrySatured - MassTareSatured;
    const DryDensitySatured = MassSoilSatured / VolMoldSatured;
    const DryUnitSatured = DryDensitySatured * 0.009807;

    valueAverage.push(DryUnitSatured);

    document.getElementById("MassSoilSatured" + i).value = MassSoilSatured.toFixed(3);
    document.getElementById("DryDensitySatured" + i).value = DryDensitySatured.toFixed(2);
    document.getElementById("DryUnitSatured" + i).value = DryUnitSatured.toFixed(2);
  }

  if (valueAverage.length === 0) {
    return;
  }

  const Average = valueAverage.reduce((sum, n) => sum + n, 0) / valueAverage.length;
  const Max = Math.max(...valueAverage);
  const Min = Math.min(...valueAverage);
  const Difference = Max - Min;
  const Percentage = (Difference / Average) * 100;
  const TestCondition = Percentage < 2 ? "Accepted" : "Failed";
  const SaturadoWaterContentEffective = (((9.789 / Max) - (1 / Gravity)) * 100);


  document.getElementById("SaturadoMaxDryUnitDensity").value = Max.toFixed(2);
  document.getElementById("SaturadoTestCondition").value = TestCondition;
  document.getElementById("SaturadoWaterContentEffective").value = isNaN(SaturadoWaterContentEffective) ? "" : SaturadoWaterContentEffective.toFixed(2);
}

// Seco Vibrado
function DryVibrated() {
  for (let i = 1; i <= 3; i++) {
    const WtMoldVibrar = document.getElementById("WtMoldVibrar" + i).value;
    const VolMoldVibrar = document.getElementById("VolMoldVibrar" + i).value;
    const MassMoldBaseVibrar = document.getElementById("MassMoldBaseVibrar" + i).value;

    const MassSoilVibrar = (MassMoldBaseVibrar - WtMoldVibrar);
    const SoilDryVibrar = (MassSoilVibrar / VolMoldVibrar);
    const SoilUnitVibrar = (SoilDryVibrar * 0.009807);

    document.getElementById("MassSoilVibrar" + i).value = isNaN(MassSoilVibrar) || MassSoilVibrar === 0 ? "" : MassSoilVibrar.toFixed(3);
    document.getElementById("SoilDryVibrar" + i).value = isNaN(SoilDryVibrar) || SoilDryVibrar === 0 ? "" : SoilDryVibrar.toFixed(2);
    document.getElementById("SoilUnitVibrar" + i).value = isNaN(SoilUnitVibrar) || SoilUnitVibrar === 0 ? "" : SoilUnitVibrar.toFixed(2);
  }
}

// Seco Sin Vibrar
function DryWithoutVibration() {
  for (let i = 1; i <= 3; i++) {
    const WtMold = document.getElementById("WtMold" + i).value;
    const VolMold = document.getElementById("VolMold" + i).value;
    const MassMoldBase = document.getElementById("MassMoldBase" + i).value;

    const MassSoil = (MassMoldBase - WtMold);
    const SoilDry = (MassSoil / VolMold);
    const SoilUnit = (SoilDry * 0.009807);

    document.getElementById("MassSoil" + i).value = isNaN(MassSoil) || MassSoil === 0 ? "" : MassSoil.toFixed(3);
    document.getElementById("SoilDry" + i).value = isNaN(SoilDry) || SoilDry === 0 ? "" : SoilDry.toFixed(2);
    document.getElementById("SoilUnit" + i).value = isNaN(SoilUnit) || SoilUnit === 0 ? "" : SoilUnit.toFixed(2);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.row");
  if (form) {
    form.querySelectorAll("input").forEach(input => {
      input.addEventListener("input", Saturado);
      input.addEventListener("input", DryVibrated);
      input.addEventListener("input", DryWithoutVibration);
    });
  }
});

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

document.addEventListener("DOMContentLoaded", () => {
  const botones = document.querySelectorAll('button[name="SearchMC"], button[name="SearchSG"]');

  botones.forEach(boton => {
    boton.addEventListener("click", search);
  });
});
