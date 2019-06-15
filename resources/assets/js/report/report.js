$(function () {
    $('#filterActivity,#filterUser,#filterTask,#filterProject').select2({
        minimumResultsForSearch: -1
    });
    var $filterDate = $('#filterDate');
    var start = moment().subtract('days');
    var end = moment().subtract('days');

    function cb(start, end) {
        $filterDate.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $filterDate.daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'left',
        showDropdowns: false,
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract('days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    cb(start, end);

    $filterDate.on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-D  H:mm:ss');
        var endDate = picker.endDate.format('YYYY-MM-D  H:mm:ss');

        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        tbl.ajax.reload();
    });

    var tbl = $('#reportTable').DataTable({
        processing: true,
        serverSide: true,
        "order": [[7, "desc"]],
        ajax: {
            url: reportUrl,
            data: function (data) {
                data.filter_activity = $('#filterActivity').find('option:selected').val();
                data.filter_user = $('#filterUser').find('option:selected').val();
                data.filter_task = $('#filterTask').find('option:selected').val();
                data.filter_project = $('#filterProject').find('option:selected').val();
                data.filter_start_date = $('#startDate').val();
                data.filter_end_date = $('#endDate').val();
            }
        },
        columnDefs: [
            {
                "targets": [4, 5],
                "width": "10%"
            },
            {
                "targets": [6],
                "width": "9%"
            },
            {
                "targets": [7],
                "width": "10%",
                "className": "text-center"
            },
        ],
        columns: [
            {
                data: 'user.name',
                name: 'user.name'
            },
            {
                data: 'task.title',
                name: 'task.title'
            },
            {
                data: 'task.project.name',
                name: 'task.project.name'
            },
            {
                data: 'activity_type.name',
                name: 'activityType.name'
            },
            {
                data: 'start_time',
                name: 'start_time'
            },
            {
                data: 'end_time',
                name: 'end_time'
            },
            {
                data: 'duration',
                name: 'duration'
            },
            {
                data: function (row) {
                    return row;
                },
                render: function (row) {
                    return '<span data-toggle="tooltip" title="' + format(row.created_at, "hh:mm:ss a") + '">' + format(row.created_at) + '</span>';
                },
                name: 'created_at'
            },
        ],
        "fnInitComplete": function () {
            $('#filterActivity,#filterUser,#filterTask,#filterProject').change(function () {
                tbl.ajax.reload();
            });
        }
    });
});

$('#reportTable').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
});

