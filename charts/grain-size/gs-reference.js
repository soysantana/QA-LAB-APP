// gs-reference.js

export const referenceLines = [
    { data: [[300, 0], [300, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true },
    { data: [[75, 0], [75, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true },
    { data: [[4.75, 0], [4.75, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true },
    { data: [[1.18, 0], [1.18, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true, lineStyle: { type: 'dashed' } },
    { data: [[0.25, 0], [0.25, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true, lineStyle: { type: 'dashed' } },
    { data: [[0.075, 0], [0.075, 100]], type: 'line', color: 'black', showSymbol: false, smooth: true, lineStyle: { type: 'dashed' } },
    { data: [[0.002, 0], [0.002, 100]], type: 'line', color: 'green', showSymbol: false, smooth: true }
];

export const referenceLabels = [
    { type: 'text', left: '17%', top: '76%', style: { text: 'Cobbles', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '23%', top: '65%', style: { text: 'Coarse', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '34%', top: '65%', style: { text: 'Fine', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '29%', top: '57%', style: { text: 'Gravel', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '37%', top: '60%', style: { text: 'Coarse', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '41%', top: '55%', style: { text: 'Sand', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '44%', top: '60%', style: { text: 'Medium', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '51%', top: '60%', style: { text: 'Fine', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '65%', top: '70%', style: { text: 'Silt', fill: 'black', fontSize: 13 } },
    { type: 'text', left: '76%', top: '68%', style: { text: 'Clay', fill: 'black', fontSize: 13 } }
];
