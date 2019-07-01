let tableName = '#role_table';
$(tableName).DataTable({
    processing: true,
    serverSide: true,
    "order": [[0, "asc"]],
    ajax: {
        url: roleUrl,
    },
    columnDefs: [
        {
            "targets": [3],
            "orderable": false,
            "className": 'text-center',
            "width": "5%"
        }
    ],
    columns: [
        {
            data: 'name',
            name: 'name'
        },
        {
            data: 'display_name',
            name: 'display_name'
        },
        {
            data: 'description',
            name: 'description'
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' + row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' + row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>'
            }, name: 'id'
        }
    ],
});

$('#addNewForm').submit(function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find("#btnSave");
    loadingButton.button('loading');
    $.ajax({
        url: roleCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#AddModal').modal('hide');
                $(tableName).DataTable().ajax.reload(null, false);
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
    var loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    var id = $('#permissionId').val();
    $.ajax({
        url: roleUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#EditModal').modal('hide');
                $(tableName).DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
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

window.renderData = function (id) {
    $.ajax({
        url: roleUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#permissionId').val(result.data.id);
                $('#editName').val(result.data.name);
                $('#editDisplayName').val(result.data.display_name);
                $('#editDescription').val(result.data.description);
                $('#EditModal').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
};

$(document).on('click', '.edit-btn', function (event) {
    let roleId = $(event.currentTarget).data('id');
    renderData(roleId);

});

$(document).on('click', '.delete-btn', function (event) {
    let roleId = $(event.currentTarget).data('id');
    deleteItem(roleUrl + roleId, tableName, 'Permission');
});
