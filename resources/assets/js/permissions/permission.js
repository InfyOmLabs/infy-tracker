let tableName = '#permission_table'
$(tableName).DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: permissionUrl,
    },
    columnDefs: [
        {
            'targets': [3],
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
            data: 'display_name',
            name: 'display_name',
        },
        {
            data: 'description',
            name: 'description',
        },
        {
            data: function (row) {
                return '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>'
            }, name: 'id',
        },
    ],
})

$(document).on('click', '.delete-btn', function (event) {
    let permissionId = $(event.currentTarget).data('id')
    deleteItem(permissionUrl + permissionId, tableName, 'Permission')
})
