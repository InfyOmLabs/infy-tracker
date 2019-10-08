require('@coreui/coreui')
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
})
$(document).ajaxError(function (event, xhr, settings) {
    if (xhr.status == 401) {
        location.replace(loginUrl)
    }
})
$.extend($.fn.dataTable.defaults, {
    'paging': true,
    'info': true,
    'ordering': true,
    'autoWidth': false,
    'pageLength': 10,
    'language': {
        'search': '',
        'sSearch': 'Search',
        'sProcessing': getSpinner(),
    },
    'preDrawCallback': function () {
        customSearch()
    },
})

function customSearch () {
    $('.dataTables_filter input').addClass('form-control')
    $('.dataTables_filter input').attr('placeholder', 'Search')
}

function getSpinner () {
    return '<div id="infyLoader" class="infy-loader">\n' +
        '    <svg width="150px" height="75px" viewBox="0 0 187.3 93.7" preserveAspectRatio="xMidYMid meet"\n' +
        '         style="left: 50%; top: 50%; position: absolute; transform: translate(-50%, -50%) matrix(1, 0, 0, 1, 0, 0);">\n' +
        '        <path stroke="#00c6ff" id="outline" fill="none" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"\n' +
        '              stroke-miterlimit="10"\n' +
        '              d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 \t\t\t\tc-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>\n' +
        '        <path id="outline-bg" opacity="0.05" fill="none" stroke="#f5981c" stroke-width="5" stroke-linecap="round"\n' +
        '              stroke-linejoin="round" stroke-miterlimit="10"\n' +
        '              d="\t\t\t\tM93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 \t\t\t\tc-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>\n' +
        '    </svg>\n' +
        '</div>'
}

$(document).on('click', '.btn-task-delete', function (event) {
    let taskId = $(event.currentTarget).data('task-id')
    deleteItem('tasks/' + taskId, '#task_table', 'Task')
    setTimeout(function () {
        revokerTracker()
    }, 1000)
})

function deleteItemAjax (url, tableId, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                $(tableId).DataTable().ajax.reload(null, false)
            }
            swal({
                title: 'Deleted!',
                text: header + ' has been deleted.',
                type: 'success',
                timer: 2000,
            })
            if (callFunction) {
                eval(callFunction)
            }
        },
        error: function (data) {
            swal({
                title: '',
                text: data.responseJSON.message,
                type: 'error',
                timer: 5000,
            })
        },
    })
}

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: 'Delete !',
            text: 'Are you sure you want to delete this "' + header + '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
        },
        function () {
            deleteItemAjax(url, tableId, header, callFunction = null)
        })
}

window.deleteItemInputConfirmation = function (
    url, tableId, header, alertMessage, callFunction = null) {
    swal({
            type: 'input',
            inputPlaceholder: 'Please type "delete" to delete this ' + header +
                '.',
            title: 'Delete !',
            text: alertMessage,
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
            imageUrl: baseUrl + 'images/warning.png',
        },
        function (inputVal) {
            if (inputVal === false) {
                return false
            }
            if (inputVal == '' || inputVal.toLowerCase() != 'delete') {
                swal.showInputError(
                    'Please type "delete" to delete this client.')
                $('.sa-input-error').css('top', '23px!important')
                return false
            }
            if (inputVal.toLowerCase() === 'delete') {
                deleteItemAjax(url, tableId, header, callFunction = null)
            }
        })
}

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html('')
    $(selector).text(errorResult.responseJSON.message)
}

window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset()
    $(validationBox).hide()
}

window.manageCheckbox = function (input) {
    if (input.id == 'enabled') {
        $(input).attr('name', 'no')
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-white',
            insert: '<div class="icheck_line-icon"></div>',
        })
    } else {
        $(input).attr('name', 'yes')
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-green',
            insert: '<div class="icheck_line-icon"></div>',
        })
    }
}
window.onload = function () {
    window.startLoader = function () {
        $('.infy-loader').show()
    }

    window.stopLoader = function () {
        $('.infy-loader').hide()
    }

// infy loader js
    stopLoader()
}

window.format = function (dateTime, format = 'DD-MMM-YYYY') {
    return moment(dateTime).format(format)
}

window.manageAjaxErrors = function (
    data, errorDivId = 'editValidationErrorsBox') {
    if (data.status == 404) {
        $.toast({
            heading: 'Error',
            text: data.responseJSON.message,
            showHideTransition: 'fade',
            icon: 'error',
            position: 'top-right',
        })
    } else {
        printErrorMessage('#' + errorDivId, data)
    }
}
$(document).on('keydown', function (e) {
    if (e.keyCode === 27) {
        $('.modal').modal('hide')
    }
})
window.displaySuccessMessage = function (message) {
    $.toast({
        heading: 'Success',
        text: message,
        showHideTransition: 'slide',
        icon: 'success',
        position: 'top-right',
    })
}

$(function () {
    $('.dataTables_length').css('padding-top', '6px')
    $('.dataTables_info').css('padding-top', '24px')
})

$.extend($.fn.dataTable.defaults, {
    drawCallback: function (settings) {
        let thisTableId = settings.sTableId
        if (settings.fnRecordsDisplay() > settings._iDisplayLength) {
            $('#' + thisTableId + '_paginate').show()
        } else {
            $('#' + thisTableId + '_paginate').hide()
        }
    },
})

//focus on select2
$(document).
    on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).
            closest('.select2-container').
            siblings('select:enabled').
            select2('open')
    })

$(function () {
    $('.modal').on('shown.bs.modal', function () {
        if ($(this).find('.timeEntryAddForm').hasClass('timeEntryAddForm') ||
            $(this).find('.editTimeEntryForm').hasClass('editTimeEntryForm')) {
            $(this).find('textarea').first().focus()
        } else {
            $(this).find('input:text').first().focus()
        }
    })
})

window.roundToQuarterHourAll = function (minuts) {
    var hours = Math.floor(minuts / 60)
    var minutes = minuts % 60
    if (hours > 0) {
        return pad(hours) + ':' + pad(minutes) + ' h'
    } else {
        return pad(hours) + ':' + pad(minutes) + ' m'
    }
}

window.pad = function (d) {
    return (d < 10) ? '0' + d : d
}

window.nl2br = function (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return ''
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined')
        ? '<br />'
        : '<br>'
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
        '$1' + breakTag + '$2')
}

window.getItemFromLocalStorage = function (item) {
    return localStorage.getItem(item + '_' + loggedInUserId)
}
