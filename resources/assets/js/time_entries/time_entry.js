$('#taskId,#editTaskId').select2({
    width: '100%',
    placeholder: "Select Task"
});

$('#duration').prop('disabled', true);

$('#timeProjectId,#editTimeProjectId').select2({
    width: '100%',
    placeholder: "Select Project"
});

$('#filterActivity,#filterUser').select2({
    minimumResultsForSearch: -1
});

$('#activityTypeId,#editActivityTypeId').select2({
    width: '100%',
    placeholder: "Select Activity Type"
});

let isEdit = false;
let editTaskId = null;
let tbl = $('#timeEntryTable').DataTable({
    processing: true,
    serverSide: true,
    "order": [[6, "desc"]],
    ajax: {
        url: timeEntryUrl,
        data: function (data) {
            data.filter_activity = $('#filterActivity').find('option:selected').val();
            data.filter_user = $('#filterUser').find('option:selected').val();
        }
    },
    columnDefs: [
        {
            "targets": [7],
            "orderable": false,
            "className": 'text-center',
            "width": '5%'
        },
        {
            "targets": [5],
            "width": "9%"
        },
        {
            "targets": [3, 4],
            "width": "10%"
        },
        {
            "targets": [6],
            "width": "10%",
            "className": 'text-center',
        },
    ],
    columns: [
        {
            data: 'user.name',
            name: 'user.name'
        },
        {
            data: function (row) {
                let taskPrefix = row.task.project.prefix + '-' + row.task.task_number;
                let url = taskUrl + taskPrefix;

                return '<a href="' + url + '">' + taskPrefix + ' ' + row.task.title + '</a>'
            },
            name: 'title'
        },
        {
            data: 'activity_type.name',
            name: 'activityType.name',
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
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm btn-edit mr-1" data-id="' + row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm btn-delete" data-id="' + row.id + '" >' +
                    '<i class="cui-trash action-icon"></i></a>'
            }, name: 'id'
        }
    ],
    "fnInitComplete": function () {
        $('#filterActivity,#filterUser,#filterTask').change(function () {
            tbl.ajax.reload();
        });
    }
});

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
    }
    $('#editDuration').val(minutes).prop('disabled', true);
});

$('#startTime,#editStartTime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: true,
    icons: {
        up: "icon-angle-up",
        down: "icon-angle-down"
    },
    sideBySide: true
});
$('#endTime,#editEndTime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: true,
    icons: {
        up: "icon-angle-up",
        down: "icon-angle-down"
    },
    sideBySide: true
});

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
                $('#editTimeProjectId').val(timeEntry.project_id).trigger('change');
                $('#entryId').val(timeEntry.id);
                $('#editTaskId').val(timeEntry.task_id).trigger("change");
                $('#editActivityTypeId').val(timeEntry.activity_type_id).trigger("change");
                $('#editDuration').val(timeEntry.duration);
                $('#editStartTime').val(timeEntry.start_time);
                $('#editEndTime').val(timeEntry.end_time);
                $('#editNote').val(timeEntry.note);
                $('#editTimeEntryModal').modal('show');
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
        async: false,
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
    isEdit = true;
    getTasksByProject(projectId, '#editTaskId', 0, '#teEditValidationErrorsBox');
});

$("#new_entry").click(function () {
    var tracketProjectId = localStorage.getItem('project_id');
    $("#timeProjectId").val(tracketProjectId);
    $("#timeProjectId").trigger('change');
    getTasksByProject(tracketProjectId, '#taskId', 0, '#tmValidationErrorsBox');
});
