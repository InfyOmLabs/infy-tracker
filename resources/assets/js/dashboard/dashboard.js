$('#userId').select2({
    width: '100%',
    placeholder:'Select User'
});
let timeRange = $('#time_range');
const today = moment();
let start = today.clone().startOf('week');
let end = today.clone().endOf('week');
let userId = $('#userId').val();
let isPickerApply = false;
$(window).on("load", function () {
    loadUserWorkReport(start.format('YYYY-MM-D  H:mm:ss'), end.format('YYYY-MM-D  H:mm:ss'),userId);
});

timeRange.on('apply.daterangepicker', function (ev, picker) {
    isPickerApply = true;
    start = picker.startDate.format('YYYY-MM-D  H:mm:ss');
    end = picker.endDate.format('YYYY-MM-D  H:mm:ss');
    loadUserWorkReport(start, end, userId);
});

window.cb = function (start, end) {
    timeRange.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
};

cb(start, end);

const lastMonth = moment().startOf('month').subtract(1, 'days');

timeRange.daterangepicker({
    startDate: start,
    endDate: end,
    opens: 'left',
    showDropdowns: true,
    autoUpdateInput: false,
    ranges: {
        'Today': [moment(), moment()],
        'This Week': [start, end],
        'Next Week': [moment().endOf('week').add(1, 'days'), moment().endOf('week').add(7, 'days')],
        'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().startOf('week').subtract(1, 'days')],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [lastMonth.clone().startOf('month'), lastMonth.clone().endOf('month')]
    }
}, cb);

$("#userId").on('change', function (e) {
    e.preventDefault();
    userId = $('#userId').val();
    let startDate = (isPickerApply) ? start : start.format('YYYY-MM-D  H:mm:ss');
    let endDate = (isPickerApply) ? end : end.format('YYYY-MM-D  H:mm:ss');
    loadUserWorkReport(startDate, endDate, userId);
});

window.loadUserWorkReport = function (startDate, endDate, userId) {
    $.ajax({
        type: 'GET',
        url: userReportUrl,
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId
        },
        cache: false
    }).done(prepareUserWorkReport);
};

window.prepareUserWorkReport = function (result) {
    $('#daily-work-report').html('');
    let data = result.data;
    if (data.totalRecords === 0) {
        $('#work-report-container').html('');
        $('#work-report-container').append('<div align="center" class="no-record">No Records Found</div>');
        return true
    } else {
        $('#work-report-container').html('');
        $('#work-report-container').append('<canvas id="daily-work-report"></canvas>');
    }

    let barChartData = {
        labels: data.date,
        datasets: data.data

    };
    let ctx = document.getElementById('daily-work-report').getContext('2d');
    ctx.canvas.style.height = '400px';
    ctx.canvas.style.width = '100%';
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            title: {
                display: false,
                text: data.label
            },
            tooltips: {
                mode: 'index',
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += Math.round(tooltipItem.yLabel * 100) / 100;
                        return label + ' hr';
                    }
                }
            },
            responsive: false,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    stacked: true,
                }],
                yAxes: [{
                    stacked: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Hours'
                    }
                }]
            }
        }
    });
};
