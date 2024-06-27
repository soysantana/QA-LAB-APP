var Blows1 = parseFloat(document.getElementById("Blows1").value);
var Blows2 = parseFloat(document.getElementById("Blows2").value); 
var Blows3 = parseFloat(document.getElementById("Blows3").value);
var LLMCPorce1 = parseFloat(document.getElementById("LLMCPorce1").textContent);
var LLMCPorce2 = parseFloat(document.getElementById("LLMCPorce2").textContent);
var LLMCPorce3 = parseFloat(document.getElementById("LLMCPorce3").textContent);

var maxLLMCPorce3 = Math.ceil(LLMCPorce3);
var minLLMCPorce1 = Math.floor(LLMCPorce1);

// Ajustar el valor para que sea par
if (minLLMCPorce1 % 2 !== 0) {
  minLLMCPorce1--;
}
 
 echarts.registerTransform(ecStat.transform.regression);

  echarts.init(document.querySelector("#liquid-limit")).setOption({
    dataset: [
      {
        source: [
          [Blows1, LLMCPorce1],
          [Blows2, LLMCPorce2],
          [Blows3, LLMCPorce3],
        ],
      },
      {
        transform: {
          type: "ecStat:regression",
          config: {
            method: "logarithmic",
          },
        },
      },
    ],
    title: {
        subtext: 'MULTI-POINT LIQUID LIMIT PLOT',
        left: 'center'
    },
    xAxis: {
        type: 'log',
        name: 'Number of Blows',
        nameLocation: 'center'
    },
    yAxis: {
        name: 'Moisture Content (%)',
        nameLocation: 'center',
        nameTextStyle: {
            color: 'black',
            lineHeight: 33
          },
        max: maxLLMCPorce3,
        min: minLLMCPorce1  
    },
    series: [
      {
        name: "scatter",
        type: "scatter",
        symbol: 'diamond',
        datasetIndex: 0,
      },
      {
        name: "line",
        type: "line",
        color: 'orange',
        smooth: true,
        datasetIndex: 1,
        symbolSize: 0.1,
        symbol: "circle",
        label: { show: true, fontSize: 16 },
        labelLayout: { dx: -20 },
        encode: { label: 2, tooltip: 1 },
      },
    ],
  });