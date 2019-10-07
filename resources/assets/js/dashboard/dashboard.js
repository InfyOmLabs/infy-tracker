$('#userId').select2({
    width: '110%',
    placeholder: 'Select User',
})
let timeRange = $('#time_range')
const today = moment()
let start = today.clone().startOf('month')
let end = today.clone().endOf('month')
let userId = $('#userId').val()
let isPickerApply = false
$(window).on('load', function () {
    loadUserWorkReport(start.format('YYYY-MM-D  H:mm:ss'),
        end.format('YYYY-MM-D  H:mm:ss'), userId)
})

timeRange.on('apply.daterangepicker', function (ev, picker) {
    isPickerApply = true
    start = picker.startDate.format('YYYY-MM-D  H:mm:ss')
    end = picker.endDate.format('YYYY-MM-D  H:mm:ss')
    loadUserWorkReport(start, end, userId)
})

window.cb = function (start, end) {
    timeRange.find('span').
        html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'))
}

cb(start, end)

const lastMonth = moment().startOf('month').subtract(1, 'days')

timeRange.daterangepicker({
    startDate: start,
    endDate: end,
    opens: 'left',
    showDropdowns: true,
    autoUpdateInput: false,
    ranges: {
        'Today': [moment(), moment()],
        'This Week': [moment().startOf('week'), moment().endOf('week')],
        'Last Week': [
            moment().startOf('week').subtract(7, 'days'),
            moment().startOf('week').subtract(1, 'days')],
        'This Month': [start, end],
        'Last Month': [
            lastMonth.clone().startOf('month'),
            lastMonth.clone().endOf('month')],
    },
}, cb)

$('#userId').on('change', function (e) {
    e.preventDefault()
    userId = $('#userId').val()
    let startDate = (isPickerApply) ? start : start.format(
        'YYYY-MM-D  H:mm:ss')
    let endDate = (isPickerApply) ? end : end.format('YYYY-MM-D  H:mm:ss')
    loadUserWorkReport(startDate, endDate, userId)
})

window.loadUserWorkReport = function (startDate, endDate, userId) {
    $.ajax({
        type: 'GET',
        url: userReportUrl,
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
        },
        cache: false,
    }).done(prepareUserWorkReport)
}

window.prepareUserWorkReport = function (result) {
    $('#daily-work-report').html('')
    let data = result.data
    if (data.totalRecords === 0) {
        $('#work-report-container').html('')
        $('#work-report-container').
            append(
                '<div align="center" class="no-record">No Records Found</div>')
        return true
    } else {
        $('#work-report-container').html('')
        $('#work-report-container').
            append('<canvas id="daily-work-report"></canvas>')
    }

    let barChartData = {
        labels: data.date,
        datasets: data.data,
    }
    let ctx = document.getElementById('daily-work-report').getContext('2d')
    ctx.canvas.style.height = '400px'
    ctx.canvas.style.width = '100%'
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            title: {
                display: false,
                text: data.label,
            },
            tooltips: {
                mode: 'index',
                callbacks: {
                    label: function (tooltipItem, data) {
                        result = roundToQuarterHour(tooltipItem.yLabel)
                        if (result == '0min') {
                            return ''
                        }
                        let label = data.datasets[tooltipItem.datasetIndex].label ||
                            ''

                        if (label) {
                            label += ': '
                        }
                        return label + result
                    },
                },
            },
            responsive: false,
            maintainAspectRatio: false,
            scales: {

                xAxes: [
                    {
                        stacked: true,
                    }],
                yAxes: [
                    {
                        stacked: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours',
                        },
                    }],
            },
        },
    })
}

window.roundToQuarterHour = function (duration) {
    const totalTime = duration.toString().split('.')
    const hours = parseInt(totalTime[0])
    const minutes = Math.floor((duration * 60)) - Math.floor((hours * 60))
    if (hours === 0) {
        return minutes + 'min'
    }

    if (minutes > 0) {
        return hours + 'hr ' + minutes + 'min'
    }
    return hours + 'hr'
}
