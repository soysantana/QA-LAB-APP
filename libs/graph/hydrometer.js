/**
 * * Graph Hydrometer
 * */

echarts.init(document.querySelector("#HydrometerGraph")).setOption({
    xAxis: {
    type: 'log',
    inverse: true,
    min: 0.001,
    max: 1000,
    splitLine: {
      show: true
    }
  },
  yAxis: {
    type: 'value',
    min: 0,
    max: 100
  },
  series: [
    {
      name: 'Granulometría',
      type: 'line',
      color: 'orange',
      smooth: true,
      symbol: 'circle',
      data: [
        [75, document.getElementById("Pass1").value],
        [63, document.getElementById("Pass2").value],
        [50.8, document.getElementById("Pass3").value],
        [37.5, document.getElementById("Pass4").value],
        [25.0, document.getElementById("Pass5").value],
        [19.0, document.getElementById("Pass6").value],
        [12.50, document.getElementById("Pass7").value],
        [9.5, document.getElementById("Pass8").value],
        [4.75, document.getElementById("Pass9").value],
        [2.00, document.getElementById("Pass10").value],
        [1.18, document.getElementById("Pass11").value],
        [0.85, document.getElementById("Pass12").value],
        [0.3, document.getElementById("Pass13").value],
        [0.25, document.getElementById("Pass14").value],
        [0.15, document.getElementById("Pass15").value],
        [0.106, document.getElementById("Pass16").value],
        [0.075, document.getElementById("Pass17").value],
        [document.getElementById("DMm1").value, document.getElementById("PassingPerceTotalSample1").value],
        [document.getElementById("DMm2").value, document.getElementById("PassingPerceTotalSample2").value],
        [document.getElementById("DMm3").value, document.getElementById("PassingPerceTotalSample3").value],
        [document.getElementById("DMm4").value, document.getElementById("PassingPerceTotalSample4").value],
        [document.getElementById("DMm5").value, document.getElementById("PassingPerceTotalSample5").value],
        [document.getElementById("DMm6").value, document.getElementById("PassingPerceTotalSample6").value],
        [document.getElementById("DMm7").value, document.getElementById("PassingPerceTotalSample7").value],
        [document.getElementById("DMm8").value, document.getElementById("PassingPerceTotalSample8").value],
        [document.getElementById("DMm9").value, document.getElementById("PassingPerceTotalSample9").value]
      ]
    }
  ]
});