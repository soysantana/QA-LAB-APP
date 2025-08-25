// gs-utils.js

function getSpecsDataFromTable(tableSelector, side = "left") {
    let specsData = [];
    const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

    rows.forEach((row, i) => {
        // Obtener mm desde th[1]
        const mmCell = row.querySelectorAll("th")[1];
        if (!mmCell) return;

        const mm = parseFloat(mmCell.textContent.trim());

        // Construir id del input specs con i+1 para coincidir
        const specsInputId = `Specs${i + 1}`;
        const specsInput = document.getElementById(specsInputId);

        if (specsInput && specsInput.value.trim() !== "") {
            const specValue = specsInput.value.trim();
            const specs = getSpecsValues(specValue);
            specsData.push([mm, side === "left" ? specs.left : specs.right]);
        }
    });

    return specsData;
}

function getSpecsValues(specValue) {
    if (specValue.includes('-')) {
        const parts = specValue.split('-');
        return {
            left: parseFloat(parts[0]),
            right: parseFloat(parts[1])
        };
    } else {
        const value = parseFloat(specValue);
        return {
            left: value,
            right: value
        };
    }
}

function getRawDataFromTable(tableSelector) {
    let rawData = [];
    const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

    rows.forEach(row => {
        const mmCell = row.querySelectorAll("th")[1];
        const passInput = row.querySelectorAll("td")[3]?.querySelector("input");

        if (mmCell) {
            const mm = parseFloat(mmCell.textContent.trim());

            let passValue = null;
            if (passInput) {
                const val = passInput.value.trim();
                passValue = val === "" ? null : parseFloat(val);
                if (isNaN(passValue)) passValue = null;
            }

            rawData.push([mm, passValue]);
        }
    });

    return rawData;
}

export { getSpecsDataFromTable, getRawDataFromTable };
