// gs-chart.js
import { getSpecsDataFromTable, getRawDataFromTable } from './gs-utils.js';
import { referenceLines, referenceLabels } from './gs-reference.js';

export function UpdateGraph() {
    const rawData = getRawDataFromTable("#grainTable");

    const filteredData = rawData
        .filter(item => item[1] !== '' && item[1] !== null && !isNaN(item[1]))
        .map(item => [item[0], parseFloat(item[1])]);

    const chart = echarts.init(document.querySelector("#GrainSizeChart"));
    chart.setOption({
        xAxis: {
            name: 'Particle Diameter (mm)',
            nameLocation: 'center',
            nameTextStyle: { color: 'black', lineHeight: 40 },
            type: 'log',
            inverse: true,
            min: 0.001,
            max: 1000,
            splitLine: { show: true }
        },
        yAxis: {
            name: 'Percent Passing (%)',
            nameLocation: 'center',
            nameTextStyle: { color: 'black', lineHeight: 33 },
            min: 0,
            max: 100,
            interval: 10
        },
        series: [
            {
                data: filteredData,
                type: filteredData.length === 1 ? 'scatter' : 'line',
                color: 'orange',
                showSymbol: false,
                z: 10,
            },
            // Línea izquierda
            {
                data: getSpecsDataFromTable("#grainTable", "left"),
                type: 'line',
                color: 'black',
                showSymbol: false
            },
            // Línea derecha
            {
                data: getSpecsDataFromTable("#grainTable", "right"),
                type: 'line',
                color: 'black',
                showSymbol: false
            },
            ...referenceLines
        ],
        graphic: referenceLabels
    });
};

UpdateGraph()
