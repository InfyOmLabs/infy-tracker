$('#client_id,#edit_client_id').select2({
    width: '100%',
    placeholder: 'Select Client',
})

$('#filterClient').select2()

let tbl = $('#projects_table').DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: projectUrl,
        data: function (data) {
            data.filter_client = $('#filterClient').
                find('option:selected').
                val()
        },
    },
    columnDefs: [
        {
            'targets': [0],
            'className': 'text-center',
            'width': '7%',
        },
        {
            'targets': [3],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
    ],
    columns: [
        {
            data: 'prefix',
            name: 'prefix',
        },
        {
            data: 'name',
            name: 'name',
        },
        {
            data: 'client.name',
            defaultContent: '',
            name: 'client.name',
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
    'fnInitComplete': function () {
        $('#filterClient').change(function () {
            tbl.ajax.reload()
        })
    },
})

$('#addNewForm').submit(function (event) {
    event.preventDefault()
    var loadingButton = jQuery(this).find('#btnSave')
    loadingButton.button('loading')
    $.ajax({
        url: projectCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#AddModal').modal('hide')
                $('#projects_table').DataTable().ajax.reload(null, false)
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
    var loadingButton = jQuery(this).find('#btnEditSave')
    loadingButton.button('loading')
    var id = $('#projectId').val()
    $.ajax({
        url: projectUrl + id,
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#EditModal').modal('hide')
                $('#projects_table').DataTable().ajax.reload(null, false)
                revokerTracker()
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
    $('#client_id').val(null).trigger('change')
    $('#user_ids').val(null).trigger('change')
    resetModalForm('#addNewForm', '#validationErrorsBox')
})

$('#EditModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox')
})

window.renderData = function (id) {
    $.ajax({
        url: projectUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let project = result.data.project
                $('#projectId').val(project.id)
                $('#edit_name').val(project.name)
                $('#edit_prefix').val(project.prefix)
                $('#edit_client_id').val(project.client_id).trigger('change')
                $('#edit_description').val(project.description)
                var valArr = result.data.users
                $('#edit_user_ids').val(valArr)
                $('#edit_user_ids').trigger('change')
                $('#EditModal').modal('show')
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
    })
}

$(document).on('click', '.edit-btn', function (event) {
    let projectId = $(event.currentTarget).data('id')
    renderData(projectId)

})

$(document).on('click', '.delete-btn', function (event) {
    let projectId = $(event.currentTarget).data('id')
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">Are you sure want to delete this project?</strong><div class="swal__text-message">By deleting this project all its task and time entries will be deleted.</div></div>'

    deleteItemInputConfirmation(projectUrl + projectId, '#projects_table',
        'Project', alertMessage)
    setTimeout(function () {
        revokerTracker()
    }, 1000)
})

$('#user_ids,#edit_user_ids').select2({
    width: '100%',
    placeholder: 'Select Users',
})
