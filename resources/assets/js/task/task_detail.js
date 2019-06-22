$(function () {
    $('#editAssignTo').select2({
        width: '100%',
        placeholder: "Select Assignee"
    });
    $('#editProjectId').select2({
        width: '100%',
        placeholder: "Select Project"
    });
    $('#editTagIds,#editAssignee').select2({
        width: '100%',
        tags: true
    });
    $('#editPriority').select2({
        width: '100%',
        placeholder: "Select Priority"
    });

    $('#dueDate,#editDueDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        icons: {
            up: "icon-angle-up",
            down: "icon-angle-down"
        },
        sideBySide: true,
        minDate: new Date()
    });
});

// open edit user model
$(document).on('click', '.edit-btn', function (event) {
    let id = $(event.currentTarget).data('id');
    var loadingButton = jQuery(this);
    loadingButton.button('loading');
    $.ajax({
        url: taskUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                var task = result.data;
                $('#tagId').val(task.id);
                $('#editTitle').val(task.title);
                $('#editDesc').val(task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger("change");
                if (task.status == 1) {
                    $('#editStatus').prop('checked', true);
                }

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
                loadingButton.button('reset');
                $('#EditModal').modal('show');
            }
        }
    });
});

$('#editForm').submit(function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    var id = $('#tagId').val();
    $.ajax({
        url: taskUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                location.reload();
            }
        },
        error: function (result) {
            loadingButton.button('reset');
            printErrorMessage("#editValidationErrorsBox", result);
        }
    });
});

$('#EditModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});
