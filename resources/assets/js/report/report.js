const clientDropDown = $('#client');
clientDropDown.select2({
    width: '100%',
    placeholder: "Select Client",
}).prepend($('<option>', {value: 0, text: 'None'}));

$('#projectIds').select2({
    width: '100%',
    placeholder: "Select Projects"
});

$('#userIds').select2({
    width: '100%',
    placeholder: "Select Users"
});

$('#tagIds').select2({
    width: '100%',
    placeholder: "Select Tags"
});

$('#start_date').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: true,
    icons: {
        up: "icon-angle-up",
        down: "icon-angle-down",
        next: "icon-angle-right",
        previous: "icon-angle-left"
    },
    sideBySide: true
});

$('#end_date').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: false,
    icons: {
        up: "icon-angle-up",
        down: "icon-angle-down",
        next: "icon-angle-right",
        previous: "icon-angle-left"
    },
    sideBySide: true
});

$("#start_date").on("dp.change", function (e) {
    $('#end_date').data("DateTimePicker").minDate(e.date);
});

clientDropDown.on('change', function () {
    $("#projectIds").empty();
    if ($(this).val() != 0) {
        $("#projectIds").val(null).trigger("change");
    }
    loadProjects($(this).val());
});

function loadProjects(clientId) {
    clientId  = (clientId == 0) ? '' : clientId;
    let url = clientProjects+ '?client_id='+clientId;
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            const projects = result.data;
            for (const key in projects) {
                if (projects.hasOwnProperty(key)) {
                    $('#projectIds').append($('<option>', {value: key, text: projects[key]}));
                }
            }
        }
    });
}

$("#projectIds").on('change', function () {
    $("#userIds").empty();
    $("#userIds").val(null).trigger("change");
    loadUsers($(this).val().toString());
});

function loadUsers(projectIds) {
    let url = projectUsers + '?projectIds='+projectIds;
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            const users = result.data;
            for (const key in users) {
                if (users.hasOwnProperty(key)) {
                    $('#userIds').append($('<option>', {value: key, text: users[key]}));
                }
            }
        }
    });
}
// open delete confirmation model
$(document).on('click', '.delete-btn', function (event) {
    let reportId = $(event.currentTarget).data('id');
    deleteReport(reportUrl + reportId);
});
window.deleteReport = function (url) {
    swal({
        title: "Delete !",
        text: "Are you sure you want to delete this report?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#5cb85c',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No',
        confirmButtonText: 'Yes'
    }, function () {
        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            success: function success(obj) {
                if (obj.success) {
                    location.href = reportUrl;
                }

                swal({
                    title: 'Deleted!',
                    text: 'Report has been deleted.',
                    type: 'success',
                    timer: 2000
                });
            },
            error: function error(data) {
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
