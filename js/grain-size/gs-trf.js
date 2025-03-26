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
            } else {
                console.warn(`Elemento con id "${key}_${i}" no encontrado.`);
            }
        });
    }

    // Asigna los totales formateados a los inputs correspondientes (sTotal_1, sTotal_2, etc.)
    screenKeys.forEach((key, index) => {
        const totalInput = document.getElementById(`sTotal_${index + 1}`); // Mapear a sTotal_1, sTotal_2, etc.
        if (totalInput) {
            totalInput.value = totals[key].toLocaleString("en-US"); // Formatea el total con separadores de miles
        } else {
            console.warn(`Elemento con id "sTotal_${index + 1}" no encontrado.`);
        }
    });

    console.log("Totales de los screens (formateados):", totals);
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
        } else {
            console.warn(`Elemento con id "WtPhumedo_${i}" no encontrado.`); // Muestra un aviso si el elemento no existe
        }
    }

    // Asigna el total al input con ID "TDMPHumedo"
    const totalInput = document.getElementById("TDMPHumedo");
    if (totalInput) {
        totalInput.value = total.toLocaleString("en-US"); // Formatea el total con separadores de miles
    } else {
        console.error('Elemento con id "TDMPHumedo" no encontrado.');
    }

    console.log("Total calculado:", total.toLocaleString("en-US")); // Muestra el total formateado en la consola
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
        } else {
            console.warn(`Elemento con id "WtReSecoSucio_${i}" no encontrado.`); // Muestra un aviso si el elemento no existe
        }
    }

    // Asigna el total al input con ID "TDMRSecoSucio"
    const totalInput = document.getElementById("TDMRSecoSucio");
    if (totalInput) {
        totalInput.value = total.toLocaleString("en-US"); // Formatea el total con separadores de miles
    } else {
        console.error('Elemento con id "TDMRSecoSucio" no encontrado.');
    }

    console.log("Total calculado:", total.toLocaleString("en-US")); // Muestra el total formateado en la consola
}