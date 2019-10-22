$('#task_users').
    select2({ width: '100%', placeholder: 'All', minimumResultsForSearch: -1 })

let firstTime = true

// open detail confirmation model
$(document).on('click', '.taskDetails', function (event) {
    let id = $(event.currentTarget).data('id')
    startLoader()
    $('#no-record-info-msg').hide()
    $('#taskDetailsTable').hide()
    $('.time-entry-data').hide()
    firstTime = true

    $.ajax({
        url: taskUrl + id + '/' + 'users',
        type: 'GET',
        success: function (result) {
            $('#task_users').empty('')
            $('#task_users').attr('data-task_id', id)
            const newOption = new Option('All', 0, false, false)
            $('#task_users').append(newOption).trigger('change')
            $.each(result, function (key, value) {
                const newOption = new Option(value, key + '-' + id, false,
                    false)
                $('#task_users').append(newOption)
            })
        },
    })
})

$(document).on('change', '#task_users', function () {
    let taskId = $(this).attr('data-task_id')
    let taskUserId = $(this).val().split('-')
    let userId = 0
    if (taskUserId.length > 1) {
        taskId = taskUserId[1]
        userId = taskUserId[0]
    }
    let url = taskDetailUrl + '/' + taskId
    let startSymbol = '?'
    if (userId !== 0) {
        startSymbol = '&'
        url = url + '?user_id=' + userId
    }
    if (reportStartDate != '' && reportEndDate != '') {
        url = url + startSymbol + 'start_time=' + reportStartDate +
            '&end_time=' + reportEndDate
    }
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let data = result.data
                let url = taskUrl + data.project.prefix + '-' +
                    data.task_number
                $('#task-heading').
                    html('<h5>Task: <a href=\'' + url +
                        '\' style=\'color: #0f6683\'>' + data.title +
                        '</a></h5>')
                drawTaskDetailTable(data)
            }
        },
    })
})

window.drawTaskDetailTable = function (data) {
    if (data.totalDuration === 0 && firstTime) {
        $('#no-record-info-msg').show()
        $('.time-entry-data').hide()
        stopLoader()
        return true
    }
    firstTime = false
    let taskDetailsTable = $('#taskDetailsTable').DataTable({
        destroy: true,
        paging: true,
        data: data.time_entries,
        searching: false,
        lengthChange: false,
        columns: [
            {
                className: 'details-control',
                defaultContent: '<a class=\'btn btn-success collapse-icon action-btn btn-sm\'><span class=\'fa fa-plus-circle action-icon\'></span></a>',
                data: null,
                orderable: false,
            },
            { data: 'user.name' },
            { data: 'start_time' },
            { data: 'end_time' },
            {
                data: function (row) {
                    return roundToQuarterHourAll(row.duration)
                },
            },
            {
                orderable: false,
                data: function (data) {
                    return '<a title=\'Edit\' class=\'btn action-btn btn-primary btn-sm mr-1\' onclick=\'renderTimeEntry(' +
                        data.id +
                        ')\' ><i class=\'cui-pencil action-icon\'></i></a>' +
                        '<a title=\'Delete\' class=\'btn action-btn btn-danger btn-sm\'  onclick=\'deleteTimeEntry(' +
                        data.id +
                        ')\'><i class=\'cui-trash action-icon\'></i></a>'
                },
                visible: taskDetailActionColumnIsVisible,
            },
        ],
    })

    $('#taskDetailsTable th:first').removeClass('sorting_asc')

    $('.time-entry-data').show()
    $('#taskDetailsTable').show()
    $('#user-drop-down-body').show()
    $('#no-record-info-msg').hide()
    stopLoader()

    $('#taskDetailsTable tbody').off('click', 'tr td.details-control')
    $('#taskDetailsTable tbody').
        on('click', 'tr td.details-control', function () {
            var tr = $(this).closest('tr')
            var row = taskDetailsTable.row(tr)

            if (row.child.isShown()) {
                // This row is already open - close it
                $(this).
                    children().
                    children().
                    removeClass('fa-minus-circle').
                    addClass('fa-plus-circle')
                row.child.hide()
                tr.removeClass('shown')
            } else {
                // Open this row
                $(this).
                    children().
                    children().
                    removeClass('fa-plus-circle').
                    addClass('fa-minus-circle')
                row.child('<div style="padding-left:50px;">' +
                    nl2br(row.data().note) + '</div>').show()
                tr.addClass('shown')
            }
        })

    $('#taskDetailsTable_wrapper').css('width', '100%')
    $('#total-duration').
        html('<strong>Total duration: ' + data.totalDuration + ' || ' +
            data.totalDurationMin + ' Minutes</strong>')
}
