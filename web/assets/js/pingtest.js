function drawChart(id, labels, data1, data2) {
    new Chart(document.getElementById("chart-" + id), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tiempo de respuesta',
                    data: data1
                },
                {
                    label: 'Tiempo de respuesta',
                    data: data2
                }
            ]
        },
        options: {
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        displayFormats: {
                            minute: 'h:mm'
                        }
                    }
                }]
            }
        }
    });
}
