var Density = [3000, 2900, 2800, 2700, 2600, 2500, 2300, 2200, 2100, 2000, 1900, 1800, 1700, 1600, 1500, 1400, 1300, 1200, 1100, 1000];
var SP2x6 = 2.6; var LVoid2x6 = [];
var SP2x7 = 2.7; var LVoid2x7 = [];
var SP2x8 = 2.8; var LVoid2x8 = [];
var SP2xx = parseFloat(document.getElementById("SpecGravity").value); var LVoid2xx = [];

for (var i = 0; i < Density.length; i++) {
  LVoid2x6[i] = (1000 * SP2x6 - Density[i]) / (Density[i] * SP2x6);
  LVoid2x7[i] = (1000 * SP2x7 - Density[i]) / (Density[i] * SP2x7);
  LVoid2x8[i] = (1000 * SP2x8 - Density[i]) / (Density[i] * SP2x8);
  var value = (1000 * SP2xx - Density[i]) / (Density[i] * SP2xx);
  if (!isNaN(value)) {
    LVoid2xx[i] = value;
  }
}

var seriesData = Array.from({ length: 6 }, (_, index) => [
  parseFloat(document.getElementById(`MoisturePorce${index + 1}`).value),
  parseFloat(document.getElementById(`DryDensity${index + 1}`).value)
]).filter(data => !isNaN(data[0]) && !isNaN(data[1]));

var CorrectedData = Array.from({ length: 6 }, (_, index) => [
  parseFloat(document.getElementById(`MCcorrected${index + 1}`).value),
  parseFloat(document.getElementById(`DensyCorrected${index + 1}`).value)
]).filter(data => !isNaN(data[0]) && !isNaN(data[1]));

var maxMoisturePorce = Math.ceil(Math.max(...seriesData.map(data => data[0])));
var minMoisturePorce = Math.floor(Math.min(...seriesData.map(data => data[0])));

var maxDryDensity = Math.ceil(Math.max(...seriesData.map(data => data[1])));
var minDryDensity = Math.floor(Math.min(...seriesData.map(data => data[1])));

echarts.init(document.querySelector("#StandardProctor")).setOption({
  xAxis: {
    name: 'Moisture Content',
    nameLocation: 'center',
    nameTextStyle: {
      color: 'black',
      lineHeight: 50
    },
    type: 'value',
    min: minMoisturePorce - 5,
    max: maxMoisturePorce + 5,
    axisLine: { onZero: false }
  },
  yAxis: {
    name: 'Dry Density, kg/m3',
    nameLocation: 'center',
    nameTextStyle: {
      color: 'black',
      lineHeight: 80
    },
    type: 'value',
    min: minDryDensity - 100,
    max: maxDryDensity + 150,
    minInterval: 100,
    axisLine: { onZero: false }
  },
  series: [
    {
      data: LVoid2x6.map(function (value, index) {
        return [value * 100, Density[index]];
      }),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2x7.map(function (value, index) {
        return [value * 100, Density[index]];
      }),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2x8.map(function (value, index) {
        return [value * 100, Density[index]];
      }),
      type: 'line',
      color: 'black',
      showSymbol: false,
      smooth: true
    },
    {
      data: LVoid2xx.map(function (value, index) {
        return [value * 100, Density[index]];
      }),
      type: 'line',
      color: 'blue',
      showSymbol: false,
      smooth: true
    },
    {
      data: seriesData,
      type: 'line',
      color: 'orange',
      symbolSize: 10,
      smooth: true
    },
    {
      data: CorrectedData,
      type: 'line',
      color: 'green',
      symbolSize: 10,
      smooth: true
    }
  ]
});
