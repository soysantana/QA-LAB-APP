export function UpdateGraph() {
  const chart = echarts.init(document.querySelector("#GrainSizeRockGraph"));

  chart.setOption({
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
      max: 100,
      interval: 10
    },
    series: [
      {
        name: 'Granulometría',
        type: 'line',
        color: 'orange',
        showSymbol: false,
        lineStyle: {
          width: 2.5
        },
        z: 10,
        data: [
          [1000, document.getElementById("Pass1").value],
          [750, document.getElementById("Pass2").value],
          [500, document.getElementById("Pass3").value],
          [325, document.getElementById("Pass4").value],
          [300, document.getElementById("Pass5").value],
          [250, document.getElementById("Pass6").value],
          [200, document.getElementById("Pass7").value],
          [150, document.getElementById("Pass8").value],
          [100, document.getElementById("Pass9").value],
          [75, document.getElementById("Pass10").value],
          [50, document.getElementById("Pass11").value],
          [37.5, document.getElementById("Pass12").value],
          [25, document.getElementById("Pass13").value],
          [19, document.getElementById("Pass14").value],
          [12.5, document.getElementById("Pass15").value],
          [9.5, document.getElementById("Pass16").value],
          [4.75, document.getElementById("Pass17").value],
          [0.85, document.getElementById("Pass18").value],
          [0.075, document.getElementById("Pass19").value],
        ]
      },
      {
        data: [
          [getSpecsValues(document.getElementById("Specs1").value).mm, getSpecsValues(document.getElementById("Specs1").value).left],
          [getSpecsValues(document.getElementById("Specs2").value).mm, getSpecsValues(document.getElementById("Specs2").value).left],
          [getSpecsValues(document.getElementById("Specs3").value).mm, getSpecsValues(document.getElementById("Specs3").value).left],
          [getSpecsValues(document.getElementById("Specs4").value).mm, getSpecsValues(document.getElementById("Specs4").value).left],
          [getSpecsValues(document.getElementById("Specs5").value).mm, getSpecsValues(document.getElementById("Specs5").value).left],
          [getSpecsValues(document.getElementById("Specs6").value).mm, getSpecsValues(document.getElementById("Specs6").value).left],
          [getSpecsValues(document.getElementById("Specs7").value).mm, getSpecsValues(document.getElementById("Specs7").value).left],
          [getSpecsValues(document.getElementById("Specs8").value).mm, getSpecsValues(document.getElementById("Specs8").value).left]
        ],
        type: 'line',
        color: 'gray',
        showSymbol: false,
        lineStyle: {
          width: 2.5
        },
      },
      {
        data: [
          [getSpecsValues(document.getElementById("Specs1").value).mm, getSpecsValues(document.getElementById("Specs1").value).right],
          [getSpecsValues(document.getElementById("Specs2").value).mm, getSpecsValues(document.getElementById("Specs2").value).right],
          [getSpecsValues(document.getElementById("Specs3").value).mm, getSpecsValues(document.getElementById("Specs3").value).right],
          [getSpecsValues(document.getElementById("Specs4").value).mm, getSpecsValues(document.getElementById("Specs4").value).right],
          [getSpecsValues(document.getElementById("Specs5").value).mm, getSpecsValues(document.getElementById("Specs5").value).right],
          [getSpecsValues(document.getElementById("Specs6").value).mm, getSpecsValues(document.getElementById("Specs6").value).right],
          [getSpecsValues(document.getElementById("Specs7").value).mm, getSpecsValues(document.getElementById("Specs7").value).right],
          [getSpecsValues(document.getElementById("Specs8").value).mm, getSpecsValues(document.getElementById("Specs8").value).right]
        ],
        type: 'line',
        color: 'gray',
        showSymbol: false,
        lineStyle: {
          width: 3
        },
      },
      {
        data: [
          [300, 0],
          [300, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true
      },
      {
        data: [
          [75, 0],
          [75, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true
      },
      {
        data: [
          [4.75, 0],
          [4.75, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true
      },
      {
        data: [
          [1.18, 0],
          [1.18, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true,
        lineStyle: {
          type: 'dashed'
        }
      },
      {
        data: [
          [0.25, 0],
          [0.25, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true,
        lineStyle: {
          type: 'dashed'
        }
      },
      {
        data: [
          [0.075, 0],
          [0.075, 100]
        ],
        type: 'line',
        color: 'black',
        showSymbol: false,
        smooth: true,
        lineStyle: {
          type: 'dashed'
        }
      },
      {
        data: [
          [0.002, 0],
          [0.002, 100]
        ],
        type: 'line',
        color: 'green',
        showSymbol: false,
        smooth: true
      }
    ],
    graphic: [
      { type: 'text', left: '17%', top: '75%', style: { text: 'Cobbles', fill: 'black', fontSize: 10 } },
      { type: 'text', left: '29%', top: '57%', style: { text: 'Gravel', fill: 'black', fontSize: 10 } },
      { type: 'text', left: '37%', top: '60%', style: { text: 'Coarse', fill: 'black', fontSize: 10 } },
      { type: 'text', left: '41%', top: '55%', style: { text: 'Sand', fill: 'black', fontSize: 10 } },
      { type: 'text', left: '44%', top: '60%', style: { text: 'Medium', fill: 'black', fontSize: 10 } },
      { type: 'text', left: '51%', top: '60%', style: { text: 'Fine', fill: 'black', fontSize: 10 } }
    ]
  });
};

UpdateGraph()