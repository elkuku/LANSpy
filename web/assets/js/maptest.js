function drawHostsChart(id, labels, dataSets) {
    new Chart(document.getElementById('hostschart-' + id), {
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
