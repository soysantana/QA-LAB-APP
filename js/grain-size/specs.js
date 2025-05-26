const specsPorMaterial = {
    TRF: {
      Specs1: "300_100",
      Specs2: "75_40-100",
      Specs3: "12.5_0-45",
      Specs4: "9.5_0-40",
      Specs5: "4.75_0-25",
      Specs6: "0.85_0-14",
      Specs7: "0.075_0-8",
    },
    UFF: {
      Specs1: "1000_100",
      Specs2: "75_40-100",
      Specs3: "12.5_25-65",
      Specs4: "4.75_20-50",
      Specs5: "0.85_12-43",
      Specs6: "0.075_10-35",
    },
    RF: {
      Specs1: "1000_100",
      Specs2: "300_50-100",
      Specs3: "75_0-100",
      Specs4: "12.5_0-30",
      Specs5: "9.5_0-20",
      Specs6: "4.75_0-12",
      Specs7: "0.85_0-5",
      Specs8: "0.075_0-3"
    },
    FRF: {
      Specs1: "800_100",
      Specs2: "300_50-100",
      Specs3: "75_0-100",
      Specs4: "12.5_0-45",
      Specs5: "9.5_0-40",
      Specs6: "4.75_0-30",
      Specs7: "0.85_0-20",
      Specs8: "0.075_0-13"
    },
    IRF: {
      Specs1: "1000_100",
      Specs2: "300_50-100",
      Specs3: "75_0-100",
      Specs4: "12.5_0-45",
      Specs5: "9.5_0-40",
      Specs6: "4.75_0-25",
      Specs7: "0.85_0-14",
      Specs8: "0.075_0-8"
    },
    BF: {
      Specs1: "300_100",
      Specs2: "75_40-100",
      Specs3: "12.5_25-65",
      Specs4: "0.85_12-43",
      Specs5: "0.075_10-35"
    }
  };

  document.getElementById("materialSelect").addEventListener("change", function () {
    const material = this.value;
    const specs = specsPorMaterial[material] || {};

    // Asignar los valores
    for (let i = 1; i <= 8; i++) {
      const input = document.getElementById("Specs" + i);
      if (input) input.value = specs["Specs" + i] || "";
    }
});

function getSpecsValues(specValue) {
  if (!specValue) {
    return { mm: null, left: null, right: null };
  }

  let mm = null;
  let rangePart = specValue;

  if (specValue.includes('_')) {
    const parts = specValue.split('_');
    mm = parseFloat(parts[0]);
    rangePart = parts[1];
  }

  if (rangePart.includes('-')) {
    const [left, right] = rangePart.split('-').map(v => parseFloat(v));
    return { mm, left, right };
  } else {
    const value = parseFloat(rangePart);
    return { mm, left: value, right: value };
  }
}
