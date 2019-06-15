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
}

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
}

window.prepareDeveloperWorkReport = function (result) {
    let data = result.data;
    if (data.totalRecords === 0) {
        $('#developers-daily-work-report-container').empty();
        $('#developers-daily-work-report-container').append('<div align="center" class="no-record">No Records Found</div>');
        return true
    }
    Highcharts.chart('developers-daily-work-report', {
        chart: {
            type: 'column'
        },

        title: {
            text: data.label
        },

        xAxis: {
            type: 'category',
            title: {
                text: 'Developers'
            }
        },

        yAxis: {
            title: {
                text: 'Hours'
            }
        },

        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        const time = this.y.toString().split('.');
                        let m = this.y * 60;
                        const h = time[0]
                        const rm = m - (h * 60);
                        return this.y === 0 ? 'On Leave' : h + ':' + Math.floor(rm) + ' hr'
                    },
                },
            }
        },

        credits: {
            enabled: false
        },

        tooltip: {
            formatter: function () {
                const time = this.y.toString().split('.');
                let m = this.y * 60;
                const h = time[0]
                const rm = m - (h * 60);
                return this.series.name + ' <br/>Total HR:' + h + ':' + Math.floor(rm) + ' hr'
            },
        },

        series: [
            {
                name: "Daily Reports",
                colorByPoint: true,
                data: data.data
            }
        ],

        drilldown: {
            series: data.drilldown
        },

        navigation: {
            buttonOptions: {
                enabled: false
            }
        },
    });
}
