function BTT() {
    let totalDcm = 0;
    let countDcm = 0;
    let totalTcm = 0;
    let totalTimeFai = 0;
    let totalMaxKn = 0;
    let totalReltd = 0;
    let totalLoand = 0;
    let totalTensStr = 0;

    for (let i = 1; i <= 10; i++) {
        const DcmInput = document.getElementById("DcmNo" + i);
        const Dcm = parseFloat(DcmInput.value);
        const Tcm = parseFloat(document.getElementById("TcmNo" + i).value) || 0;
        const TimeFai = parseFloat(document.getElementById("TimeFaiNo" + i).value) || 0;
        const MaxKn = parseFloat(document.getElementById("MaxKnNo" + i).value) || 0;

        if (!isNaN(Dcm) && Dcm !== 0) {
            totalDcm += Dcm;
            countDcm++;

            if (Tcm !== 0 && TimeFai !== 0 && MaxKn !== 0) {
                const Reltd = Tcm / Dcm;
                const Loand = MaxKn / TimeFai;
                const TensStr = (2 * MaxKn) / (Math.PI * Tcm * Dcm) * 10;

                totalReltd += Reltd;
                totalLoand += Loand;
                totalTensStr += TensStr;

                document.getElementById("ReltdNo" + i).value = Reltd.toFixed(2);
                document.getElementById("LoandNo" + i).value = Loand.toFixed(3);
                document.getElementById("TensStrNo" + i).value = TensStr.toFixed(2);
            }
        }

        if (Tcm !== 0) {
            totalTcm += Tcm;
        }

        if (TimeFai !== 0) {
            totalTimeFai += TimeFai;
        }

        if (MaxKn !== 0) {
            totalMaxKn += MaxKn;
        }
    }

    const averageDcm = countDcm > 0 ? totalDcm / countDcm : 0;
    const averageTcm = countDcm > 0 ? totalTcm / countDcm : 0;
    const averageTimeFai = countDcm > 0 ? totalTimeFai / countDcm : 0;
    const averageMaxKn = countDcm > 0 ? totalMaxKn / countDcm : 0;
    const averageReltd = countDcm > 0 ? totalReltd / countDcm : 0;
    const averageLoand = countDcm > 0 ? totalLoand / countDcm : 0;
    const averageTensStr = countDcm > 0 ? totalTensStr / countDcm : 0;

    document.getElementById("DcmNoAvge").value = averageDcm.toFixed(1);
    document.getElementById("TcmNoAvge").value = averageTcm.toFixed(1);
    document.getElementById("TimeFaiNoAvge").value = averageTimeFai.toFixed(1);
    document.getElementById("MaxKnNoAvge").value = averageMaxKn.toFixed(1);
    document.getElementById("ReltdNoAvge").value = averageReltd.toFixed(1);
    document.getElementById("LoandNoAvge").value = averageLoand.toFixed(1);
    document.getElementById("TensStrNoAvge").value = averageTensStr.toFixed(1);
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