/**
 * * Graph GrainSizeRockGraph
 * */

echarts.init(document.querySelector("#GrainSizeRockGraph")).setOption({
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
      lineStyle: {
        width: 3
      },
      showSymbol: false,
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
    }
  ]
});