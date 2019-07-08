$('#client').select2({
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

$("#client").on('change', function () {
    $("#projectIds").empty();
    loadProjects($(this).val());
});

function loadProjects(clientId) {
    let url = clientsUrl + clientId + '/projects'
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
    loadUsers($(this).val().toString());
});

function loadUsers(projectIds) {
    if (projectIds === '') {
        projectIds = 0;
    }
    let url = projectsUrl + projectIds + '/users'
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
