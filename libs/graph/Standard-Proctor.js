var Density = [3000, 2900, 2800, 2700, 2600, 2500, 2300, 2200, 2100, 2000, 1900, 1800, 1700, 1600, 1500, 1400, 1300, 1200, 1100, 1000];
var SP2x6 = 2.6, SP2x7 = 2.7, SP2x8 = 2.8, LVoid2x6 = [], LVoid2x7 = [], LVoid2x8 = [], LVoid2xx = [];

// Obtener el valor de SP2xx solo si el elemento existe
var SP2xxElement = document.getElementById("SpecGravity");
var SP2xx = SP2xxElement ? parseFloat(SP2xxElement.value) : 0; 

// Calcular LVoid2x6, LVoid2x7, LVoid2x8 y LVoid2xx
for (var i = 0; i < Density.length; i++) {
  LVoid2x6[i] = (1000 * SP2x6 - Density[i]) / (Density[i] * SP2x6);
  LVoid2x7[i] = (1000 * SP2x7 - Density[i]) / (Density[i] * SP2x7);
  LVoid2x8[i] = (1000 * SP2x8 - Density[i]) / (Density[i] * SP2x8);

  var value = (1000 * SP2xx - Density[i]) / (Density[i] * SP2xx);
  if (!isNaN(value)) {
    LVoid2xx[i] = value;
  }
}

// Obtener seriesData y CorrectedData
var seriesData = Array.from({ length: 6 }, (_, index) => {
  var moistureElement = document.getElementById(`MoisturePorce${index + 1}`);
  var dryDensityElement = document.getElementById(`DryDensity${index + 1}`);
  
  return moistureElement && dryDensityElement ? [
    parseFloat(moistureElement.value),
    parseFloat(dryDensityElement.value)
  ] : [NaN, NaN];
}).filter(data => !isNaN(data[0]) && !isNaN(data[1]));

var CorrectedData = Array.from({ length: 6 }, (_, index) => {
  var mcCorrectedElement = document.getElementById(`MCcorrected${index + 1}`);
  var densyCorrectedElement = document.getElementById(`DensyCorrected${index + 1}`);
  
  return mcCorrectedElement && densyCorrectedElement ? [
    parseFloat(mcCorrectedElement.value),
    parseFloat(densyCorrectedElement.value)
  ] : [NaN, NaN];
}).filter(data => !isNaN(data[0]) && !isNaN(data[1]));

// Calcular máximos y mínimos
var maxMoisturePorce = Math.ceil(Math.max(...seriesData.map(data => data[0]), ...CorrectedData.map(data => data[0])));
var minMoisturePorce = Math.floor(Math.min(...seriesData.map(data => data[0]), ...CorrectedData.map(data => data[0])));
var maxDryDensity = Math.ceil(Math.max(...seriesData.map(data => data[1]), ...CorrectedData.map(data => data[1])));
var minDryDensity = Math.floor(Math.min(...seriesData.map(data => data[1]), ...CorrectedData.map(data => data[1])));

// Configurar el gráfico con ECharts
echarts.init(document.querySelector("#StandardProctor")).setOption({
  xAxis: {
    name: 'Moisture Content',
    nameLocation: 'center',
    nameTextStyle: { color: 'black', lineHeight: 50 },
    type: 'value',
    min: minMoisturePorce - 5,
    max: maxMoisturePorce + 5,
    axisLine: { onZero: false }
  },
  yAxis: {
    name: 'Dry Density, kg/m3',
    nameLocation: 'center',
    nameTextStyle: { color: 'black', lineHeight: 80 },
    type: 'value',
    min: minDryDensity - 100,
    max: maxDryDensity + 150,
    minInterval: 100,
    axisLine: { onZero: false }
  },
  series: [
    {
      data: LVoid2x6.map((value, index) => [value * 100, Density[index]]),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2x7.map((value, index) => [value * 100, Density[index]]),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2x8.map((value, index) => [value * 100, Density[index]]),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2xx.map((value, index) => [value * 100, Density[index]]),
      type: 'line',
      color: 'blue',
      showSymbol: false,
      smooth: true
    },
    {
      data: seriesData,
      type: 'line',
      color: 'orange', // Color de la línea
      symbol: 'diamond',
      symbolSize: 9,
      smooth: true,
      itemStyle: {
        color: 'white',
        borderColor: 'orange',
        borderWidth: 1
      },
      lineStyle: {
        color: 'orange',
        width: 2
      }
    },   
    {
      data: CorrectedData,
      type: 'line',
      symbol: 'diamond',
      symbolSize: 8,
      smooth: true,
      itemStyle: {
        color: 'white',
        borderColor: 'green',
        borderWidth: 1
      },
      lineStyle: {
        color: 'green',
        width: 2
      }
    }
  ]
});
