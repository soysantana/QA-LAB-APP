function MoistureOven() {

    const WetSoil = parseFloat(document.getElementById("WetSoil").value);
    const DrySoil = parseFloat(document.getElementById("DrySoil").value);
    const Tare = parseFloat(document.getElementById("Tare").value);

    // ----
    const Water = WetSoil - DrySoil;
    const DrySoilWs = DrySoil - Tare;
    const Moisture = (Water / DrySoilWs)*100;

    // ---
    document.getElementById("Water").value = Water.toFixed(2);
    document.getElementById("DrySoilWs").value = DrySoilWs.toFixed(2);
    document.getElementById("Moisture").value = Moisture.toFixed(2) + "%";
}

function MoistureMicrowave() {
    const WetSoil = parseFloat(document.getElementById("WetSoil").value);
    const Tare = parseFloat(document.getElementById("Tare").value);

    let lastWetSoilValue = WetSoil;

    for (let i = 1; i <= 5; i++) {
        const currentWetSoilValue = parseFloat(document.getElementById("WetSoil" + i).value);

        if (!isNaN(currentWetSoilValue)) {
            lastWetSoilValue = currentWetSoilValue;
        }
    }

    const Water = WetSoil - lastWetSoilValue;
    const DrySoilWs = lastWetSoilValue - Tare;
    const Moisture = (Water / DrySoilWs)*100;

    document.getElementById("Water").value = Water.toFixed(2);
    document.getElementById("DrySoilWs").value = DrySoilWs.toFixed(2);
    document.getElementById("Moisture").value = Moisture.toFixed(2) + "%";
}

function MoistureConstantMass() {
    const WetSoil = parseFloat(document.getElementById("WetSoil").value);
    const Tare = parseFloat(document.getElementById("Tare").value);

    let lastWetSoilValue = WetSoil;

    for (let i = 1; i <= 5; i++) {
        const currentWetSoilValue = parseFloat(document.getElementById("WetSoil" + i).value);

        if (!isNaN(currentWetSoilValue)) {
            lastWetSoilValue = currentWetSoilValue;
        }
    }

    const Water = WetSoil - lastWetSoilValue;
    const DrySoilWs = lastWetSoilValue - Tare;
    const Moisture = (Water / DrySoilWs)*100;

    document.getElementById("Water").value = Water.toFixed(2);
    document.getElementById("DrySoilWs").value = DrySoilWs.toFixed(2);
    document.getElementById("Moisture").value = Moisture.toFixed(2) + "%";
}