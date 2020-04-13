$('#department_id,#edit_department_id').select2({
    width: '100%',
    placeholder: 'Select Department',
});

$('#clients_table').DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: clientUrl,
    },
    columnDefs: [
        {
            'targets': [4],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
    ],
    columns: [
        {
            data: 'name',
            name: 'name',
        },
        {
            data: function (row) {
                return (row.department !== null) ? row.department.name : '';
            },
            name: 'department.name',
        },
        {
            data: 'email',
            name: 'email',
        },
        {
            data: function (row) {
                if (row.website != null) {
                    return '<a href="http://' + row.website +
                        '" target="_blank" >' + row.website + '</a>';
                } else {
                    return null;
                }
            }, name: 'website',
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn"  data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>';
            }, name: 'id',
        },
    ],
});

$('#addNewForm').submit(function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find('#btnSave');
    loadingButton.button('loading');
    $.ajax({
        url: clientCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#AddModal').modal('hide');
                $('#clients_table').DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            printErrorMessage('#validationErrorsBox', result);
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});

$('#editForm').submit(function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find('#btnEditSave');
    loadingButton.button('loading');
    var id = $('#clientId').val();
    $.ajax({
        url: clientUrl + id,
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#EditModal').modal('hide');
                $('#clients_table').DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
        complete: function () {
            loadingButton.button('reset');
        },
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
                $('#edit_department_id').
                    val(client.department_id).
                    trigger('change.select2');
                $('#edit_website').val(client.website);
                $('#EditModal').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
};
$(document).on('click', '.edit-btn', function (event) {
    let clientId = $(event.currentTarget).data('id');
    renderData(clientId);
});

$(document).on('click', '.delete-btn', function (event) {
    let clientId = $(event.currentTarget).data('id');
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">Are you sure want to delete this client?</strong><div class="swal__text-message">By deleting this client all its project, task and time entries will be deleted.</div></div>';

    deleteItemInputConfirmation(clientUrl + clientId, '#clients_table',
        'Client', alertMessage);
});
