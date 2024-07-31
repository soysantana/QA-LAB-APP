/**
 * * Graph Grain Size Coarse Filter
 * */

echarts.init(document.querySelector("#GrainSizeCF")).setOption({
  xAxis: {
    name: 'Particle Diameter (mm)',
    nameLocation: 'center',
    nameTextStyle: {
      color: 'black',
      lineHeight: 40
    },
    type: 'log',
    inverse: true,
    min: 0.0001,
    max: 1000.0,
    position: 'right',
  },
  yAxis: {
    name: 'Percent Passing (%)',
    nameLocation: 'center',
    nameTextStyle: {
      color: 'black',
      lineHeight: 33
    },
    min: 0,
    max: 100
  },
  series: [
    {
      data: [
        [300, document.getElementById("Pass1").value],
        [75, document.getElementById("Pass2").value],
        [37.5, document.getElementById("Pass3").value],
        [25, document.getElementById("Pass4").value],
        [19, document.getElementById("Pass5").value],
        [9.5, document.getElementById("Pass6").value],
        [4.75, document.getElementById("Pass7").value],
        [2, document.getElementById("Pass8").value],
        [1.18, document.getElementById("Pass9").value],
        [0.85, document.getElementById("Pass10").value],
        [0.3, document.getElementById("Pass11").value],
        [0.25, document.getElementById("Pass12").value],
        [0.075, document.getElementById("Pass13").value]
      ],
      type: 'line',
      color: 'orange',
      showSymbol: false
    },
    { // line left
      data: [
        [37.5, 100],
        [25, 87],
        [19, 70],
        [9.5, 33],
        [4.75, 7],
        [2, 0],
        [0.85, 0],
        [0.075, 0]
      ],
      type: 'line',
      color: 'black',
      showSymbol: false
    },
    { // line right
      data: [
        [37.5, 100],
        [25, 100],
        [19, 100],
        [9.5, 100],
        [4.75, 60],
        [2, 15],
        [0.85, 7],
        [0.075, 5]
      ],
      type: 'line',
      color: 'black',
      showSymbol: false
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
      color: 'black',
      showSymbol: false,
      smooth: true
    }
  ],
  graphic: [
    {
      type: 'text',
      left: '17%',
      top: '75%',
      style: {
        text: 'Cobbles',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '23%',
      top: '65%',
      style: {
        text: 'Coarse',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '34%',
      top: '65%',
      style: {
        text: 'Fine',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '29%',
      top: '57%',
      style: {
        text: 'Gravel',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '37%',
      top: '60%',
      style: {
        text: 'Coarse',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '41%',
      top: '55%',
      style: {
        text: 'Sand',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '44%',
      top: '60%',
      style: {
        text: 'Medium',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '51%',
      top: '60%',
      style: {
        text: 'Fine',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '65%',
      top: '70%',
      style: {
        text: 'Silt',
        fill: 'black',
        fontSize: 13
      }
    },
    {
      type: 'text',
      left: '76%',
      top: '68%',
      style: {
        text: 'Clay',
        fill: 'black',
        fontSize: 13
      }
    }
  ]
});