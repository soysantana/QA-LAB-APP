/**
 * * Graph Grain Size Fine Filter
 * */

function getSpecsValues(specValue) {
  if (specValue.includes('-')) {
      const parts = specValue.split('-');
      return {
          left: parseFloat(parts[0]),  // Valor antes del guion
          right: parseFloat(parts[1])   // Valor después del guion
      };
  } else {
      const value = parseFloat(specValue);
      return {
          left: value,   // Valor igual en ambos
          right: value
      };
  }
}

echarts.init(document.querySelector("#GrainSizeFineFilter")).setOption({
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
        [127, document.getElementById("Pass1").value],
        [101.6, document.getElementById("Pass2").value],
        [88.90, document.getElementById("Pass3").value],
        [76.20, document.getElementById("Pass4").value],
        [63.50, document.getElementById("Pass5").value],
        [50.80, document.getElementById("Pass6").value],
        [38.10, document.getElementById("Pass7").value],
        [25.00, document.getElementById("Pass8").value],
        [19.00, document.getElementById("Pass9").value],
        [12.50, document.getElementById("Pass10").value],
        [9.50, document.getElementById("Pass11").value],
        [4.75, document.getElementById("Pass12").value],
        [2.00, document.getElementById("Pass13").value],
        [1.18, document.getElementById("Pass14").value],
        [0.85, document.getElementById("Pass15").value],
        [0.30, document.getElementById("Pass16").value],
        [0.25, document.getElementById("Pass17").value],
        [0.075, document.getElementById("Pass18").value]
      ],
      type: 'line',
      color: 'orange',
      showSymbol: false
    },
    { // line left
      data: [
        [9.5, getSpecsValues(document.getElementById("Specs11").value).left],
        [4.75, getSpecsValues(document.getElementById("Specs12").value).left],
        [2, getSpecsValues(document.getElementById("Specs13").value).left],
        [1.18, getSpecsValues(document.getElementById("Specs14").value).left],
        [0.3, getSpecsValues(document.getElementById("Specs16").value).left],
        [0.25, getSpecsValues(document.getElementById("Specs17").value).left],
        [0.075, getSpecsValues(document.getElementById("Specs18").value).left]
      ],
      type: 'line',
      color: 'black',
      showSymbol: false
    },
    { // line right
      data: [
        [9.5, getSpecsValues(document.getElementById("Specs11").value).right],
        [4.75, getSpecsValues(document.getElementById("Specs12").value).right],
        [2, getSpecsValues(document.getElementById("Specs13").value).right],
        [1.18, getSpecsValues(document.getElementById("Specs14").value).right],
        [0.3, getSpecsValues(document.getElementById("Specs16").value).right],
        [0.25, getSpecsValues(document.getElementById("Specs17").value).right],
        [0.075, getSpecsValues(document.getElementById("Specs18").value).right]
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