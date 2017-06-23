function drawChart(id, labels, dataSets) {
    new Chart(document.getElementById('chart-' + id), {
        type: 'line',
        data: {
            labels: labels,
            datasets: dataSets
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
