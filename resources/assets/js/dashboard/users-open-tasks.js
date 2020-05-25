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

window.getLabel = function (data) {
    let label = [];
    $.each(data, function (index, value) {
        let string = '';
        $.each(value.projects, function (index, value) {
            string = string + value;
            label.push(string);
        });
    });

    return label;
};

window.prepareUsersOpenTasksChart = function (result) {
    $('#users-open-tasks-container').html('');
    let data = result.data;
    let string = getLabel(data.result);
    console.log(string);
    if (data.totalRecords === 0) {
        $('#users-open-tasks-container').empty();
        $('#users-open-tasks-container').
            append(
                '<div align="center" class="no-record">No Records Found</div>');
        return true;
    } else {
        $('#users-open-tasks-container').html('');
        $('#users-open-tasks-container').
            append('<canvas id="users-open-tasks"></canvas>');
    }
    let ctx = document.getElementById('users-open-tasks').
        getContext('2d');
    ctx.canvas.style.height = '400px';
    ctx.canvas.style.width = '100%';
    let usersOpenTasksChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.data.labels,
            datasets: [
                {
                    label: data.label,
                    data: data.data.data,
                    backgroundColor: data.data.backgroundColor,
                    borderColor: data.data.borderColor,
                    borderWidth: 1,
                }],
        },
        options: {
            tooltips: {
                mode: 'index',
                callbacks: {
                    label: function (tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label ||
                            '';

                        if (label) {
                            label += ': ';
                        }
                        result = tooltipItem.yLabel;
                        return label + result;
                    },
                    // afterBody:{
                    //     return string
                    // }
                },
            },
            scales: {
                yAxes: [
                    {
                        scaleLabel: {
                            display: true,
                            labelString: 'Number Of Tasks',
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                        }
                    }],
            },
            legend: { display: false },
        },
    });
};
