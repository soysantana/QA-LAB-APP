function calcularPromedio() {
  const inputs = document.querySelectorAll('.LeebHardnessInput');
  let suma = 0;
  let contador = 0;

  inputs.forEach(input => {
    const valor = parseFloat(input.value);
    if (!isNaN(valor)) {
      suma += valor;
      contador++;
    }
  });

  const promedio = contador > 0 ? (suma / contador).toFixed(0) : 0;
  document.getElementById('resultadoPromedio').value = 
    contador > 0 
      ? `${promedio}`
      : 'No se ingresaron valores válidos';
}