function calculateQualitativeExamination() {
    for (let i = 1; i <= 5; i++) {
        // Obtener valores y convertirlos a número (los vacíos se convierten en 0)
        const Splitting = parseFloat(document.getElementById("SplittingNo" + i).value) || 0;
        const Crumbling = parseFloat(document.getElementById("CrumblingNo" + i).value) || 0;
        const Cracking = parseFloat(document.getElementById("CrackingNo" + i).value) || 0;
        const Flaking = parseFloat(document.getElementById("FlakingNo" + i).value) || 0;
        const TotalParticles = parseFloat(document.getElementById("TotalParticles" + i).value) || 0;

        // Solo calcular si TotalParticles es mayor que 0
        if (TotalParticles > 0) {
            // Calcular porcentajes (incluso si algunos valores son 0)
            const SplittingPct = (Splitting / TotalParticles) * 100;
            const CrumblingPct = (Crumbling / TotalParticles) * 100;
            const CrackingPct = (Cracking / TotalParticles) * 100;
            const FlakingPct = (Flaking / TotalParticles) * 100;

            // Asignar resultados (formateados sin decimales)
            document.getElementById("SplittingPct" + i).value = SplittingPct.toFixed(0);
            document.getElementById("CrumblingPct" + i).value = CrumblingPct.toFixed(0);
            document.getElementById("CrackingPct" + i).value = CrackingPct.toFixed(0);
            document.getElementById("FlakingPct" + i).value = FlakingPct.toFixed(0);
        } else {
            // Si TotalParticles es 0 o vacío, limpiar los resultados
            document.getElementById("SplittingPct" + i).value = '';
            document.getElementById("CrumblingPct" + i).value = '';
            document.getElementById("CrackingPct" + i).value = '';
            document.getElementById("FlakingPct" + i).value = '';
        }
    }
}

function updateDates() {
    const baseInput = document.getElementById('StartDate1');
    const baseDate = new Date(baseInput.value);

    if (isNaN(baseDate)) return;

    for (let i = 2; i <= 5; i++) {
        const newDate = new Date(baseDate);
        newDate.setDate(baseDate.getDate() + (i - 1));
        document.getElementById(`StartDate${i}`).value = newDate.toISOString().split('T')[0];
    }
}

// Array to store percentages for coarse aggregate
// This will be used in the soundness coarse calculation
let PctRetCoarseArray = [];

function calculateGrainSizeCoarse() {
    let WtRetCoarseTotal = 0;

    // calcular el total
    for (let i = 1; i <= 11; i++) {
        const WtRetCoarse = parseFloat(document.getElementById("WtRetCoarse" + i).value);
        if (!isNaN(WtRetCoarse) && WtRetCoarse !== 0) {
            WtRetCoarseTotal += WtRetCoarse;
        }
    }

    // calcular y mostrar porcentajes
    for (let i = 1; i <= 11; i++) {
        const WtRetCoarseEl = document.getElementById("WtRetCoarse" + i);
        const PctRetCoarseEl = document.getElementById("PctRetCoarse" + i);
        const WtRetCoarse = parseFloat(WtRetCoarseEl.value);

        if (!isNaN(WtRetCoarse) && WtRetCoarseTotal > 0) {
            const PctRetCoarse = (WtRetCoarse / WtRetCoarseTotal) * 100;
            PctRetCoarseEl.value = PctRetCoarse.toFixed(2);
            PctRetCoarseArray[i - 1] = PctRetCoarse;
        } else {
            // Si no hay valor en WtRetCoarse, dejar el porcentaje vacío
            PctRetCoarseEl.value = '';
            PctRetCoarseArray[i - 1] = null;
        }
    }

    // calculoPctRetCoarseTotal
    const PctRetCoarseTotal = (WtRetCoarseTotal / WtRetCoarseTotal) * 100;
    document.getElementById("PctRetCoarseTotal").value = isNaN(PctRetCoarseTotal) ? '' : PctRetCoarseTotal.toFixed(2);

    document.getElementById("WtRetCoarseTotal").value = isNaN(WtRetCoarseTotal) ? '' : WtRetCoarseTotal.toFixed(2);
}

// Array to store percentages for fine aggregate
// This will be used in the soundness fine calculation
let PctRetFineArray = [];

function calculateGrainSizeFine() {
    let WtRetFineTotal = 0;

    // calcular el total
    for (let i = 1; i <= 7; i++) {
        const WtRetFine = parseFloat(document.getElementById("WtRetFine" + i).value);
        if (!isNaN(WtRetFine) && WtRetFine !== 0) {
            WtRetFineTotal += WtRetFine;
        }
    }

    // calcular y mostrar porcentajes
    for (let i = 1; i <= 7; i++) {
        const WtRetFineEl = document.getElementById("WtRetFine" + i);
        const PctRetFineEl = document.getElementById("PctRetFine" + i);
        const WtRetFine = parseFloat(WtRetFineEl.value);

        if (!isNaN(WtRetFine) && WtRetFineTotal > 0) {
            const PctRetFine = (WtRetFine / WtRetFineTotal) * 100;
            PctRetFineEl.value = PctRetFine.toFixed(2);
            PctRetFineArray[i - 1] = PctRetFine;
        } else {
            // Si no hay valor en WtRetFine, dejar el porcentaje vacío
            PctRetFineEl.value = '';
            PctRetFineArray[i - 1] = null;
        }
    }

    // calculoPctRetFineTotal
    const PctRetFineTotal = (WtRetFineTotal / WtRetFineTotal) * 100;
    document.getElementById("PctRetFineTotal").value = isNaN(PctRetFineTotal) ? '' : PctRetFineTotal.toFixed(2);

    document.getElementById("WtRetFineTotal").value = isNaN(WtRetFineTotal) ? '' : WtRetFineTotal.toFixed(2);
}

function calculateSoundnessFine() {
    let TotalStarWeightRet = 0;
    let TotalFinalWeightRet = 0;
    let TotalWeightedLoss = 0;
    let percentageArray = [];

    for (let i = 1; i <= 7; i++) {
        const StarWeightRet = parseFloat(document.getElementById("StarWeightRet" + i).value);
        const FinalWeightRet = parseFloat(document.getElementById("FinalWeightRet" + i).value);

        if (!isNaN(StarWeightRet) && StarWeightRet !== 0) {
            TotalStarWeightRet += StarWeightRet;

            const PercentagePassing = ((StarWeightRet - FinalWeightRet) / StarWeightRet) * 100;
            percentageArray.push(PercentagePassing);
            document.getElementById("PercentagePassing" + i).value = isNaN(PercentagePassing) ? '' : PercentagePassing.toFixed(2);


        } else {
            document.getElementById("PercentagePassing" + i).value = '';
            percentageArray[i - 1] = null;
        }

        if (!isNaN(FinalWeightRet) && FinalWeightRet !== 0) {
            TotalFinalWeightRet += FinalWeightRet;
        }
    }

    for (let i = 0; i < 7; i++) {
        const FinalWetRet = percentageArray[i];
        const RetFine = PctRetFineArray[6 - i];
        let WeightedLoss = null;

        if (FinalWetRet !== null && RetFine !== null) {
            WeightedLoss = (FinalWetRet * RetFine) / 100;
            TotalWeightedLoss += WeightedLoss;
            document.getElementById("WeightedLoss" + (i + 1)).value = isNaN(WeightedLoss) ? '' : WeightedLoss.toFixed(2);
        } else {
            document.getElementById("WeightedLoss" + (i + 1)).value = '';
        }
    }

    // Total Resultado StarWeightRet
    document.getElementById("TotalStarWeightRet").value = isNaN(TotalStarWeightRet) ? '' : TotalStarWeightRet.toFixed(2);
    // Total Resultado FinalWeightRet
    document.getElementById("TotalFinalWeightRet").value = isNaN(TotalFinalWeightRet) ? '' : TotalFinalWeightRet.toFixed(2);
    // Total Resultado WeightedLoss
    document.getElementById("TotalWeightedLoss").value = isNaN(TotalWeightedLoss) ? '' : TotalWeightedLoss.toFixed(2);
}

function calculateSoundnessCoarse() {
    let TotalStarWeightRetCoarse = 0;
    let TotalFinalWeightRetCoarse = 0;
    let StarWeightRetCoarseArray = [];
    let FinalWeightRetCoarseArray = [];

    for (let i = 1; i <= 10; i++) {
        const StarWeightRetCoarse = parseFloat(document.getElementById("StarWeightRetCoarse" + i).value);
        StarWeightRetCoarseArray.push(StarWeightRetCoarse);

        if (!isNaN(StarWeightRetCoarse) && StarWeightRetCoarse !== 0) {
            TotalStarWeightRetCoarse += StarWeightRetCoarse;
        }
    }

    for (let i = 1; i <= 7; i++) {
        const FinalWeightRetCoarse = parseFloat(document.getElementById("FinalWeightRetCoarse" + i).value);
        FinalWeightRetCoarseArray.push(FinalWeightRetCoarse);

        if (!isNaN(FinalWeightRetCoarse) && FinalWeightRetCoarse !== 0) {
            TotalFinalWeightRetCoarse += FinalWeightRetCoarse;
        }
    }

    const PercentagePassingCoarse1 = ((StarWeightRetCoarseArray[0] - FinalWeightRetCoarseArray[0]) / StarWeightRetCoarseArray[0]) * 100;
    const PercentagePassingCoarse2 = ((StarWeightRetCoarseArray[1] + StarWeightRetCoarseArray[2]) - FinalWeightRetCoarseArray[1]) / (StarWeightRetCoarseArray[1] + StarWeightRetCoarseArray[2]) * 100;
    const PercentagePassingCoarse3 = ((StarWeightRetCoarseArray[3] + StarWeightRetCoarseArray[4]) - FinalWeightRetCoarseArray[2]) / (StarWeightRetCoarseArray[3] + StarWeightRetCoarseArray[4]) * 100;
    const PercentagePassingCoarse4 = ((StarWeightRetCoarseArray[5] + StarWeightRetCoarseArray[6]) - FinalWeightRetCoarseArray[3]) / (StarWeightRetCoarseArray[5] + StarWeightRetCoarseArray[6]) * 100;
    const PercentagePassingCoarse5 = ((StarWeightRetCoarseArray[7] - FinalWeightRetCoarseArray[4]) / TotalStarWeightRetCoarse);
    const PercentagePassingCoarse6 = ((StarWeightRetCoarseArray[8] - FinalWeightRetCoarseArray[5]) / TotalStarWeightRetCoarse);
    const PercentagePassingCoarse7 = ((StarWeightRetCoarseArray[9] - FinalWeightRetCoarseArray[6]) / TotalStarWeightRetCoarse);

    document.getElementById("PercentagePassingCoarse1").value = isNaN(PercentagePassingCoarse1) ? '' : PercentagePassingCoarse1.toFixed(3);
    document.getElementById("PercentagePassingCoarse2").value = isNaN(PercentagePassingCoarse2) ? '' : PercentagePassingCoarse2.toFixed(3);
    document.getElementById("PercentagePassingCoarse3").value = isNaN(PercentagePassingCoarse3) ? '' : PercentagePassingCoarse3.toFixed(3);
    document.getElementById("PercentagePassingCoarse4").value = isNaN(PercentagePassingCoarse4) ? '' : PercentagePassingCoarse4.toFixed(3);
    document.getElementById("PercentagePassingCoarse5").value = isNaN(PercentagePassingCoarse5) ? '' : PercentagePassingCoarse5.toFixed(5);
    document.getElementById("PercentagePassingCoarse6").value = isNaN(PercentagePassingCoarse6) ? '' : PercentagePassingCoarse6.toFixed(5);
    document.getElementById("PercentagePassingCoarse7").value = isNaN(PercentagePassingCoarse7) ? '' : PercentagePassingCoarse7.toFixed(5);

    // Array de factores por grupo
    const WeightedLossCoarse = [];

    WeightedLossCoarse[1] = (PercentagePassingCoarse1 * PctRetCoarseArray[10]) / 100;
    WeightedLossCoarse[2] = (PercentagePassingCoarse2 * PctRetCoarseArray[9] + PctRetCoarseArray[8]) / 100;
    WeightedLossCoarse[3] = (PercentagePassingCoarse3 * PctRetCoarseArray[6] + PctRetCoarseArray[7]) / 100;
    WeightedLossCoarse[4] = (PercentagePassingCoarse4 * PctRetCoarseArray[4] + PctRetCoarseArray[5]) / 100;
    WeightedLossCoarse[5] = (PercentagePassingCoarse5 * PctRetCoarseArray[3]) / 100;
    WeightedLossCoarse[6] = (PercentagePassingCoarse6 * PctRetCoarseArray[2]) / 100;
    WeightedLossCoarse[7] = (PercentagePassingCoarse7 * PctRetCoarseArray[1]) / 100;

    // Decimales por índice
    const decimalPlaces = {
        1: 3,
        2: 3,
        3: 3,
        4: 3,
        5: 5,
        6: 5,
        7: 5
    };

    // Mostrar en los campos
    for (let i = 1; i <= 7; i++) {
        const value = WeightedLossCoarse[i];
        const fixed = isNaN(value) ? '' : value.toFixed(decimalPlaces[i]);
        document.getElementById("WeightedLossCoarse" + i).value = fixed;
    }


    // Total Resultado WeightedLossCoarse
    const TotalWeightedLossCoarse = [1, 2, 3, 4, 5, 6, 7].reduce((sum, i) => {
        const val = parseFloat(WeightedLossCoarse[i]);
        return sum + (isNaN(val) ? 0 : val);
    }, 0);

    document.getElementById("TotalWeightedLossCoarse").value = isNaN(TotalWeightedLossCoarse) ? '' : TotalWeightedLossCoarse.toFixed(2);


    // Total Resultado StarWeightRetCoarse
    document.getElementById("TotalStarWeightRetCoarse").value = isNaN(TotalStarWeightRetCoarse) ? '' : TotalStarWeightRetCoarse.toFixed(2);
    // Total Resultado FinalWeightRetCoarse
    document.getElementById("TotalFinalWeightRetCoarse").value = isNaN(TotalFinalWeightRetCoarse) ? '' : TotalFinalWeightRetCoarse.toFixed(2);
}

$("input").on("input", function (event) {
    event.preventDefault();
    calculateQualitativeExamination();
    updateDates();
    calculateGrainSizeCoarse();
    calculateGrainSizeFine();
    calculateSoundnessFine();
    calculateSoundnessCoarse();
});