let tableName = '#role_table'
$(tableName).DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: roleUrl,
    },
    columnDefs: [
        {
            'targets': [2],
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
            data: 'description',
            name: 'description',
        },
        {
            data: function (row) {
                return '<a title="Edit" href="' + roleUrl + row.id +
                    '/edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>'
            }, name: 'id',
        },
    ],
})

$(document).on('click', '.delete-btn', function (event) {
    let roleId = $(event.currentTarget).data('id')
    deleteItem(roleUrl + roleId, tableName, 'Role')
})
