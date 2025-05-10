/**
 * Function Grain Size
 */
function TRF() {
    const screenKeys = [
        "screen40",
        "screen30",
        "screen20",
        "screen13",
        "screen12",
        "screen10",
        "screen8",
        "screen6",
        "screen4",
        "screen3",
        "screen2",
        "screen1p5",
        "screen1",
        "screen3p4",
        "screen1p2",
        "screen3p8",
        "screenNo4",
        "screenNo20",
        "screenNo200",
        "screenPan"
    ];

    // Inicializa un objeto para acumular los totales
    const totals = {};
    screenKeys.forEach(key => (totals[key] = 0)); // Inicializa cada clave con 0

    // Itera sobre cada set de valores (del 1 al 10)
    for (let i = 1; i <= 10; i++) {
        screenKeys.forEach(key => {
            const element = document.getElementById(`${key}_${i}`);
            if (element) {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    totals[key] += value; // Suma el valor al total correspondiente
                }
            }
        });
    }

    // Asigna los totales formateados a los inputs correspondientes (sTotal_1, sTotal_2, etc.)
    screenKeys.forEach((key, index) => {
        const totalInput = document.getElementById(`sTotal_${index + 1}`); // Mapear a sTotal_1, sTotal_2, etc.
        if (totalInput) {
            totalInput.value = totals[key].toLocaleString("en-US"); // Formatea el total con separadores de miles
        }
    });

}

/**
 * Function to calculate the total of WtPhumedo inputs
 */
function calculatePasanteHumedo() {
    let total = 0; // Inicializa el total en 0

    // Itera sobre los 55 inputs
    for (let i = 1; i <= 55; i++) {
        const element = document.getElementById(`WtPhumedo_${i}`); // Obtiene el input por ID
        if (element) {
            const value = parseFloat(element.value); // Convierte el valor a número
            if (!isNaN(value)) {
                total += value; // Suma al total si es un número válido
            }
        }
    }

    // Asigna el total al input con ID "TDMPHumedo"
    const totalInput = document.getElementById("TDMPHumedo");
    if (totalInput) {
        totalInput.value = total.toLocaleString("en-US"); // Formatea el total con separadores de miles
    }
}

/**
 * Function to calculate the total of WtReSecoSucio_ inputs
 */
function calculateRepresentativoSecoSucio() {
    let total = 0; // Inicializa el total en 0

    // Itera sobre los 8 inputs
    for (let i = 1; i <= 8; i++) {
        const element = document.getElementById(`WtReSecoSucio_${i}`); // Obtiene el input por ID
        if (element) {
            const value = parseFloat(element.value); // Convierte el valor a número
            if (!isNaN(value)) {
                total += value; // Suma al total si es un número válido
            }
        }
    }

    // Asigna el total al input con ID "TDMRSecoSucio"
    const totalInput = document.getElementById("TDMRSecoSucio");
    if (totalInput) {
        totalInput.value = total.toLocaleString("en-US"); // Formatea el total con separadores de miles
    }

for (let i = 1; i <= 10; i++) {
    const sTotal = document.getElementById(`sTotal_${i}`); // Obtiene el input por ID
    if (sTotal) {
        const value = parseFloat(sTotal.value); // Convierte el valor a número
        if (!isNaN(value)) {
            total += value; // Suma al total si es un número válido
        }
        console.log("Suma total:", total); // Muestra el total final
    }
}
}

/**
 * Function to calculate the total of sTotal_ inputs
 */
function TotalMore3() {
    let total = 0; // Inicializa el total en 0

    for (let i = 1; i <= 10; i++) {
        const sTotal = document.getElementById(`sTotal_${i}`); // Obtiene el input por ID
        if (sTotal) {
                    const rawValue = sTotal.value.replace(',', '.');
        const value = parseFloat(rawValue); // Convierte el valor a número
            if (!isNaN(value)) {
                total += value; // Suma al total si es un número válido
            }
            console.log("Suma total:", total); // Muestra el total final
        }
    }
}

  $("input").on("blur", function(event) {
    event.preventDefault();
    TotalMore3();
  });