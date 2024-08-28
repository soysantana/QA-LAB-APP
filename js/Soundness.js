/**
 * Soundness Calculation
 * 
 * This function calculates various percentages and weight distributions
 * based on user inputs related to fine and coarse aggregates, as well as
 * a qualitative examination of coarse sizes.
 * 
 * The function validates inputs to ensure they are numbers and handles cases
 * where division by zero might occur. Results are displayed in corresponding input fields.
 */

/**
 * Helper function to calculate and display the percentage retained.
 * 
 * @param {number} value - The numerator (WtRet or similar).
 * @param {number} total - The denominator (WtDrySoil or similar).
 * @param {string} outputId - The ID of the output element where the result will be displayed.
 */
function calculateAndDisplayPercentage(value, total, outputId) {
    const outputElement = document.getElementById(outputId);
    if (!isNaN(value) && total > 0) {
        const percentage = (value / total) * 100;
        outputElement.value = percentage.toFixed(2);
    } else {
        outputElement.value = ""; // Clear the field if the value is invalid
    }
}

function Soundness() {
    // Obtain the value of WtDrySoil
    const WtDrySoil = parseFloat(document.getElementById("WtDrySoil").value);

    // Validate WtDrySoil to avoid division by zero
    if (isNaN(WtDrySoil) || WtDrySoil <= 0) {
        alert("Wt Dry Soil(g) No puede estar vacio.");
        return;
    }

    let totalWtRet = 0;

    // Loop through each set of inputs (1 to 16) and calculate PctRet
    for (let i = 1; i <= 16; i++) {
        const WtRet = parseFloat(document.getElementById("WtRet" + i).value);
        if (!isNaN(WtRet)) {
            totalWtRet += WtRet;
        }
        calculateAndDisplayPercentage(WtRet, WtDrySoil, "PctRet" + i);
    }

    // Calculate WtRetTotalPan and WtRetTotal based on new calculations
    const WtRetTotalPan = WtDrySoil - totalWtRet;
    const WtRetTotal = totalWtRet + WtRetTotalPan;

    // Display results for total pan and total retained weight
    document.getElementById("WtRetTotalPan").value = WtRetTotalPan.toFixed(2);
    document.getElementById("WtRetTotal").value = WtRetTotal.toFixed(2);

    // Obtain the value for WtRetPan
    const WtRetPan = parseFloat(document.getElementById("WtRetPan").value);

    // Calculate and display percentages for Pan, TotalPan, and Total
    calculateAndDisplayPercentage(WtRetPan, WtDrySoil, "PctRetPan");
    calculateAndDisplayPercentage(WtRetTotalPan, WtDrySoil, "PctRetTotalPan");
    calculateAndDisplayPercentage(WtRetTotal, WtDrySoil, "PctRetTotal");

    // Grain Size Distribution for Coarse Aggregate

    let totalWtRetCoarse = 0;

    // Loop through each set of inputs (1 to 11) and calculate PctRetCoarse
    for (let i = 1; i <= 11; i++) {
        const WtRetCoarse = parseFloat(document.getElementById("WtRetCoarse" + i).value);
        if (!isNaN(WtRetCoarse)) {
            totalWtRetCoarse += WtRetCoarse;
        }
    }

    // Calculate and display percentages for coarse aggregates
    for (let i = 1; i <= 11; i++) {
        const WtRetCoarse = parseFloat(document.getElementById("WtRetCoarse" + i).value);
        calculateAndDisplayPercentage(WtRetCoarse, totalWtRetCoarse, "PctRetCoarse" + i);
    }

    // Calculate and display percentages for Total
    calculateAndDisplayPercentage(totalWtRetCoarse, totalWtRetCoarse, "PctRetCoarseTotal");

    // Display total weight retained for coarse aggregates
    document.getElementById("WtRetCoarseTotal").value = totalWtRetCoarse.toFixed(2);

    // Grain Size Distribution for Fine Aggregate

    let totalWtRetFine = 0;

    // Loop through each set of inputs (1 to 6) and calculate PctRetFine
    for (let i = 1; i <= 7; i++) {
        const WtRetFine = parseFloat(document.getElementById("WtRetFine" + i).value);
        if (!isNaN(WtRetFine)) {
            totalWtRetFine += WtRetFine;
        }
    }

    // Calculate and display percentages for Fine Aggregate
    let PctRetFineArray = [];
    for (let i = 1; i <= 7; i++) {
        const WtRetFine = parseFloat(document.getElementById("WtRetFine" + i).value);
        const pctRetFine = (WtRetFine / totalWtRetFine) * 100;
        calculateAndDisplayPercentage(WtRetFine, totalWtRetFine, "PctRetFine" + i);

        // Store the calculated PctRetFine for use in the next loop
        PctRetFineArray.push(pctRetFine);
    }

    // Calculate and display percentages for Total
    calculateAndDisplayPercentage(totalWtRetFine, totalWtRetFine, "PctRetFineTotal");

    // Display total weight retained for Fine Aggregate
    document.getElementById("WtRetFineTotal").value = totalWtRetFine.toFixed(2);

    // Qualitative Examination of Coarse Sizes

    // Loop through the remaining qualitative inputs and calculate percentages
    for (let i = 1; i <= 5; i++) {
        const SplittingNo = parseFloat(document.getElementById("SplittingNo" + i).value);
        const CrumblingNo = parseFloat(document.getElementById("CrumblingNo" + i).value);
        const CrackingNo = parseFloat(document.getElementById("CrackingNo" + i).value);
        const FlakingNo = parseFloat(document.getElementById("FlakingNo" + i).value);
        const TotalParticles = parseFloat(document.getElementById("TotalParticles" + i).value);

        // Calculate percentages only if TotalParticles is greater than 0 to avoid division by zero
        const SplittingPct = TotalParticles > 0 ? (SplittingNo / TotalParticles) * 100 : NaN;
        const CrumblingPct = TotalParticles > 0 ? (CrumblingNo / TotalParticles) * 100 : NaN;
        const CrackingPct = TotalParticles > 0 ? (CrackingNo / TotalParticles) * 100 : NaN;
        const FlakingPct = TotalParticles > 0 ? (FlakingNo / TotalParticles) * 100 : NaN;

        // Update the corresponding input fields with the calculated percentages
        document.getElementById("SplittingPct" + i).value = !isNaN(SplittingPct) ? SplittingPct.toFixed(0) : "";
        document.getElementById("CrumblingPct" + i).value = !isNaN(CrumblingPct) ? CrumblingPct.toFixed(0) : "";
        document.getElementById("CrackingPct" + i).value = !isNaN(CrackingPct) ? CrackingPct.toFixed(0) : "";
        document.getElementById("FlakingPct" + i).value = !isNaN(FlakingPct) ? FlakingPct.toFixed(0) : "";
    }

    // Test Result Soundness Fine Aggregate
    let totalStarWeightRet = 0;
    let totalFinalWeightRet = 0;
    let totalWeightedLoss = 0;

    // Loop through the inputs related to soundness and calculate results
    for (let i = 1; i <= 7; i++) {
        const StarWeightRet = parseFloat(document.getElementById("StarWeightRet" + i).value);
        const FinalWeightRet = parseFloat(document.getElementById("FinalWeightRet" + i).value);

        // Only perform calculations if StarWeightRet is a valid number
        if (!isNaN(StarWeightRet) && StarWeightRet > 0) {
            totalStarWeightRet += StarWeightRet;
            totalFinalWeightRet += FinalWeightRet;

            // Calculate PercentagePassing
            const PercentagePassing = ((StarWeightRet - FinalWeightRet) / StarWeightRet) * 100;

            // Retrieve the corresponding PctRetFine from the array
            const PctRetFine = PctRetFineArray[7 - i] || 0; // Adjust indexing as necessary

            // Calculate WeightedLoss
            const WeightedLoss = (PercentagePassing * PctRetFine) / 100;
            totalWeightedLoss += WeightedLoss;

            // Update the corresponding input fields with the calculated values
            document.getElementById("PercentagePassing" + i).value = PercentagePassing.toFixed(2);
            document.getElementById("WeightedLoss" + i).value = WeightedLoss.toFixed(2);
        } else {
            // Set fields to empty if StarWeightRet is invalid
            document.getElementById("PercentagePassing" + i).value = "";
            document.getElementById("WeightedLoss" + i).value = "";
        }
    }

    // Display total Star Weight Retained and Final Weight Retained
    document.getElementById("TotalStarWeightRet").value = totalStarWeightRet.toFixed(2);
    document.getElementById("TotalFinalWeightRet").value = totalFinalWeightRet.toFixed(2);
    document.getElementById("TotalWeightedLoss").value = totalWeightedLoss.toFixed(2);


    // Test Result Soundness Coarse Aggregate
    let totalStarWeightRetCoarse = 0;
    let totalFinalWeightRetCoarse = 0;

    // Arrays to store weights
    const StarWeightRetCoarse = [];
    const FinalWeightRetCoarse = [];

    // Fill arrays with input values
    for (let i = 1; i <= 10; i++) {
        const weight = parseFloat(document.getElementById("StarWeightRetCoarse" + i).value);
        if (!isNaN(weight) && weight > 0) {
            StarWeightRetCoarse[i - 1] = weight;
            totalStarWeightRetCoarse += weight;
        }
    }

    for (let i = 1; i <= 6; i++) {
        const weight = parseFloat(document.getElementById("FinalWeightRetCoarse" + i).value);
        if (!isNaN(weight) && weight > 0) {
            FinalWeightRetCoarse[i - 1] = weight;
            totalFinalWeightRetCoarse += weight;
        }
    }

    // Calculate Percentage Passing values
    const calculatePercentagePassing = (initialWeights, finalWeight) => {
        const totalInitialWeight = initialWeights.reduce((acc, weight) => acc + weight, 0);
        return totalInitialWeight > 0 ? ((totalInitialWeight - finalWeight) / totalInitialWeight) * 100 : "";
    }

    // Calculate percentage passing for coarse aggregates
    const PercentagePassingCoarse1 = (StarWeightRetCoarse[0] && FinalWeightRetCoarse[0]) ?
        calculatePercentagePassing([StarWeightRetCoarse[0]], FinalWeightRetCoarse[0]) : "";

    const PercentagePassingCoarse2 = (StarWeightRetCoarse.slice(1, 3) && FinalWeightRetCoarse[1]) ?
        calculatePercentagePassing(StarWeightRetCoarse.slice(1, 3), FinalWeightRetCoarse[1]) : "";

    const PercentagePassingCoarse3 = (StarWeightRetCoarse.slice(3, 5) && FinalWeightRetCoarse[2]) ?
        calculatePercentagePassing(StarWeightRetCoarse.slice(3, 5), FinalWeightRetCoarse[2]) : "";

    const PercentagePassingCoarse4 = (StarWeightRetCoarse.slice(5, 7) && FinalWeightRetCoarse[3]) ?
        calculatePercentagePassing(StarWeightRetCoarse.slice(5, 7), FinalWeightRetCoarse[3]) : "";

    const PercentagePassingCoarse5 = (StarWeightRetCoarse.slice(7, 9) && FinalWeightRetCoarse[4]) ?
        calculatePercentagePassing(StarWeightRetCoarse.slice(7, 9), FinalWeightRetCoarse[4]) : "";

    // Adjust calculation for PercentagePassingCoarse6
    const PercentagePassingCoarse6 = (StarWeightRetCoarse[9] !== undefined && FinalWeightRetCoarse[5] !== undefined && totalStarWeightRetCoarse > 0) ?
        ((StarWeightRetCoarse[9] - FinalWeightRetCoarse[5]) / totalStarWeightRetCoarse) :
        "";

    // Display results
    for (let i = 1; i <= 6; i++) {
        const percentageValue = eval("PercentagePassingCoarse" + i);
        document.getElementById("PercentagePassingCoarse" + i).value = typeof percentageValue === "number" ? percentageValue.toFixed(2) : "";
    }

    document.getElementById("TotalWeightRetCoarse").value = totalStarWeightRetCoarse.toFixed(2);
    document.getElementById("TotalFinalWeightRetCoarse").value = totalFinalWeightRetCoarse.toFixed(2);



    // Arrays to store PctRetCoarse values
    const PctRetCoarse = [];

    // Fill PctRetCoarse array with values
    for (let i = 1; i <= 11; i++) {
        const pctRet = parseFloat(document.getElementById("PctRetCoarse" + i).value);
        if (!isNaN(pctRet)) {
            PctRetCoarse[i - 1] = pctRet;
        }
    }

    // Arrays to store weighted losses
    const WeightedLossCoarse = [];
    let totalWeightedLossCoarse = 0;

    // Calculate Weighted Loss for each coarse aggregate
    WeightedLossCoarse[0] = (typeof PercentagePassingCoarse1 === "number" && !isNaN(PercentagePassingCoarse1) && PctRetCoarse[10] !== undefined) ?
        (PercentagePassingCoarse1 * PctRetCoarse[10]) / 100 : "";

    WeightedLossCoarse[1] = (typeof PercentagePassingCoarse2 === "number" && !isNaN(PercentagePassingCoarse2) && PctRetCoarse[8] !== undefined && PctRetCoarse[9] !== undefined) ?
        (PercentagePassingCoarse2 * (PctRetCoarse[8] + PctRetCoarse[9])) / 100 : "";

    WeightedLossCoarse[2] = (typeof PercentagePassingCoarse3 === "number" && !isNaN(PercentagePassingCoarse3) && PctRetCoarse[6] !== undefined && PctRetCoarse[7] !== undefined) ?
        (PercentagePassingCoarse3 * (PctRetCoarse[6] + PctRetCoarse[7])) / 100 : "";

    WeightedLossCoarse[3] = (typeof PercentagePassingCoarse4 === "number" && !isNaN(PercentagePassingCoarse4) && PctRetCoarse[4] !== undefined && PctRetCoarse[5] !== undefined) ?
        (PercentagePassingCoarse4 * (PctRetCoarse[4] + PctRetCoarse[5])) / 100 : "";

    WeightedLossCoarse[4] = (typeof PercentagePassingCoarse5 === "number" && !isNaN(PercentagePassingCoarse5) && PctRetCoarse[2] !== undefined && PctRetCoarse[3] !== undefined) ?
        (PercentagePassingCoarse5 * (PctRetCoarse[2] + PctRetCoarse[3])) / 100 : "";

    WeightedLossCoarse[5] = (typeof PercentagePassingCoarse6 === "number" && !isNaN(PercentagePassingCoarse6) && PctRetCoarse[1] !== undefined) ?
        (PercentagePassingCoarse6 * PctRetCoarse[1]) / 100 : "";

    // Display the weighted loss results
    for (let i = 0; i < WeightedLossCoarse.length; i++) {
        if (WeightedLossCoarse[i] !== "") {
            totalWeightedLossCoarse += WeightedLossCoarse[i];
            document.getElementById("WeightedLossCoarse" + (i + 1)).value = WeightedLossCoarse[i].toFixed(2);
        } else {
            document.getElementById("WeightedLossCoarse" + (i + 1)).value = "";
        }
    }

    // Display the total weighted loss
    document.getElementById("TotalWeightedLossCoarse").value = totalWeightedLossCoarse.toFixed(2);




    // Test Information For the Solution Used and Sodium Sulfate

    // Obtain the start date from the first input
    const startDateInput = document.getElementById("StartDate1").value;
    const startDate = new Date(startDateInput);

    // Validate startDate
    if (isNaN(startDate.getTime())) {
        return;
    }

    // Loop through each input field to set dates
    for (let i = 1; i <= 5; i++) {
        // Increment the date by one day
        const newDate = new Date(startDate);
        newDate.setDate(startDate.getDate() + (i - 1));

        // Format the date as yyyy-mm-dd
        const formattedDate = newDate.toISOString().split('T')[0];

        // Set the value of the input
        document.getElementById("StartDate" + i).value = formattedDate;
    }
}