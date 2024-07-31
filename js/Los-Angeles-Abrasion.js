function laaLarge() {

    const InitWeig = parseFloat(document.getElementById("InitWeig").value);
    const FinalWeig = parseFloat(document.getElementById("FinalWeig").value);

    // ----
    const WeigLoss = InitWeig - FinalWeig;
    const WeigLossPorce = (WeigLoss / InitWeig) * 100;

    // ---
    document.getElementById("WeigLoss").value = WeigLoss.toFixed(2);
    document.getElementById("WeigLossPorce").value = WeigLossPorce.toFixed(2);
}
