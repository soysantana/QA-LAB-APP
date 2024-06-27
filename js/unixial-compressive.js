function UCS() {
    // Obtener los valores
    const DimensionD = parseFloat(document.getElementById("DimensionD").value);
    const DimensionH = parseFloat(document.getElementById("DimensionH").value);
    const WeightKg = parseFloat(document.getElementById("WeightKg").value);

    // Calculos
    const RelationHD = DimensionH / DimensionD;
    const AreaM2 = Math.PI * (DimensionD / 2) ** 2 * 0.0001;
    const VolM3 = AreaM2 * (DimensionH * 0.01);
    const UnitWeigKgm3 = WeightKg / VolM3;

    // Result
    document.getElementById("RelationHD").value = RelationHD.toFixed(2);
    document.getElementById("AreaM2").value = AreaM2.toFixed(5);
    document.getElementById("VolM3").value = VolM3.toFixed(6);
    document.getElementById("UnitWeigKgm3").value = UnitWeigKgm3.toFixed(0);
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