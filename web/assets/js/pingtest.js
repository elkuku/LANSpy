function drawChart(i, labels, data) {
    new Chart(document.getElementById("chart-" + i), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: i,
                data: data
            }]
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
