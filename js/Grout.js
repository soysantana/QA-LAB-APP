function GROUT() {
    let totalInputs = 0;
    let sumaStrenght = 0;

    for (let i = 1; i <= 5; i++) {
        const Diameter = parseFloat(document.getElementById("DiameterNo" + i).value);
        const High = parseFloat(document.getElementById("HighNo" + i).value);
        const Length = parseFloat(document.getElementById("LengthNo" + i).value);
        const WeigCy = parseFloat(document.getElementById("WeigCyNo" + i).value);
        const Strenght = parseFloat(document.getElementById("StrenghtNo" + i).value);

        // Verifica si el valor de Strenght es válido (no NaN)
        if (!isNaN(Strenght)) {
            sumaStrenght += Strenght; // Suma al total de Strenghts
            totalInputs++; // Incrementa la cantidad de valores válidos
        }

        const Area = (Diameter * Length) / 1000000;
        const Vol = Area * (High / 1000);
        const UnitWeig = (WeigCy / Vol);

        // Result
        document.getElementById("AreaNo" + i).value = Area.toFixed(4);
        document.getElementById("VolNo" + i).value = Vol.toFixed(6);
        document.getElementById("UnitWeigNo" + i).value = UnitWeig.toFixed(0);

        // Calcula y asigna el promedio de Strenght dentro del bucle
        if (totalInputs > 0) {
            const averageStrenght = sumaStrenght / totalInputs;
            document.getElementById("AverageNo1").value = averageStrenght.toFixed(2);
        }
    }
}

function showImage(input, type) {
    const fileInput = input;
    const imageContainer = document.getElementById(`imageContainer${type.charAt(0).toUpperCase() + type.slice(1)}`);

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            const imgElement = document.createElement("img");
            imgElement.src = e.target.result;
            imgElement.classList.add("img-fluid");

            imageContainer.innerHTML = "";
            imageContainer.appendChild(imgElement);
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imageContainer.innerHTML = "<p>No se seleccionó ninguna imagen</p>";
    }
}