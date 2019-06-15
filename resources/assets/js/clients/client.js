$('#clients_table').DataTable({
    processing: true,
    serverSide: true,
    "order": [[0, "asc"]],
    ajax: {
        url: clientUrl,
    },
    columnDefs: [
        {
            "targets": [3],
            "orderable": false,
            "className": 'text-center',
            "width": "5%"
        },
        {
            "targets": [2],
            "width": "12%"
        },
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
            data: function (row) {
                if (row.website != null) {
                    return '<a href="http://' + row.website + '" target="_blank" >' + row.website + '</a>'
                } else {
                    return null;
                }
            }, name: 'website'
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' + row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn"  data-id="' + row.id + '">' +
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
        url: clientCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#AddModal').modal('hide');
                $('#clients_table').DataTable().ajax.reload(null, false);
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
    var id = $('#clientId').val();
    $.ajax({
        url: clientUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#EditModal').modal('hide');
                $('#clients_table').DataTable().ajax.reload(null, false);
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
        url: clientUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let client = result.data;
                $('#clientId').val(client.id);
                $('#edit_name').val(client.name);
                $('#edit_email').val(client.email);
                $('#edit_website').val(client.website);
                $('#EditModal').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        }
    });
};
$(document).on('click', '.edit-btn', function (event) {
    let clientId = $(event.currentTarget).data('id');
    renderData(clientId)
});

$(document).on('click', '.delete-btn', function (event) {
    let clientId = $(event.currentTarget).data('id');
    deleteItem(clientUrl + clientId, '#clients_table', 'Client');
});
