    echarts.init(document.querySelector("#PlasticityChart")).setOption({
      title: {
        subtext: 'PLASTICITY CHART',
        left: 'center'
      },
      xAxis: {
        name: 'LIQUID LIMIT (LL)',
        nameLocation: 'center',
        nameTextStyle: {
          color: 'black',
          lineHeight: 40
        },
      },
      yAxis: {
        name: 'PLASTICITY INDEX (PI)',
        nameLocation: 'center',
        nameTextStyle: {
          color: 'black',
          lineHeight: 33
        },
      },
      series: [
        {
          data: [
            [0, 0],
            [60, 60]
          ],
          type: 'line',
          color: 'black',
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [25, 4],
            [100, 59]
          ],
          type: 'line',
          color: 'black',
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [7, 7],
            [29, 7]
          ],
          type: 'line',
          color: 'black',
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [4, 4],
            [25, 4]
          ],
          type: 'line',
          color: 'black',
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [50, 0],
            [50, 50]
          ],
          type: 'line',
          color: 'black',
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [16, 7],
            [75, 60]
          ],
          type: 'line',
          color: 'black',
          lineStyle: {
            type: 'dashed'
          },
          showSymbol: false,
          smooth: true
        },
        {
          data: [
            [document.getElementById("LLPorce").value, document.getElementById("PLIndexPorce").value]
          ],
          type: 'scatter',
          color: 'orange',
          symbol: 'diamond'
        }
      ],
      graphic: [
        {
          type: 'text',
          left: '19%',
          top: '75%',
          style: {
            text: 'CL - ML',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '37%',
          top: '72%',
          style: {
            text: 'ML or OL',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '38%',
          top: '57%',
          style: {
            text: 'CL or OL',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '63%',
          top: '65%',
          style: {
            text: 'MH or OH',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '56%',
          top: '38%',
          style: {
            text: 'CH or OH',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '56%',
          top: '19%',
          style: {
            text: '"U" LINE',
            fill: 'black',
            fontSize: 10
          }
        },
        {
          type: 'text',
          left: '72%',
          top: '23%',
          style: {
            text: '"A" LINE',
            fill: 'black',
            fontSize: 10
          }
        }
      ]
    });