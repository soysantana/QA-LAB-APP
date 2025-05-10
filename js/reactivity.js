function reactivityTest(method) {
    // Determina el número de campos a evaluar según el método
    var numFields = method === "FM13-007" ? 3 : 5;

    var total = 0;
    var count = 0;

    for (var i = 1; i <= numFields; i++) {
        var elementId = "Particles" + i;
        var element = document.getElementById(elementId);

        if (element && !isNaN(parseFloat(element.value))) {
            total += parseFloat(element.value);
            count++;
        }
    }

    if (count >= 1) {
        var avgParticles = total / count;
        var reactionResult;
        var AcidResult;

        // Clasificación de la reacción
        if (avgParticles >= 30) {
            reactionResult = "Strong Reaction";
        } else if (avgParticles >= 16) {
            reactionResult = "Moderate Reaction";
        } else if (avgParticles >= 1) {
            reactionResult = "Weak Reaction";
        } else {
            reactionResult = "No Reaction";
        }

        // Resultado de aceptabilidad
        if (reactionResult === "Strong Reaction") {
            AcidResult = "Rejected";
        } else {
            AcidResult = "Accepted";
        }

        document.getElementById("AcidResult").value = AcidResult;
        document.getElementById("ReactionResult").value = reactionResult;
        document.getElementById("AvgParticles").value = avgParticles.toFixed(0);
    }
}
