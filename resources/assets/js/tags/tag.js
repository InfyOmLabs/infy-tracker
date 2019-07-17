$('#tags_table').DataTable({
    processing: true,
    serverSide: true,
    "order": [[0, "asc"]],
    ajax: {
        url: tagUrl,
    },
    columnDefs: [
        {
            "targets": [1],
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
        url: tagCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#AddModal').modal('hide');
                $('#tags_table').DataTable().ajax.reload(null, false);
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
    var id = $('#tagId').val();
    $.ajax({
        url: tagUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#EditModal').modal('hide');
                $('#tags_table').DataTable().ajax.reload(null, false);
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
    $('#tagHeader').html('New Tag');
    resetModalForm('#addNewForm', '#validationErrorsBox');
});

$('#EditModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

window.renderData = function (id) {
    $.ajax({
        url: tagUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#tagId').val(result.data.id);
                $('#tagName').val(result.data.name);
                $('#EditModal').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
};

window.setBulkTags = function () {
    $('#isBulkTags').val(true);
    $('#tagHeader').html('Add Bulk Tags');
};

$(document).on('click', '.edit-btn', function (event) {
    let tagId = $(event.currentTarget).data('id');
    renderData(tagId);

});

$(document).on('click', '.delete-btn', function (event) {
    let tagId = $(event.currentTarget).data('id');
    deleteItem(tagUrl + tagId, '#tags_table', 'Tag');
});
