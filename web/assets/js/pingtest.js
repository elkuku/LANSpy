function drawChart(id, labels, data1, data2) {
    new Chart(document.getElementById('chart-' + id), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'google.com',
                    data: data1
                },
                {
                    label: 'github.com',
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
