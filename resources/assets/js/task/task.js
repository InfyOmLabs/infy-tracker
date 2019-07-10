$(function () {
    $('#no-record-info-msg').hide();
    $('#filter_project,#filter_status,#filter_user').select2({
        minimumResultsForSearch: -1
    });
    $('#assignTo,#editAssignTo').select2({
        width: '100%',
        placeholder: "Select Assignee"
    });
    $('#projectId,#editProjectId').select2({
        width: '100%',
        placeholder: "Select Project"
    });
    $('#priority,#editPriority').select2({
        width: '100%',
        placeholder: "Select Priority"
    });
    $('#assignee,#editAssignee').select2({
        width: '100%',
    });
    $('#tagIds,#editTagIds').select2({
        width: '100%',
        tags: true,
        createTag: function (tag) {
            var found = false;
            $("#tagIds option").each(function() {
                if ($.trim(tag.term).toUpperCase() === $.trim($(this).text()).toUpperCase()) {
                    found = true;
                }
            });
            if (!found) {
                return {
                    id: tag.term,
                    text: tag.term
                };
            }
        }
    });

    $('#dueDate,#editDueDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        icons: {
            up: "icon-angle-up",
            down: "icon-angle-down"
        },
        sideBySide: true,
        minDate: moment().millisecond(0).second(0).minute(0).hour(0)
    });

    $(document).ajaxComplete(function (result) {
        $('input[name=yes]').iCheck({
            checkboxClass: 'icheckbox_line-green',
            insert: '<div class="icheck_line-icon"></div>'
        });
        $('input[name=no]').iCheck({
            checkboxClass: 'icheckbox_line-white',
            insert: '<div class="icheck_line-icon"></div>'
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
});

function getRandomColor() {
    let num = Math.floor(Math.random() * 12) + 1;
    let coloCodes = ['0095ff', '9594fe', 'da4342', '8e751c', 'ac1f87', 'c86069', '370e1c', 'ca4e7d', 'c02bd8', '289e05', '3aad14', '0D8ABC', '511852'];
    return coloCodes[num];
}

var tbl = $('#task_table').DataTable({
    processing: true,
    serverSide: true,
    "order": [[4, "desc"]],
    ajax: {
        url: taskIndexUrl,
        data: function (data) {
            data.filter_project = $('#filter_project').find('option:selected').val();
            data.filter_user = $('#filter_user').find('option:selected').val();
            data.filter_status = $('#filter_status').find('option:selected').val();
        },
    },
    columnDefs: [
        {
            "targets": [7],
            "orderable": false,
            "className": 'text-center',
            "width": "9%"
        },
        {
            "targets": [0],
            "width": "5%",
            "className": 'text-center',
            "orderable": false,
        },
        {
            "targets": [3],
            "orderable": false,
        },
        {
            "targets": [5, 4],
            "width": "10%",
            "className": 'text-center',
        },
        {
            "targets": [6],
            "className": 'text-center',
        },
    ],
    columns: [
        {
            data: function (row) {
                return row.status == 1 ? '<div class="active_btn"><input name="yes" id="enabled" class="enabled" type="checkbox" checked data-check="' + row.id + '"></div>' : '<div class="active_btn"><input name="no" id="disabled" type="checkbox" class="enabled" data-check="' + row.id + '"></div>';
            }, name: 'status'
        },
        {
            data: function (row) {
                let url = taskUrl + row.project.prefix + '-' + row.task_number;
                return '<a href="' + url + '">' + row.title + '</a>'
            },
            name: 'title'
        },
        {
            data: function (row) {
                if(typeof taskStatus[row.status] == 'undefined')
                    return '';
                else
                    return '<span class="badge '+taskBadges[row.status]+' text-uppercase">'+taskStatus[row.status]+'</span>';
                },
            name: 'status'
        },
        {
            data: function (row) {
                let imgStr = '';
                $(row.task_assignee).each(function (i, e) {
                    let colorCode = getRandomColor();
                    let nameArr = e.name.split(' ');
                    if(nameArr.length >= 2){
                        imgStr += '<img class="assignee__avatar" src="https://ui-avatars.com/api/?name='+nameArr[0]+'+'+nameArr[1]+'&background='+colorCode+'&color=fff&rounded=true&size=30" data-toggle="tooltip" title="'+e.name+'">';
                    }else {
                        imgStr += '<img class="assignee__avatar" src="https://ui-avatars.com/api/?name='+nameArr[0]+'&background='+colorCode+'&color=fff&rounded=true&size=30" data-toggle="tooltip" title="'+e.name+'">';
                    }
                });

                return imgStr;
            }, name: 'taskAssignee.name'
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                return '<span>' + format(row.due_date) + '</span>';
            },
            name: 'due_date'
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
                if(row.created_user) {
                    let colorCode = getRandomColor();
                    let nameArr = row.created_user.name.split(' ');
                    return '<img class="assignee__avatar" src="https://ui-avatars.com/api/?name='+nameArr[0]+'+'+nameArr[1]+'&background='+colorCode+'&color=fff&rounded=true&size=30" data-toggle="tooltip" title="'+row.created_user.name+'">';
                } else {
                    return '';
                }
            }, name: 'createdUser.name'
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm mr-1 edit-btn" data-id="' + row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm btn-task-delete mr-1" data-task-id="' + row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>' +
                    '<a title="Add Timer Entry" class="btn btn-success action-btn btn-sm entry-model mr-1" data-toggle="modal" data-target="#timeEntryAddModal" data-id="' + row.id + '" data-project-id="' + row.project.id + '">' +
                    '<i class="fa fa-user-clock action-icon"></i></a>' +
                    '<a title="Details" data-toggle="modal" class="btn action-btn btn-info btn-sm taskDetails"  data-target="#taskDetailsModal" data-id="' + row.id + '"> ' +
                    '<i class="fa fa-clock action-icon"></i></a>'
            }, name: 'id'
        }
    ],
    "fnInitComplete": function () {
        $('#filter_project,#filter_status,#filter_user').change(function () {
            tbl.ajax.reload();
        });
    },
});

$('#task_table').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// open edit user model
$(document).on('click', '.edit-btn', function (event) {
    let id = $(event.currentTarget).data('id');
    $.ajax({
        url: taskUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let task = result.data;
                let desc = $('<div/>').html(task.description).text();
                CKEDITOR.instances.editDesc.setData(desc);
                $('#tagId').val(task.id);
                $('#editTitle').val(task.title);
                $('#editDesc').val(task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger("change");
                $('#editStatus').val(task.status);

                var tagsIds = [];
                var userIds = [];
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id);
                });
                $(task.task_assignee).each(function (i, e) {
                    userIds.push(e.id);
                });
                $("#editTagIds").val(tagsIds).trigger('change');

                $("#editAssignee").val(userIds).trigger('change');
                $("#editPriority").val(task.priority).trigger('change');
                $('#EditModal').modal('show');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        }
    });
});

// open delete confirmation model
$(document).on('click', '.delete-btn', function (event) {
    let id = $(event.currentTarget).data('id');
    deleteItem(taskUrl + id, '#task_table', 'Task');
});

// open detail confirmation model
$(document).on('click', '.taskDetails', function (event) {
    let id = $(event.currentTarget).data('id');
    startLoader()
    $('#no-record-info-msg').hide();
    $('#taskDetailsTable').hide();
    $.ajax({
        url: taskDetailUrl + '/' + id,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let data = result.data;
                 stopLoader();
                if (data.totalDuration === 0) {
                    $('#no-record-info-msg').show();
                    return true;
                } else {
                    $('#taskDetailsTable').show();
                }
                var table = $("#taskDetailsTable tbody").html("");
                $.each(data.time_entries, function (idx, elem) {
                    table.append(
                        "<tr>" +
                        "<td id='tdCollapse" + elem.id + "' onclick='manageCollapseIcon(" + JSON.stringify(elem.id) + ")' class='clickable' data-toggle='collapse' data-target='#collapse" + elem.id + "' aria-expanded='false' aria-controls='collapse" + elem.id + "'>" +
                        "<a title='Expand' class='btn btn-success action-btn btn-sm'><span class='fa fa-plus-circle action-icon'></span></a></td>" +
                        "<td>" + elem.user.name + "</td>" +
                        "<td>" + elem.start_time + "</td>" +
                        "<td>" + elem.end_time + "</td>" +
                        "<td>" + elem.duration + "</td>" +
                        "<td><a title='Edit' class='btn action-btn btn-primary btn-sm mr-1' onclick='renderTimeEntry(" + elem.id + ")' ><i class='cui-pencil action-icon'></i></a>" +
                        "<a title='Delete' class='btn action-btn btn-danger btn-sm'  onclick='deleteTimeEntry(" + elem.id + ")'><i class='cui-trash action-icon'></i></a></td>" +
                        "</tr>"
                    );
                    table.append("<tr id='collapse" + elem.id + "' class='collapse'><td colspan='6'><div class='pull-left'>" +
                        "<b>Notes: </b><pre>" + elem.note + "</pre>" +
                        "</div></td></tr>");
                });
                table.append("<tr><td colspan='6'><b>Total Duration : " + data.totalDuration + "</b></td></tr>");
            }
        }
    });
});

$('#addNewForm').submit(function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find("#btnSave");
    loadingButton.button('loading');

    let formdata = $(this).serialize();
    let desc = CKEDITOR.instances.description.getData();
    formdata = formdata.replace("description=", "description="+desc);
    $.ajax({
        url: createTaskUrl,
        type: 'POST',
        data: formdata,
        success: function (result) {
            if (result.success) {
                $('#AddModal').modal('hide');
                $('#task_table').DataTable().ajax.reload();
            }
        },
        error: function (result) {
            printErrorMessage("#validationErrorsBox", result);
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#editForm').submit(function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    let id = $('#tagId').val();
    let formdata = $(this).serializeArray();
    let desc = CKEDITOR.instances.editDesc.getData();
    $.each(formdata, function (i, val) {
        if(val.name == 'description'){
            formdata[i].value = desc;
        }
    });
    $.ajax({
        url: taskUrl + id + '/update',
        type: 'post',
        data: formdata,
        success: function (result) {
            if (result.success) {
                $('#EditModal').modal('hide');
                $('#task_table').DataTable().ajax.reload();
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#AddModal').on('hidden.bs.modal', function () {
    CKEDITOR.instances.description.setData('');
    $('#projectId').val(null).trigger("change");
    $('#assignee').val(null).trigger("change");
    $('#tagIds').val(null).trigger("change");
    $('#priority').val(null).trigger("change");
    resetModalForm('#addNewForm', '#validationErrorsBox');
});

$('#EditModal').on('hidden.bs.modal', function () {
    CKEDITOR.instances.editDesc.setData('');
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$(function () {

    $(document).ajaxComplete(function () {
        $("input[class=enabled]").on('ifChanged', function (e) {
            var taskId = ($(this).attr("data-check"));
            manageCheckbox(this);
            updateTaskStatus(taskId);
        });
    });

    function updateTaskStatus(id) {
        $.ajax({
            url: taskUrl + id + '/update-status',
            type: 'POST',
            cache: false,
            success: function (result) {
                if (result.success) {
                    $('#task_table').DataTable().ajax.reload(null, false);
                }
            }
        });
    }
});

window.manageCollapseIcon = function (id) {
    var isExpanded = $('#tdCollapse' + id).attr('aria-expanded');
    if (isExpanded == 'true') {
        $('#tdCollapse' + id).find('a span').removeClass('fa-minus-circle');
        $('#tdCollapse' + id).find('a span').addClass("fa-plus-circle");
    } else {
        $('#tdCollapse' + id).find('a span').removeClass('fa-plus-circle');
        $('#tdCollapse' + id).find('a span').addClass("fa-minus-circle");
    }
};

window.deleteTimeEntry = function (timeEntryId) {
    let url = timeEntryUrl + timeEntryId;
    swal({
            title: "Delete !",
            text: "Are you sure you want to delete this Time Entry?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes'
        },
        function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        $(".close").trigger('click');
                    }
                    swal({
                        title: 'Deleted!',
                        text: 'Time Entry has been deleted.',
                        type: 'success',
                        timer: 2000
                    });
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        timer: 5000
                    });
                }
            });
        });
};

function setTaskDrp(id) {
    $('#taskId').val(id).trigger("change");
    $("#taskId").prop("disabled", true);
}

$(document).on('click', '.entry-model', function (event) {
    let taskId = $(event.currentTarget).data('id');
    let projectId = $(event.currentTarget).data('project-id');
    $('#timeProjectId').val(projectId).trigger("change");
    getTasksByproject(projectId, '#taskId', taskId, '#tmValidationErrorsBox');
});

CKEDITOR.replace( 'description', {
    language: 'en',
    height: '150px',
});

CKEDITOR.replace( 'editDesc', {
    language: 'en',
    height: '150px',
});
