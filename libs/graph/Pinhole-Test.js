echarts.init(document.querySelector("#Pinhole")).setOption({
  title: {
    subtext: 'DISPERSIVE GRADE VS FLOW RATE',
    left: 'center'
  },
  xAxis: {
    name: 'TEST TIME (MINUTE)',
    nameLocation: 'center',
    min: 0,
    max: 20,
    minInterval: 20,
    nameTextStyle: {
      color: 'black',
      lineHeight: 40
    },
  },
  yAxis: {
    name: 'FLOW RATE (mL/s)',
    nameLocation: 'center',
    minInterval: 5,
    nameTextStyle: {
      color: 'black',
      lineHeight: 33
    },
  },
  series: [
    {
      data: [
        [0.17, 0.96],
        [0.37, 0.86],
        [0.56, 0.86],
        [1.05, 0.85],
        [1.53, 0.87],
        [5.01, 0.86],
        [6.01, 0.90],
        [7.01, 0.83],
        [8.01, 0.88],
        [9.01, 0.88],
        [10.01, 0.87],
        [11.01, 1.08],
        [12.01, 1.03],
        [13.01, 1.03],
        [14.01, 1.03],
        [15.01, 1.05],
        [16.01, 1.33],
        [17.01, 1.35],
        [18.01, 1.33],
        [19.01, 1.35],
        [20.01, 1.35]
      ],
      type: 'line'
    }
  ],
  graphic: [
    {
      type: 'group',
      children: [
        {
          type: 'image',
          style: {
            image: "/app/assets/img/pinhole.png",
            x: 61,
            y: 54,
            width: 715,
            height: 320
          }
        }
      ]
    }
  ]
});