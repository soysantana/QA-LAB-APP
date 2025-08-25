function enviarImagenAlServidor(tipoReporte, chartIDs = []) {
    const urlParams = new URLSearchParams(window.location.search);
    const sampleId = urlParams.get('id');
    if (!sampleId) {
        alert("Falta el parámetro ID en la URL");
        return;
    }

    const imagenes = {};
    for (const id of chartIDs) {
        const chart = echarts.getInstanceByDom(document.getElementById(id));
        if (!chart) {
            console.warn(`No se encontró gráfico con ID: ${id}`);
            continue;
        }
        imagenes[id] = chart.getDataURL({
            pixelRatio: 1,
            backgroundColor: '#fff'
        });
    }

    fetch(`../../pdf/${tipoReporte}.php?id=${encodeURIComponent(sampleId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(imagenes)
    })
    .then(response => {
        const disposition = response.headers.get('Content-Disposition');
        let filename = "Reporte.pdf";
        if (disposition && disposition.indexOf('filename=') !== -1) {
            const matches = /filename="?([^"]+)"?/.exec(disposition);
            if (matches && matches[1]) filename = matches[1];
        }

        return response.blob().then(blob => ({ blob, filename }));
    })
    .then(({ blob, filename }) => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    })
    .catch(console.error);
}

export { enviarImagenAlServidor };