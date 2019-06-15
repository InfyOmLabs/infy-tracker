$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.extend($.fn.dataTable.defaults, {
    "paging": true,
    "info": true,
    "ordering": true,
    "autoWidth": false,
    "pageLength": 10,
    "language": {
        "search": "",
        "sSearch": "Search",
        "sProcessing": getSpinner()
    },
    "preDrawCallback": function () {
        customSearch()
    }
});

function customSearch() {
    $('.dataTables_filter input').addClass("form-control");
    $('.dataTables_filter input').attr("placeholder", "Search");
}

function getSpinner() {
    return '<div class="spinner">\n' +
        '  <div class="double-bounce1"></div>\n' +
        '  <div class="double-bounce2"></div>\n' +
        '</div>';
}

$(document).on('click', '.btn-task-delete', function (event) {
    var taskId = $(event.currentTarget).data('task-id');
    deleteItem('tasks/' + taskId, '#task_table', 'Task');
});

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: "Delete !",
            text: "Are you sure you want to delete this " + header + "?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes'
        },
        function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        $(tableId).DataTable().ajax.reload(null, false);
                    }
                    swal({
                        title: 'Deleted!',
                        text: header + ' has been deleted.',
                        type: 'success',
                        timer: 2000
                    });
                    if (callFunction) {
                        eval(callFunction);
                    }
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        timer: 5000
                    });
                }
            });
        });
};

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html("");
    $(selector).text(errorResult.responseJSON.message);
};

window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset();
    $(validationBox).hide();
};

window.manageCheckbox = function (input) {
    if (input.id == "enabled") {
        $(input).attr('name', 'no');
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-white',
            insert: '<div class="icheck_line-icon"></div>'
        });
    } else {
        $(input).attr('name', 'yes');
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-green',
            insert: '<div class="icheck_line-icon"></div>'
        });
    }
};
window.onload = function () {
    window.startLoader = function () {
        $('#infyLoader').show();
    }

    window.stopLoader = function () {
        $('#infyLoader').hide();
    }

// infy loader js
    stopLoader();
};

window.format = function (dateTime, format = 'DD-MMMM-YYYY') {
    return moment(dateTime).format(format);
};

window.manageAjaxErrors = function (data, errorDivId = 'editValidationErrorsBox') {
    if (data.status == 404) {
        $.toast({
            heading: 'Error',
            text: data.responseJSON.message,
            showHideTransition: 'fade',
            icon: 'error',
            position: 'top-right',
        });
    } else {
        printErrorMessage("#" + errorDivId, data);
    }
};
