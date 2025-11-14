import { clasificarSuelo } from './gs-classification.js';

function calcularParametrosGranulometricos(datos) {
  const valoresBuscados = [10, 15, 30, 60, 85];
  const indiceColumnaBusqueda = 0;

  const minPass = Math.min(...datos.map(fila => fila[0]));
  const maxPass = Math.max(...datos.map(fila => fila[2]));
  const valoresValidos = valoresBuscados.filter(v => v >= minPass && v <= maxPass);

  // Buscar dos puntos para interpolar un Dx
  const buscarPar = (valor) => {
    for (let i = 0; i < datos.length; i++) {
      const [y1, x1, y2, x2] = datos[i];
      if (
        y1 <= valor && valor <= y2 &&
        x1 != null && x2 != null &&
        y1 != null && y2 != null
      ) {
        return { x1, x2, y1, y2 };
      }
    }
    return null;
  };

  // Interpolación logarítmica
  const calcularDx = ({ x1, x2, y1, y2 }, valor) => {
    const lnX1 = Math.log(x1);
    const lnX2 = Math.log(x2);
    const c = (y2 - y1) / (lnX2 - lnX1);
    const b = (y1 + y2) / 2 - c * (lnX1 + lnX2) / 2;
    return Math.exp((valor - b) / c);
  };

  // Buscar pasante directamente por tamiz (columna 1)
  const buscarPasantePorTamiz = (tamizObjetivo) => {
    for (const fila of datos) {
      const pasante = fila[0];
      const tamiz = fila[1];
      if (tamiz === tamizObjetivo) return pasante != null ? pasante : 100;
    }
    return 100; // Si no se encuentra, asumir 100%
  };


  const DxMap = {};
  valoresBuscados.forEach(valor => {
    const par = buscarPar(valor);
    const input = document.getElementById("D" + valor);
    if (valoresValidos.includes(valor) && par) {
      const resultado = calcularDx(par, valor);
      DxMap["D" + valor] = resultado;
      if (input) input.value = resultado.toFixed(3);
    } else {
      DxMap["D" + valor] = null;
      if (input) input.value = "-";
    }
  });

  const D10 = DxMap.D10;
  const D30 = DxMap.D30;
  const D60 = DxMap.D60;

  let Cu = "", Cc = "";

  if (
    !isNaN(D10) && D10 > 0 &&
    !isNaN(D30) && D30 > 0 &&
    !isNaN(D60) && D60 > 0
  ) {
    Cu = D60 / D10;
    Cc = (D30 ** 2) / (D60 * D10);
  }

  const ccInput = document.getElementById("Cc");
  const cuInput = document.getElementById("Cu");

  ccInput.value = typeof Cc === "number" && isFinite(Cc) ? Cc.toFixed(2) : "-";
  cuInput.value = typeof Cu === "number" && isFinite(Cu) ? Cu.toFixed(2) : "-";


  // Calcular gravel, sand, fines
  const sieve3In = buscarPasantePorTamiz(75);
  const sieveNo4 = buscarPasantePorTamiz(4.75);
  const sieveNo200 = buscarPasantePorTamiz(0.075);

  const CoarserGravel = 100 - sieve3In;
  const gravel = sieve3In - sieveNo4;
  const sand = sieveNo4 - sieveNo200;
  const fines = sieveNo200;

  // Clasificar el suelo
  const LL = parseFloat(document.getElementById("LiquidLimit")?.value) || null;
  const IP = parseFloat(document.getElementById("PlasticityIndex")?.value) || null;

  const clasificacion = clasificarSuelo(gravel, sand, fines, Cu, Cc, LL, IP);

  // Asignar los valores a los inputs
  document.getElementById("ClassificationUSCS1")?.setAttribute("value", clasificacion.description);
  document.getElementById("ClassificationUSCS2")?.setAttribute("value", clasificacion.code);

  document.getElementById("Classification1")?.setAttribute("value", `${clasificacion.code} - ${clasificacion.description}`);

  document.getElementById("CoarserGravel").value = !isNaN(CoarserGravel) ? CoarserGravel.toFixed(2) : "";
  document.getElementById("Gravel").value = !isNaN(gravel) ? gravel.toFixed(2) : "";
  document.getElementById("Sand").value = !isNaN(sand) ? sand.toFixed(2) : "";
  document.getElementById("Fines").value = !isNaN(fines) ? fines.toFixed(2) : "";

  return {
    D10, D15: DxMap.D15, D30, D60, D85: DxMap.D85,
    Cu, Cc,
    gravel, sand, fines
  };
}

export { calcularParametrosGranulometricos };
