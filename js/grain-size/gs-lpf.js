import { calcularParametrosGranulometricos } from './gs-summary.js';
import { enviarImagenAlServidor } from '../export/export-chart.js';
import { UpdateGraph } from '../../charts/grain-size/gs-chart.js';

function LPF() {

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

  const cumRetArray = [0];
  const PassArray = new Array(13).fill(null);

  const DrySoilTare = parseFloat(document.getElementById("DrySoilTare").value);
  const Tare = parseFloat(document.getElementById("Tare").value);
  const Washed = parseFloat(document.getElementById("Washed").value);
  const PanWtRen = parseFloat(document.getElementById("PanWtRen").value);

  const DrySoil = DrySoilTare - Tare;
  const WashPan = DrySoil - Washed;

  let lastCumRet = 0;

  // Detectar primer índice con valor válido en WtRet
  let startIndex = 1;
  for (let i = 1; i <= 13; i++) {
    const val = parseFloat(document.getElementById("WtRet" + i).value);
    if (!isNaN(val)) {
      startIndex = i;
      break;
    }
  }

  // Limpiar campos anteriores a startIndex para que no muestren nada
  for (let i = 1; i < startIndex; i++) {
    document.getElementById("Ret" + i).value = "";
    document.getElementById("CumRet" + i).value = "";
    document.getElementById("Pass" + i).value = "";
    PassArray[i - 1] = null; // o NaN
  }

  // Calcular desde startIndex en adelante
  for (let i = startIndex; i <= 13; i++) {
    const WtRet = parseFloat(document.getElementById("WtRet" + i).value);

    let Ret = NaN;
    if (!isNaN(WtRet) && DrySoil > 0) {
      Ret = (WtRet / DrySoil) * 100;
    }

    let CumRet = NaN;
    if (!isNaN(Ret)) {
      CumRet = lastCumRet + Ret;
      lastCumRet = CumRet;
    } else {
      CumRet = lastCumRet;
    }
    cumRetArray.push(CumRet);

    const Pass = !isNaN(CumRet) ? 100 - CumRet : NaN;

    PassArray[i - 1] = Pass;

    document.getElementById("Ret" + i).value = isNaN(Ret) ? '' : Ret.toFixed(2);
    document.getElementById("CumRet" + i).value = isNaN(CumRet) ? '' : CumRet.toFixed(2);
    document.getElementById("Pass" + i).value = isNaN(Pass) ? '' : Pass.toFixed(2);
  }

  const PanRet = DrySoil > 0 ? (PanWtRen / DrySoil) * 100 : NaN;
  const TotalWtRet = PanWtRen + WashPan;
  const TotalRet = DrySoil > 0 ? (TotalWtRet / DrySoil) * 100 : NaN;
  const TotalCumRet = lastCumRet + TotalRet;
  const TotalPass = !isNaN(TotalCumRet) ? Math.abs(100 - TotalCumRet) : NaN;

  // Mostrar resultados generales
  document.getElementById("DrySoil").value = isNaN(DrySoil) ? '' : DrySoil.toFixed(2);
  document.getElementById("WashPan").value = isNaN(WashPan) ? '' : WashPan.toFixed(2);
  document.getElementById("PanRet").value = isNaN(PanRet) ? '' : PanRet.toFixed(2);
  document.getElementById("TotalWtRet").value = isNaN(TotalWtRet) ? '' : TotalWtRet.toFixed(2);
  document.getElementById("TotalRet").value = isNaN(TotalRet) ? '' : TotalRet.toFixed(2);
  document.getElementById("TotalCumRet").value = isNaN(TotalCumRet) ? '' : TotalCumRet.toFixed(2);
  document.getElementById("TotalPass").value = isNaN(TotalPass) ? '' : TotalPass.toFixed(2);

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

  calcularParametrosGranulometricos(datos);
  UpdateGraph();

}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form.row");
  if (form) {
    form.querySelectorAll("input").forEach(input => {
      input.addEventListener("input", LPF);
    });
  }
});

document.querySelectorAll('[data-exportar]').forEach((el) => {
  el.addEventListener('click', () => {
    const tipo = el.dataset.exportar;
    enviarImagenAlServidor(tipo, ["GrainSizeChart"]);
  });
});

document.getElementById("reviewBtn").addEventListener("click", () => {
  
  const specs = {
    2:  [100,100],   // 3” 
    5:  [80,100],    // 3/4"
    6:  [70,100],    // 3/8"
    7:  [60,100],    // No.4
    8:  [50,100],    // No.10
    13: [25,94]      // No.200
  };

  const names = {
    2: '3"',
    5: '3/4"',
    6: '3/8"',
    7: 'No.4',
    8: 'No.10',
    13: 'No.200'
  };

  let html = "";

  for (let i in specs) {
    const passValue = parseFloat(document.getElementById("Pass"+i).value);
    const min = specs[i][0];
    const max = specs[i][1];

    const ok = (!isNaN(passValue) && passValue >= min && passValue <= max);
    const badge = ok
      ? "<span class='badge bg-success'>PASS</span>"
      : "<span class='badge bg-danger'>FAIL</span>";

    html += `
      <tr>
        <td>${names[i]}</td>
        <td>${isNaN(passValue) ? '-' : passValue.toFixed(2)}</td>
        <td>${min} - ${max}</td>
        <td>${badge}</td>
      </tr>
    `;
  }

  document.getElementById("reviewTableBody").innerHTML = html;

  const modal = new bootstrap.Modal(document.getElementById("reviewModal"));
  modal.show();
});
