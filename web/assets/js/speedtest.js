function drawChart(id, labels, data1, data2) {
    new Chart(document.getElementById('chart-' + id), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'download',
                    data: data1
                },
                {
                    label: 'upload',
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
