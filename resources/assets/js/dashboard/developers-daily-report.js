let $datePicker = $('#developers-report-date-picker');
let start = moment();

$(window).on("load", function () {
    loadDevelopersWorkReport(start.format('YYYY-MM-D  H:mm:ss'));
});

$datePicker.on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate.format('YYYY-MM-D  H:mm:ss');
    loadDevelopersWorkReport(startDate);
});

window.cb = function (start) {
    $datePicker.find('span').html(start.format('MMMM D, YYYY'));
};

cb(start);

$datePicker.daterangepicker({
    startDate: start,
    opens: 'left',
    maxDate: moment(),
    autoUpdateInput: false,
    singleDatePicker: true,
}, cb);

window.loadDevelopersWorkReport = function (startDate) {
    $.ajax({
        type: 'GET',
        url: userDeveloperReportUrl,
        dataType: 'json',
        data: {
            start_date: startDate,
        },
        cache: false
    }).done(prepareDeveloperWorkReport);
};

window.prepareDeveloperWorkReport = function (result) {
    $('#developers-daily-work-report-container').html('');
    let data = result.data;
    if (data.totalRecords === 0) {
        $('#developers-daily-work-report-container').empty();
        $('#developers-daily-work-report-container').append('<div align="center" class="no-record">No Records Found</div>');
        return true
    } else {
        $('#developers-daily-work-report-container').html('');
        $('#developers-daily-work-report-container').append('<canvas id="developers-daily-work-report"></canvas>');
    }
    let ctx = document.getElementById('developers-daily-work-report').getContext('2d');
    ctx.canvas.style.height = '500px';
    ctx.canvas.style.width = '100%';
    let dailyWorkReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.data.labels,
            datasets: [{
                label: data.label,
                data: data.data.data,
                backgroundColor: data.data.backgroundColor,
                borderColor: data.data.borderColor,
                borderWidth: 1
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        result = convertToTimeFormat(tooltipItem.yLabel);
                        return label + result;
                    }
                }
            },
            scales: {
                yAxes: [{
                    stacked: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Hours'
                    },
                    ticks: {
                        beginAtZero: true,
                        callback: function (label, index, labels) {
                           return getTotalHours(label);
                        }
                    }
                }]
            }
        }
    });
};
window.convertToTimeFormat = function (totalMinutes) {
    let hours = Math.floor(totalMinutes / 60);
    let minutes = Math.floor(totalMinutes % 60);
    return hours + 'h' + " " + minutes + 'm';
};
window.getTotalHours = function (totalMinutes) {
    let hours = Math.floor(totalMinutes / 60);
    return hours;
};
