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
        [flowData1 = document.getElementById("Seg_1").value * 0.0166667, document.getElementById("Flow_Rate_1").value],
        [flowData2 = document.getElementById("Seg_2").value * 0.0166667 + flowData1, document.getElementById("Flow_Rate_2").value],
        [flowData3 = document.getElementById("Seg_3").value * 0.0166667 + flowData2, document.getElementById("Flow_Rate_3").value],
        [flowData4 = document.getElementById("Seg_4").value * 0.0166667 + flowData3, document.getElementById("Flow_Rate_4").value],
        [flowData5 = document.getElementById("Seg_5").value * 0.0166667 + flowData4, document.getElementById("Flow_Rate_5").value],
        [flowData6 = document.getElementById("Seg_6").value * 0.0166667 + flowData5, document.getElementById("Flow_Rate_6").value],
        [flowData7 = document.getElementById("Seg_7").value * 0.0166667 + flowData6, document.getElementById("Flow_Rate_7").value],
        [flowData8 = document.getElementById("Seg_8").value * 0.0166667 + flowData7, document.getElementById("Flow_Rate_8").value],
        [flowData9 = document.getElementById("Seg_9").value * 0.0166667 + flowData8, document.getElementById("Flow_Rate_9").value],
        [flowData10 = document.getElementById("Seg_10").value * 0.0166667 + flowData9, document.getElementById("Flow_Rate_10").value],
        [flowData11 = document.getElementById("Seg_11").value * 0.0166667 + flowData10, document.getElementById("Flow_Rate_11").value],
        [flowData12 = document.getElementById("Seg_12").value * 0.0166667 + flowData11, document.getElementById("Flow_Rate_12").value],
        [flowData13 = document.getElementById("Seg_13").value * 0.0166667 + flowData12, document.getElementById("Flow_Rate_13").value],
        [flowData14 = document.getElementById("Seg_14").value * 0.0166667 + flowData13, document.getElementById("Flow_Rate_14").value],
        [flowData15 = document.getElementById("Seg_15").value * 0.0166667 + flowData14, document.getElementById("Flow_Rate_15").value],
        [flowData16 = document.getElementById("Seg_16").value * 0.0166667 + flowData15, document.getElementById("Flow_Rate_16").value],
        [flowData17 = document.getElementById("Seg_17").value * 0.0166667 + flowData16, document.getElementById("Flow_Rate_17").value],
        [flowData18 = document.getElementById("Seg_18").value * 0.0166667 + flowData17, document.getElementById("Flow_Rate_18").value],
        [flowData19 = document.getElementById("Seg_19").value * 0.0166667 + flowData18, document.getElementById("Flow_Rate_19").value],
        [flowData20 = document.getElementById("Seg_20").value * 0.0166667 + flowData19, document.getElementById("Flow_Rate_20").value],
        [flowData21 = document.getElementById("Seg_21").value * 0.0166667 + flowData20, document.getElementById("Flow_Rate_21").value],
        [flowData22 = document.getElementById("Seg_22").value * 0.0166667 + flowData21, document.getElementById("Flow_Rate_22").value],
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