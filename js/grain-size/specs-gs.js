export const specs = {
    CF: {
        I: { Specs7: "100", Specs8: "87-100", Specs9: "80-100", Specs11: "50-100", Specs12: "15-60", Specs13: "2-15", Specs15: "0-7", Specs18: "0-2" },
        C: { Specs7: "100", Specs8: "87-100", Specs9: "70-100", Specs11: "33-100", Specs12: "7-60", Specs13: "0-15", Specs15: "0-7", Specs18: "0-5" },
        N: { Specs7: "100", Specs8: "87-100", Specs9: "80-100", Specs11: "40-100", Specs12: "7-60", Specs13: "0-15", Specs15: "0-7", Specs18: "0-1.7" },
        A: { Specs7: "100", Specs8: "87-100", Specs9: "80-100", Specs11: "50-100", Specs12: "15-60", Specs13: "2-15", Specs15: "0-7", Specs18: "0-2" },
        D: { Specs7: "100", Specs8: "87-100", Specs9: "80-100", Specs11: "40-100", Specs12: "7-60", Specs13: "0-15", Specs15: "0-7", Specs18: "0-2" },
    },
    FF: {
        I: { Specs11: "100", Specs12: "95-100", Specs13: "75-100", Specs14: "60-85", Specs16: "10-30", Specs17: "5-25", Specs18: "0-2" },
        C: { Specs11: "100", Specs12: "95-100", Specs13: "65-100", Specs14: "50-85", Specs16: "5-30", Specs17: "0-25", Specs18: "0-5" },
        N: { Specs11: "100", Specs12: "95-100", Specs13: "75-100", Specs14: "50-85", Specs16: "5-30", Specs17: "0-25", Specs18: "0-1.7" },
        A: { Specs11: "100", Specs12: "95-100", Specs13: "75-100", Specs14: "60-85", Specs16: "10-30", Specs17: "5-25", Specs18: "0-2" },
        D: { Specs11: "100", Specs12: "95-100", Specs13: "75-100", Specs14: "50-85", Specs16: "5-30", Specs17: "0-25", Specs18: "0-2" },
    },
};

export function mostrarSpecs(value) {
    const [material, tipo] = value.split("-");
    const data = specs[material][tipo];

    // limpiar todos los inputs antes de rellenar
    document.querySelectorAll("input[id^='Specs']").forEach(input => input.value = "");

    // rellenar los inputs que correspondan
    for (const key in data) {
        const input = document.getElementById(key);
        if (input) {
            input.value = data[key];
        }
    }
}

export function initSpecsSelect() {
    const select = document.getElementById("specsType");
    if (!select) return;

    select.addEventListener("change", (e) => {
        if (e.target.value) {
            mostrarSpecs(e.target.value);
        }
    });
}
