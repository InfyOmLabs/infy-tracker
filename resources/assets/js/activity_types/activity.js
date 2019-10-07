let tableId = '#activity_type'
$(tableId).DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: activityUrl,
    },
    columnDefs: [
        {
            'targets': [1],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
    ],
    'fnInitComplete': function () {
    },
    columns: [
        {
            data: 'name', name: 'name',
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon" ></i></a>'
            }, name: 'id',
        },
    ],
})

$('#addNewForm').submit(function (event) {
    event.preventDefault()
    var loadingButton = jQuery(this).find('#btnSave')
    loadingButton.button('loading')
    $.ajax({
        url: activityCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#AddModal').modal('hide')
                $('#activity_type').DataTable().ajax.reload(null, false)
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
    var loadingButton = jQuery(this).find('#btnEditSave')
    loadingButton.button('loading')
    var id = $('#activityTypeId').val()
    $.ajax({
        url: activityUrl + id,
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#EditModal').modal('hide')
                $('#activity_type').DataTable().ajax.reload(null, false)
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#AddModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox')
})

$('#EditModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox')
})

window.renderData = function (id) {
    $.ajax({
        url: activityUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#activityTypeId').val(result.data.id)
                $('#activityType').val(result.data.name)
                $('#EditModal').modal('show')
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
    })
}

$(document).on('click', '.edit-btn', function (event) {
    let activityId = $(event.currentTarget).data('id')
    renderData(activityId)
})

$(document).on('click', '.delete-btn', function (event) {
    let activityId = $(event.currentTarget).data('id')
    deleteItem(activityUrl + activityId, '#activity_type', 'Activity')
})
