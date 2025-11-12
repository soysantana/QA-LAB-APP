// dbSearch.js
export async function fetchData(table, criteria = {}, inputMap = {}) {
    /**
     * table: nombre de la tabla en la BD
     * criteria: objeto con los campos y valores para buscar { SampleName: 'X', SampleNumber: 1 }
     * inputMap: opcional, objeto que mapea columnas a inputs { Moisture_Content_Porce: 'NatMc' }
     */

    try {
        const response = await fetch('/js/db-search/fetch-data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ table, criteria })
        });

        if (!response.ok) throw new Error('Error en la consulta');

        const data = await response.json();

        const mensajeDiv = document.getElementById('mensaje-container');

        if (data && Object.keys(data).length > 0) {
            // Mostrar mensaje de éxito
            if (mensajeDiv) {
                mensajeDiv.textContent = `Lo encontre`;
                mensajeDiv.style.display = 'block';

                setTimeout(() => { mensajeDiv.style.display = 'none'; }, 3000);
            }

            // Si hay inputMap, coloca valores en los inputs correspondientes
            for (const [column, inputId] of Object.entries(inputMap)) {
                const input = document.getElementById(inputId);
                if (input && data[column] !== undefined) {
                    input.value = data[column];
                }
            }

            return data;
        } else {
            const mensajeDiv = document.getElementById('mensaje-container');
            if (mensajeDiv) {
                mensajeDiv.textContent = `No se encontraron resultados`;
                mensajeDiv.style.display = 'block';

                setTimeout(() => { mensajeDiv.style.display = 'none'; }, 3000);
            }

            console.warn(`No se encontraron resultados en la tabla "${table}"`);
            return null;
        }
    } catch (err) {
        console.error('Error fetchData:', err);
        return null;
    }
}
