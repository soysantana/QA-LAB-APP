function PLT() {
    // Obtener los valores
    const JackPiston = parseFloat(document.getElementById("JackPiston").value);
    const K1assumed = parseFloat(document.getElementById("K1assumed").value);
    const K2assumed = parseFloat(document.getElementById("K2assumed").value);
    const MethodTest = document.getElementById("MethodTest").value;
    const DimensionL = parseFloat(document.getElementById("DimensionL").value);
    const DimensionD = parseFloat(document.getElementById("DimensionD").value);
    const PlattensSeparation = parseFloat(document.getElementById("PlattensSeparation").value);
    const GaugeReading = parseFloat(document.getElementById("GaugeReading").value);
    
    var data = [
        { key: "Diametral", letter: DimensionL > (0.5 * DimensionD) ? "A" : "False" },
        { key: "Axial", letter: (DimensionD > (0.3 * DimensionL) && DimensionL < DimensionD) ? "B" : "False" },
        { key: "Block", letter: "C" },
        { key: "Irregular Lump", letter: "D" },
    ];

    function buscarValor() {
        for (var i = 0; i < data.length; i++) {
            if (data[i].key === MethodTest) {
                return data[i].letter;
            }
        }
        return "";
    }

    var TypeABCD = buscarValor();

    const FailureLoad = GaugeReading > 0 ? GaugeReading * JackPiston : 0;
    const Demm = TypeABCD === "B" ? Math.sqrt(4 * DimensionD * PlattensSeparation / Math.PI()) :
             TypeABCD === "A" ? PlattensSeparation :
             TypeABCD === "C" ? Math.sqrt(4 * DimensionD * PlattensSeparation / Math.PI()) :
             TypeABCD === "D" ? Math.sqrt(4 * DimensionD * PlattensSeparation / Math.PI()) :
             "False";
             
    const IsMpa = FailureLoad / Math.pow(Demm / 1000, 2);
    const F = Math.pow(Demm / 50, 0.45);
    const Is50 = F * IsMpa;
    const UCSK1Mpa = Is50 * K1assumed;
    const UCSK2Mpa = Is50 * K2assumed;

    function Classification(IsMpa) {
        if (IsMpa < 0.03) {
            return 'Extremly Low';
        } else if ( IsMpa >= 0.03 && IsMpa < 0.1) {
            return 'Very Low';
        } else if (IsMpa >= 0.1 && IsMpa < 0.3) {
            return 'Low';
        } else if (IsMpa >= 0.3 && IsMpa < 1) {
            return 'Medium';
        } else if (IsMpa >= 1 && IsMpa < 3) {
            return 'High';
        } else if (IsMpa >= 3 && IsMpa < 10) {
            return 'Very High';
        } else {
            return 'Extremelly High';
        }
    }

    // Result
    document.getElementById("TypeABCD").value = TypeABCD;
    document.getElementById("FailureLoad").value = FailureLoad.toFixed(3);
    document.getElementById("Demm").value = Demm;
    document.getElementById("IsMpa").value = IsMpa.toFixed(3);
    document.getElementById("F").value = F.toFixed(3);
    document.getElementById("Is50").value = Is50.toFixed(3);
    document.getElementById("UCSK1Mpa").value = UCSK1Mpa.toFixed(3);
    document.getElementById("UCSK2Mpa").value = UCSK2Mpa.toFixed(3);
    document.getElementById("Classification").value = Classification(IsMpa);
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