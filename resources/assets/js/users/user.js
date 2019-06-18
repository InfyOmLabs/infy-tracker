$(function () {
    $('#projectId,#editProjectId').select2({
        width: '100%',
        tags: true
    });
});

var tbl = $('#users_table').DataTable({
    processing: true,
    serverSide: true,
    "order": [[0, "desc"]],
    ajax: {
        url: usersUrl,
    },
    columnDefs: [
        {
            "targets": [4],
            "orderable": false,
            "className": 'text-center',
            "width": "5%"
        },
        {
            "targets": [3],
            "orderable": false,
            "className": 'text-center',
            "width": "9%"
        }
    ],
    columns: [
        {
            data: 'name',
            name: 'name'
        },
        {
            data: 'email',
            name: 'email'
        },
        {
            data: 'phone',
            name: 'phone'
        },
        {
            data: function (row) {
                var email_verification = '<button type="button" title="Send Verification Mail" id="email-btn" class="btn action-btn btn-primary btn-sm email-btn" ' +
                    'data-loading-text="<span class=\'spinner-border spinner-border-sm\'></span>" data-id="' + row.id + '">' +
                    '<i class="icon-envelope icons action-icon "></i></button>';
                if(row.is_email_verified){
                    email_verification = '<a title="Email Verified" data-id="' + row.id + '">' +
                        '<i class="cui-circle-check check-icon"></i></a>';
                }
                return email_verification;
            }, name: 'id'
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' + row.id + '">'
                    + '<i class="cui-pencil action-icon"  style="color:#3c8dbc"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' + row.id + '">' +
                    '<i class="cui-trash action-icon text-danger"></i></a>'
            }, name: 'id'
        }
    ],
});

$('#users_table').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
});

window.renderData = function (url) {
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let user = result.data;
                $('#userId').val(user.id);
                $('#edit_name').val(user.name);
                $('#edit_email').val(user.email);
                $('#edit_phone').val(user.phone);
                $('#editProjectId').val(user.project_ids).trigger("change");
                if (user.is_active) {
                    $('#edit_is_active').val(1).prop('checked', true);
                }
                $('#EditModal').modal('show');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
};

window.sendVerificationEmail = function (url) {
    var loadingButton = $("#email-btn");
    loadingButton.button('loading');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                swal("Success!", result.message, "success");
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
};

$(function () {
    // create new user
    $('#addNewForm').submit(function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find("#btnSave");
        loadingButton.button('loading');
        $.ajax({
            url: createUserUrl,
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#AddModal').modal('hide');
                    $('#users_table').DataTable().ajax.reload(null, false);
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

    // update user
    $('#editForm').submit(function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find("#btnEditSave");
        loadingButton.button('loading');
        var id = $('#userId').val();
        $.ajax({
            url: usersUrl + id + '/update',
            type: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#EditModal').modal('hide');
                    $('#users_table').DataTable().ajax.reload(null, false);
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
        resetModalForm('#addNewForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    // open edit user model
    $(document).on('click', '.edit-btn', function (event) {
        let userId = $(event.currentTarget).data('id');
        renderData(usersUrl + userId + '/edit');
    });

    // open delete confirmation model
    $(document).on('click', '.delete-btn', function (event) {
        let userId = $(event.currentTarget).data('id');
        deleteItem(usersUrl + userId, '#users_table', 'User');
    });

    // open email confirmation model
    $(document).on('click', '.email-btn', function (event) {
        let userId = $(event.currentTarget).data('id');
        sendVerificationEmail(usersUrl + 'send-email/' + userId);
    });
});
