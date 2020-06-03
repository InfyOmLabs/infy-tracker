$(window).on('load', function () {
    loadUsersOpenTasks();
});

window.loadUsersOpenTasks = function () {
    $.ajax({
        type: 'GET',
        url: usersOpenTasksUrl,
        cache: false,
    }).done(prepareUsersOpenTasksChart);
};

window.prepareUsersOpenTasksChart = function (result) {
    $('#users-open-tasks-container').html('');
    let data = result.data;
    if (data.totalRecords === 0) {
        $('#users-open-tasks-container').empty();
        $('#users-open-tasks-container').append(
            '<div align="center" class="no-record">No Records Found</div>');
        return true;
    } else {
        $('#users-open-tasks-container').html('');
        $('#users-open-tasks-container').append('<canvas id="users-open-tasks"></canvas>');
    }
    let ctx = document.getElementById('users-open-tasks').getContext('2d');
    ctx.canvas.style.height = '550px';
    ctx.canvas.style.width = '100%';

    let barChartData = {
        labels: data.name,
        datasets: data.data,
    };

    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                callbacks: {
                    title: function (tooltipItem, data) {
                        let tasks = 0;
                        tooltipItem.forEach(item => {
                            tasks = tasks + item.yLabel;
                        });
                        return tooltipItem[0].label + ' - ' + tasks;
                    },
                    label: function (tooltipItem, data) {
                        if (tooltipItem.yLabel == '0') {
                            return ''
                        }
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': '
                        }
                        return label + tooltipItem.yLabel
                    },
                },
            },
            responsive: false,
            maintainAspectRatio: false,
            ticks: {
                beginAtZero: true,
                stepSize: 2,
            },
            scales: {
                xAxes: [
                    {
                        stacked: true,
                    } ],
                yAxes: [
                    {
                        stacked: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Tasks',
                        },
                    } ],
            },
        },
    })
};
