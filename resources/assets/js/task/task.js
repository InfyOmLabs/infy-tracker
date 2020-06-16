$(function () {
    $('#no-record-info-msg').hide()
    $('#user-drop-down-body').hide()

    $('#filter_project,#filter_user').select2()
    $('#filter_status').select2({
        minimumResultsForSearch: -1,
    })
    $('#assignTo,#editAssignTo').select2({
        width: '100%',
        placeholder: 'Select Assignee',
    })
    $('#projectId,#editProjectId').select2({
        width: '100%',
        placeholder: 'Select Project',
    })
    $('#priority,#editPriority').select2({
        width: '100%',
        placeholder: 'Select Priority',
    })
    $('#assignee,#editAssignee').select2({
        width: '100%',
    })
    $('#tagIds,#editTagIds').select2({
        width: '100%',
        tags: true,
        createTag: function (tag) {
            let found = false
            $('#tagIds option').each(function () {
                if ($.trim(tag.term).toUpperCase() ===
                    $.trim($(this).text()).toUpperCase()) {
                    found = true
                }
            })
            if (!found) {
                return {
                    id: tag.term,
                    text: tag.term,
                }
            }
        },
    })

    $('#dueDate,#editDueDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        minDate: moment().millisecond(0).second(0).minute(0).hour(0),
    })

    $('#dueDateFilter').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
            clear: 'icon-trash icons',
        },
        sideBySide: true,
        showClear: true,
    })
    tbl.ajax.reload()

    $(document).ajaxComplete(function (result) {
        $('input[name=yes]').iCheck({
            checkboxClass: 'icheckbox_line-green',
            insert: '<div class="icheck_line-icon"></div>',
        })
        $('input[name=no]').iCheck({
            checkboxClass: 'icheckbox_line-white',
            insert: '<div class="icheck_line-icon"></div>',
        })
    })

    $('[data-toggle="tooltip"]').tooltip()
})

let taskAssignees = []

function getRandomColor () {
    let num = Math.floor(Math.random() * 12) + 1
    let coloCodes = [
        '0095ff',
        '9594fe',
        'da4342',
        '8e751c',
        'ac1f87',
        'c86069',
        '370e1c',
        'ca4e7d',
        'c02bd8',
        '289e05',
        '3aad14',
        '0D8ABC',
        '511852']
    return coloCodes[num]
}

var tbl = $('#task_table').DataTable({
    processing: true,
    serverSide: true,
    'order': [[5, 'desc']],
    ajax: {
        url: taskIndexUrl,
        data: function (data) {
            data.filter_project = $('#filter_project').
                find('option:selected').
                val();
            data.filter_user = $('#filter_user').find('option:selected').val();
            data.filter_status = $('#filter_status').
                find('option:selected').
                val();
            data.due_date_filter = $('#dueDateFilter').val()
        },
    },
    columnDefs: [
        {
            'targets': [7],
            'orderable': false,
            'width': '9%',
        },
        {
            'targets': [0],
            'width': '2%',
            'className': 'text-center',
            'orderable': false,
        },
        {
            'targets': [2],
            'orderable': false,
        },
        {
            'targets': [3],
            'width': '6%',
        },
        {
            'targets': [4, 5],
            'width': '10%',
            'className': 'text-center',
        },
        {
            'targets': [6],
            'width': '6%',
            'className': 'text-center',
        },
    ],
    columns: [
        {
            data: function (row) {
                return row.status == 1
                    ? '<div class="active_btn" title="Mark as pending"><input name="yes" id="enabled" class="enabled" type="checkbox" checked data-check="' +
                    row.id + '"></div>'
                    : '<div class="active_btn" title="Mark as complete"><input name="no" id="disabled" type="checkbox" class="enabled" data-check="' +
                    row.id + '"></div>'
            }, name: 'status',
        },
        {
            data: function (row) {
                let url = taskUrl + row.project.prefix + '-' + row.task_number
                return '<a href="' + url + '">' + row.title + '</a>'
            },
            name: 'title',
        },
        {
            data: function (row) {
                let imgStr = '';
                $(row.task_assignee).each(function (i, e) {
                    imgStr += '<img class="assignee__avatar" src="' +
                        e.img_avatar + '" data-toggle="tooltip" title="' +
                        e.name + '">';
                });

                return imgStr;
            }, name: 'taskAssignee.name',
        },
        {
            data: function (row) {
                const priority = row.priority;
                const priorityColors = {
                    'highest': '#FF0000',
                    'high': '#FF3333',
                    'medium': '#FF8000',
                    'low': '#336600',
                    'lowest': '#4C9900',
                };

                return '<i class="fa fa-arrow-up" style="color: ' +
                    priorityColors[priority] + '"></i> ' +
                    priority.charAt(0).toUpperCase() + priority.slice(1);
            }, name: 'priority',
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                if (row.due_date == null || row.due_date === '') {
                    return '';
                }

                let todayDate = (new Date()).toISOString().split('T')[0];
                if (row.status === 0 && todayDate > row.due_date) {
                    return '<span class="text-danger">' + format(row.due_date) + '</span>';
                }

                return format(row.due_date);
            },
            name: 'due_date',
        },
        {
            data: function (row) {
                return row
            },
            render: function (row) {
                return '<span data-toggle="tooltip" title="' +
                    format(row.created_at, 'hh:mm:ss a') + '">' +
                    format(row.created_at) + '</span>'
            },
            name: 'created_at',
        },
        {
            data: function (row) {
                if (row.created_user) {
                    return '<img class="assignee__avatar" src="' +
                        row.created_user.img_avatar +
                        '" data-toggle="tooltip" title="' +
                        row.created_user.name + '">'
                } else {
                    return ''
                }
            }, name: 'createdUser.name',
        },
        {
            data: function (row) {
                let taskAssignee = []
                $.each(row.task_assignee, function (key, value) {
                    taskAssignee.push(value.id)
                })
                let actionString =
                    '<a title="Details" data-toggle="modal" class="btn action-btn btn-info btn-sm taskDetails mr-1"  data-target="#taskDetailsModal" data-id="' +
                    row.id + '"> ' +
                    '<i class="fa fa-clock action-icon"></i></a>' +
                    '<a title="Edit" class="btn action-btn btn-primary btn-sm mr-1 edit-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm btn-task-delete" data-task-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>'

                if ($.inArray(loggedInUserId, taskAssignee) > -1) {
                    actionString += '<a title="Add Time Entry" class="btn btn-success action-btn btn-sm entry-model ml-1" data-toggle="modal" data-target="#timeEntryAddModal" data-id="' +
                        row.id + '" data-project-id="' + row.project.id + '">' +
                        '<i class="fa fa-user-clock action-icon"></i></a>'
                }

                return actionString
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $('#filter_project,#filter_status,#filter_user').change(function () {
            tbl.ajax.reload()
        })

        $('#dueDateFilter').on('dp.change', function (e) {
            tbl.ajax.reload()
        })
    },
})

$('#task_table').on('draw.dt', function () {
    $('.tooltip').tooltip('hide')
    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
})

// open edit user model
$(document).on('click', '.edit-btn', function (event) {
    let id = $(event.currentTarget).data('id')
    $.ajax({
        url: taskUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let task = result.data.task;
                let allTags = result.data.tags;
                $('#editTagIds').empty();
                $.each(allTags, function (i, e) {
                    $('#editTagIds').
                        append($('<option>', { value: i, text: e }));
                });

                let desc = task.description;
                quillEdit.clipboard.dangerouslyPasteHTML(0, desc);  // to set the HTML content to Quill Editor instance/container

                $('#tagId').val(task.id);
                $('#editTitle').val(task.title);
                $('#taskEditDescription').val(task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger('change');
                $('#editStatus').val(task.status);

                var tagsIds = [];
                var userIds = [];
                taskAssignees = []
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id)
                })
                $(task.task_assignee).each(function (i, e) {
                    userIds.push(e.id)
                    taskAssignees.push(e.id)
                })
                $('#editTagIds').val(tagsIds).trigger('change')

                $('#editAssignee').val(userIds).trigger('change')
                $('#editPriority').val(task.priority).trigger('change')

                setTimeout(function () {
                    $.each(task.task_assignee, function (i, e) {
                        $('#editAssignee option[value=\'' + e.id + '\']').
                            prop('selected', true).
                            trigger('change')
                    })
                    $('#EditModal').modal('show')

                }, 1500)
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
    })
})

// open delete confirmation model
$(document).on('click', '.delete-btn', function (event) {
    let id = $(event.currentTarget).data('id')
    deleteItem(taskUrl + id, '#task_table', 'Task')
})

$('#addNewForm').submit(function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find('#btnTaskSave');
    loadingButton.button('loading');

    let formdata = $(this).serialize();
    let desc = quill.root.innerHTML;  // retrieve the HTML content from the Quill container
    formdata = formdata.replace('description=', 'description=' + desc);
    $.ajax({
        url: createTaskUrl,
        type: 'POST',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#AddModal').modal('hide');
                $('#task_table').DataTable().ajax.reload();
                revokerTracker()
            }
        },
        error: function (result) {
            printErrorMessage('#validationErrorsBox', result)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#editForm').submit(function (event) {
    event.preventDefault()
    let loadingButton = jQuery(this).find('#btnTaskEditSave')
    loadingButton.button('loading')
    let id = $('#tagId').val()
    let formdata = $(this).serializeArray()
    $.each(formdata, function (i, val) {
        // getText() for Quill Editor will get the text of the specific editor instance
        if (val.name == 'description' && quillEdit.getText() !== '') {
            formdata[i].value = quillEdit.root.innerHTML;
        }
    })
    $.ajax({
        url: taskUrl + id,
        type: 'put',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#EditModal').modal('hide')
                $('#task_table').DataTable().ajax.reload()
                revokerTracker()
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#AddModal').on('hidden.bs.modal', function () {
    quill.setContents([{ insert: '\n' }]);  // to empty content of the Quill Editor instance/container
    $('#projectId').val(null).trigger('change');
    $('#assignee').val(null).trigger('change');
    $('#tagIds').val(null).trigger('change');
    $('#priority').val(null).trigger('change');
    resetModalForm('#addNewForm', '#validationErrorsBox');
})

$('#EditModal').on('hidden.bs.modal', function () {
    quillEdit.setContents([{ insert: '\n' }]);
    resetModalForm('#editForm', '#editValidationErrorsBox');
})

$(function () {

    $(document).ajaxComplete(function () {
        $('input[class=enabled]').on('ifChanged', function (e) {
            var taskId = ($(this).attr('data-check'))
            manageCheckbox(this)
            updateTaskStatus(taskId)
        })
    })

    function updateTaskStatus (id) {
        let stopwatchTaskId = getItemFromLocalStorage('task_id')
        let isClockRunning = getItemFromLocalStorage('clockRunning')
        if (id === stopwatchTaskId && isClockRunning === 'true') {
            tbl.ajax.reload();
            swal({
                'title': 'Warning',
                'text': 'Please stop timer before completing task.',
                'type': 'warning',
            });
            return false;
        }
        $.ajax({
            url: taskUrl + id + '/update-status',
            type: 'POST',
            cache: false,
            success: function (result) {
                if (result.success) {
                    $('#task_table').DataTable().ajax.reload(null, false)
                    revokerTracker()
                }
            },
        })
    }
})

window.manageCollapseIcon = function (id) {
    var isExpanded = $('#tdCollapse' + id).attr('aria-expanded')
    if (isExpanded === 'true') {
        $('#tdCollapse' + id).find('a span').removeClass('fa-minus-circle');
        $('#tdCollapse' + id).find('a span').addClass('fa-plus-circle');
    } else {
        $('#tdCollapse' + id).find('a span').removeClass('fa-plus-circle');
        $('#tdCollapse' + id).find('a span').addClass('fa-minus-circle');
    }
}

window.deleteTimeEntry = function (timeEntryId) {
    let url = timeEntryUrl + timeEntryId
    swal({
            title: 'Delete !',
            text: 'Are you sure you want to delete this Time Entry?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
        },
        function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        $('.close').trigger('click')
                    }
                    swal({
                        title: 'Deleted!',
                        text: 'Time Entry has been deleted.',
                        type: 'success',
                        timer: 2000,
                    })
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        timer: 5000,
                    })
                },
            })
        })
}

function setTaskDrp (id) {
    $('#taskId').val(id).trigger('change')
    $('#taskId').prop('disabled', true)
}

$(document).on('click', '.entry-model', function (event) {
    let taskId = $(event.currentTarget).data('id');
    let projectId = $(event.currentTarget).data('project-id');
    $('#timeProjectId').val(projectId).trigger('change');
    getTasksByProject(projectId, '#taskId', taskId, '#tmValidationErrorsBox');

    setTimeout(function () {
        $('#taskId').val(taskId).trigger('change');
    }, 1500);
})

const toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],        
    ['blockquote', 'code-block'],

    [{ 'header': 1 }, { 'header': 2 }],               
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],      
    [{ 'indent': '-1'}, { 'indent': '+1' }],          
    [{ 'direction': 'rtl' }],                        

    [{ 'size': ['small', false, 'large', 'huge'] }],  
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

    [{ 'color': [] }, { 'background': [] }],          
    [{ 'font': [] }],
    [{ 'align': [] }],

    ['clean']                                        
];

// quill editor initialization scripts
let quill = new Quill('#taskDescriptionContainer', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow',
    placeholder: 'Add task description...',
});

let quillEdit = new Quill('#taskEditDescriptionContainer', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow',
    placeholder: 'Add task description...',
});

$(document).on('change', '#projectId', function (event) {
    let projectId = $(this).val();
    loadProjectAssignees(projectId, 'assignee');
});

$(document).on('change', '#editProjectId', function (event) {
    let projectId = $(this).val()
    loadProjectAssignees(projectId, 'editAssignee')
    setTimeout(function () {
        $('#editAssignee').val(taskAssignees).trigger('change')
    }, 1500)
})

function loadProjectAssignees (projectId, selector) {
    let url = usersOfProjects + '?projectIds=' + projectId
    $('#' + selector).empty()
    $('#' + selector).trigger('change')
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            const users = result.data;
            for (const key in users) {
                if (users.hasOwnProperty(key)) {
                    $('#' + selector).
                        append($('<option>', { value: key, text: users[key] }));
                }
            }
            // condition applied only when new task modal is opened
            if ($('#projectId').val() !== '') {
                $('#' + selector).val(currentLoggedInUserId);
                $('#' + selector).trigger('change.select2');
            }
        },
    })
}

//modal not closed on click outside
$('.modal').modal({ show: false, backdrop: 'static' })
