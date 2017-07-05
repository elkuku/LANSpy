function drawPingChart(id, labels, data1, data2) {
    new Chart(document.getElementById('pingchart-' + id), {
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
