let timeRange = $('#time_range');
const today = moment();
let start = today.clone().startOf('week');
let end = today.clone().endOf('week');

$(window).on("load", function () {
    loadUserWorkReport(start.format('YYYY-MM-D  H:mm:ss'), end.format('YYYY-MM-D  H:mm:ss'));
});

timeRange.on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate.format('YYYY-MM-D  H:mm:ss');
    let endDate = picker.endDate.format('YYYY-MM-D  H:mm:ss');
    loadUserWorkReport(startDate, endDate);
});

window.cb = function (start, end) {
    timeRange.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
}

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

window.loadUserWorkReport = function (startDate, endDate) {
    $.ajax({
        type: 'GET',
        url: userReportUrl,
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
        },
        cache: false
    }).done(prepareUserWorkReport);
}

window.prepareUserWorkReport = function (result) {
    let data = result.data;
    if (data.totalRecords === 0) {
        $('#work-report-container').empty();
        $('#work-report-container').append('<div align="center" class="no-record">No Records Found</div>');
        return true
    }
    Highcharts.chart('work-report-container', {
        colors: ['#6574cd', '#F66081', '#9561e2', '#ff0052', '#e1c936', '#9e00ff', '#ffef00', '#3f3f3f'],
        chart: {
            type: 'column'
        },
        title: {
            text: data.label
        },
        xAxis: {
            categories: data.date
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Hours'
            },
            stackLabels: {
                enabled: false,
            }
        },
        credits: {
            enabled: false
        },
        legend: {
            align: 'right',
            x: -33,
            verticalAlign: 'top',
            padding: 3,
            y: 0,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#cc321a',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            formatter: function () {
                const totalTime = this.point.stackTotal.toString().split('.');
                const h = totalTime[0]
                const rm = this.point.stackTotal * 60 - (h * 60);

                const projectTime = this.point.y.toString().split('.');
                const ph = projectTime[0]
                const prm = this.point.y * 60 - (ph * 60);

                return this.series.name + '-' + ph + ':' + Math.floor(prm) + ' hr' +'<br/>Total HR:' + h + ':' + Math.floor(rm) + ' hr'
            },
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: '#000'
                    },
                    formatter: function () {
                        const time = this.y.toString().split('.');
                        let m = this.y * 60;
                        const h = time[0]
                        const rm = m - (h * 60);
                        return h + ':' + Math.floor(rm)
                    },
                    inside: true,
                },
                groupPadding: 0
            },
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        navigation: {
            buttonOptions: {
                enabled: false
            }
        },
        series: data.data
    });
    // start for no records founds
}
