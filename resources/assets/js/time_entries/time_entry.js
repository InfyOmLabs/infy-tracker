$('#taskId,#editTaskId').select2({
    width: '100%',
    placeholder: "Select Task"
});

$('#duration').prop('disabled', true);

$('#timeProjectId,#editTimeProjectId').select2({
    width: '100%',
    placeholder: "Select Project"
});

$('#filterActivity,#filterUser,#filter_project').select2();

$('#activityTypeId,#editActivityTypeId').select2({
    width: '100%',
    placeholder: "Select Activity Type"
});

let isEdit = false;
let editTaskId, editProjectId = null;
let tbl = $('#timeEntryTable').DataTable({
    processing: true,
    serverSide: true,
    "order": [[9, "desc"]],
    ajax: {
        url: timeEntryUrl,
        data: function (data) {
            data.filter_project = $('#filter_project').find('option:selected').val();
            data.filter_activity = $('#filterActivity').find('option:selected').val();
            data.filter_user = $('#filterUser').find('option:selected').val();
        }
    },
    columnDefs: [
        {
            "targets": [9],
            "width": "7%",
            "className": 'text-center',
            "visible": false
        },
        {
            "targets": [6],
            "width": "9%"
        },
        {
            "targets": [7],
            "width": "4%"
        },
        {
            "targets": [4, 5],
            "width": "10%"
        },
        {
            "targets": [8],
            "orderable": false,
            "className": 'text-center',
            "width": '5%'
        },
        {
            "targets": [3],
            "width": "8%"
        },
        {
            "targets": [0, 1],
            "width": "3%"
        },
    ],
    columns: [
        {
            className: 'details-control',
            defaultContent: "<a title='Expand' class='btn btn-success collapse-icon action-btn btn-sm'><span class='fa fa-plus-circle action-icon'></span></a>",
            data: null,
            orderable: false,
            searchable: false,
            name: "note"
        },
        {
            data: function (row) {
                if (row.user) {
                    return '<img class="assignee__avatar" src="' + row.user.img_avatar + '" data-toggle="tooltip" title="' + row.user.name + '">';
                } else {
                    return '';
                }
            },
            name: 'user.name',
        },
        {
            data: function (row) {
                let taskPrefix = row.task.project.prefix + '-' + row.task.task_number;
                let url = taskUrl + taskPrefix;

                return '<a href="' + url + '">' + taskPrefix + ' ' + row.task.title + '</a>'
            },
            name: 'task.title'
        },
        {
            data: 'activity_type.name',
            name: 'activityType.name',
            defaultContent: '',
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
            data: function (row) {
                return roundToQuarterHourAll(row.duration);
            },
            name: 'duration'
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                if(row.entry_type == 1) {
                    return '<span class="badge badge-primary">' + row.entry_type_string + '</span>';
                }
                return '<span class="badge badge-secondary">' + row.entry_type_string + '</span>';
            },
            name: 'entry_type'
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm btn-edit mr-1" data-id="' + row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm btn-delete" data-id="' + row.id + '" >' +
                    '<i class="cui-trash action-icon"></i></a>'
            }, name: 'id'
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
        $('#filterActivity,#filterUser,#filterTask,#filter_project').change(function () {
            tbl.ajax.reload();
        });
    }
});

$('#timeEntryTable tbody').off('click', 'tr td.details-control');
$('#timeEntryTable tbody').on('click', 'tr td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = tbl.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        row.child('<div style="padding-left:50px;">' + nl2br(row.data().note) + '</div>').show();
        tr.addClass('shown');
    }
});

if (!canManageEntries) {
    tbl.columns([0]).visible(false);
}

$('#timeEntryTable').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
});

$('#timeEntryAddForm').submit(function (event) {
    event.preventDefault();
    $('#taskId').removeAttr('disabled');
    const loadingButton = jQuery(this).find("#btnSave");
    loadingButton.button('loading');
    $.ajax({
        url: storeTimeEntriesUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#timeEntryAddModal').modal('hide');
                $('#timeEntryTable').DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            printErrorMessage("#tmValidationErrorsBox", result);
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#timeEntryAddModal').on('hidden.bs.modal', function () {
    isEdit = false;
    $("#startTime").data("DateTimePicker").date(null);
    $("#endTime").data("DateTimePicker").date(null);
    $('#taskId').val(null).trigger("change");
    $('#activityTypeId').val(null).trigger("change");
    $('#duration').prop('disabled', false);
    $('#startTime').prop('disabled', false);
    $('#endTime').prop('disabled', false);
    resetModalForm('#timeEntryAddForm', '#tmValidationErrorsBox');
});

$('#startTime,#endTime').on('dp.change', function () {
    const startTime = $('#startTime').val();
    const endTime = $('#endTime').val();
    let minutes = 0;
    if (endTime) {
        const diff = new Date(Date.parse(endTime) - Date.parse(startTime));
        minutes = diff / (1000 * 60);
        if (!Number.isInteger(minutes)) {
            minutes = minutes.toFixed(2);
        }
    }
    $('#duration').val(minutes).prop('disabled', true);
});

$("#startTime").attr("placeholder", 'YYYY-MM-DD HH:mm:ss');
$("#endTime").attr("placeholder", 'YYYY-MM-DD HH:mm:ss');

$('#dvStartTime,#dvEndTime').on("click", function () {
    $('#startTime').removeAttr('disabled');
    $('#endTime').removeAttr('disabled');
    $('#duration').prop('disabled', true);
});

$('#editStartTime,#editEndTime').on('dp.change', function () {
    const startTime = $('#editStartTime').val();
    const endTime = $('#editEndTime').val();
    let minutes = 0;
    if (endTime) {
        const diff = new Date(Date.parse(endTime) - Date.parse(startTime));
        minutes = diff / (1000 * 60);
        if (!Number.isInteger(minutes)) {
            minutes = minutes.toFixed(2);
        }
    }
    $('#editDuration').val(minutes).prop('disabled', true);
    $('#editStartTime').data("DateTimePicker").maxDate(moment().endOf('now'));
    $('#editEndTime').data("DateTimePicker").maxDate(moment().endOf('now'));
});

$('#startTime,#editStartTime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: true,
    icons: {
        up: "icon-arrow-up icons",
        down: "icon-arrow-down icons",
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    maxDate: moment().endOf('day'),
});
$('#endTime,#editEndTime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: true,
    icons: {
        up: "icon-arrow-up icons",
        down: "icon-arrow-down icons",
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    maxDate: moment().endOf('day'),
});
$('#startTime,#endTime').on('dp.change', function (selected) {
    $('#startTime').data("DateTimePicker").maxDate(moment().endOf('now'));
    $('#endTime').data("DateTimePicker").maxDate(moment().endOf('now'));
});
$('#endTime').val(moment().format('YYYY-MM-DD HH:mm:ss'));

$('#editTimeEntryForm').submit(function (event) {
    event.preventDefault();
    const loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    const id = $('#entryId').val();
    $.ajax({
        url: timeEntryUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#editTimeEntryModal').modal('hide');
                $('#timeEntryTable').DataTable().ajax.reload(null, false);
                if ($.isFunction(window.taskDetails)) {
                    taskDetails(result.data.task_id);
                }
            }
        },
        error: function (error) {
            manageAjaxErrors(error, 'teEditValidationErrorsBox');
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#editTimeEntryModal').on('hidden.bs.modal', function () {
    $('#editDuration').prop('disabled', false);
    $('#editStartTime').prop('disabled', false);
    $('#editEndTime').prop('disabled', false);
    resetModalForm('#editTimeEntryForm', '#teEditValidationErrorsBox');
});

window.renderTimeEntry = function (id) {
    $.ajax({
        url: timeEntryUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let timeEntry = result.data;
                editTaskId = timeEntry.task_id;
                editProjectId = timeEntry.project_id;
                $('#editTimeProjectId').val(timeEntry.project_id).trigger('change');
                $('#entryId').val(timeEntry.id);
                $('#editTaskId').val(timeEntry.task_id).trigger("change");
                $('#editActivityTypeId').val(timeEntry.activity_type_id).trigger("change");
                $('#editDuration').val(timeEntry.duration);
                $('#editStartTime').val(timeEntry.start_time);
                $('#editEndTime').val(timeEntry.end_time);
                $('#editNote').val(timeEntry.note);
                $('#editTimeEntryModal').modal('show');
                //add it cause of project_id change, when it change it sets tasks dynamically and selected task_id vanished
                setTimeout(function () {
                    $('#editTaskId').val(timeEntry.task_id).trigger("change");
                }, 1500);
            }
        },
        error: function (error) {
            manageAjaxErrors(error, 'teEditValidationErrorsBox');
        }
    });
};

$(document).on('click', '.btn-edit', function (event) {
    let timeId = $(event.currentTarget).data('id');
    renderTimeEntry(timeId);
});

$(document).on('click', '.btn-delete', function (event) {
    let timeId = $(event.currentTarget).data('id');
    deleteItem(timeEntryUrl + timeId, '#timeEntryTable', 'Time Entry');
});

window.getTasksByProject = function (projectId, taskId, selectedId, errorBoxId) {
    if (!(projectId > 0)) {
        return false;
    }
    let taskURL = projectsURL + projectId + '/tasks';
    taskURL = (isEdit) ? taskURL + '?task_id=' + editTaskId : taskURL;

    $.ajax({
        url: taskURL,
        type: 'get',
        success: function (result) {
            var tasks = result.data;
            if (selectedId > 0) {
                var options = '<option value="0" disabled>Select Task</option>';
            } else {
                var options = '<option value="0" disabled selected>Select Task</option>';
            }
            $.each(tasks, function (key, value) {
                if (selectedId > 0 && selectedId == key) {
                    options += '<option value="' + key + '" selected>' + value + '</option>';
                } else {
                    options += '<option value="' + key + '">' + value + '</option>';
                }
            });
            $(taskId).html(options);
            if (selectedId > 0) {
                $(taskId).val(selectedId).trigger("change");
            }
        },
        error: function (result) {
            printErrorMessage(errorBoxId, result);
        }
    });
};

$("#timeProjectId").on('change', function () {
    $("#taskId").select2("val", "");
    const projectId = $(this).val();
    getTasksByProject(projectId, '#taskId', 0, '#tmValidationErrorsBox');
});

$("#editTimeProjectId").on('change', function () {
    $("#editTaskId").select2("val", "");
    const projectId = $(this).val();
    isEdit = (editProjectId == projectId) ? true : false;

    getTasksByProject(projectId, '#editTaskId', 0, '#teEditValidationErrorsBox');
});

$("#new_entry").click(function () {
    var tracketProjectId = localStorage.getItem('project_id');
    $("#timeProjectId").val(tracketProjectId);
    $("#timeProjectId").trigger('change');
    getTasksByProject(tracketProjectId, '#taskId', 0, '#tmValidationErrorsBox');
});
