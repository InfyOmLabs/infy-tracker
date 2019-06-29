$('#tmActivityId,#tmTaskId,#tmProjectId').select2({
    width: '100%',
});

loadProjects();
let isClockRunning = localStorage.getItem('clockRunning');
$(window).on("load", function () {
    if (isClockRunning == null) {
        getUserLastTaskWork();
    }
});

window.startWatch = function () {
    $("#startTimer").hide();
    $("#stopTimer").show();

    var stTime = (localStorage.getItem('start_time') !== null) ? localStorage.getItem('start_time') : getCurrentTime();
    var d1 = new Date($.now());
    var d2 = new Date(moment(stTime).format("YYYY-MM-DD HH:mm:ss"));
    var diffMs = parseInt(d1 - d2);
    hours = parseInt((diffMs / (1000 * 60 * 60)) % 24);
    minutes = parseInt((diffMs / (1000 * 60)) % 60);
    seconds = parseInt((diffMs / 1000) % 60);

    gethours = (hours < 10) ? ('0' + hours + ': ') : (hours + ': ');
    mins = (minutes < 10) ? ('0' + minutes + ': ') : (minutes + ': ');
    secs = (seconds < 10) ? ('0' + seconds) : (seconds);

    // display the stopwatch
    $('#timer').html('<h3><b>' + gethours + mins + secs + '</b></h3>');
    seconds++;

    setItemToLocalStorage({'seconds': seconds, 'minutes': minutes, 'hours': hours});
    clearTime = setTimeout("startWatch( )", 1000);
};

window.stopWatch = function () {
    clear = setTimeout("stopWatch( )", 1000);
};

var isOpen = 0;

$('#imgTimer').click(() => {
    if (isOpen == 0) {
        $('#timeTracker').show();
        $('.img-stopwatch').attr('src', closeWatchImg);
        isOpen = 1;
    } else {
        $('#timeTracker').hide();
        $('.img-stopwatch').attr('src', stopWatchImg);
        isOpen = 0;
    }
    $('#validationErrorsBox').hide();
});

// if timer is running then set values as it is
if (localStorage.getItem('clockRunning') !== null) {
    startWatch();
}

$('#drpUsers,#drpActivity,#drpTasks').select2({
    width: '100%',
});

var clear;

// initialize your variables outside the function
var clearTime;
var count, seconds = 0, minutes = 0, hours = 0;
var secs, mins, gethours;
var entryStartTime, entryStopTime = 0;

$("#startTimer").click(function (e) {
    var activity = $('#tmActivityId').val();
    var task = $('#tmTaskId').val();
    var project = $('#tmProjectId').val();

    if (project != '' && activity != '' && task != '') {
        e.preventDefault();
        $('#tmActivityId').attr('disabled', true);
        $('#tmTaskId').attr('disabled', true);
        $('#tmProjectId').attr('disabled', true);

        var setItems = {
            'user_id': loginUserId,
            'activity_id': activity,
            'task_id': task,
            'project_id': project,
            'clockRunning': true
        };
        setItemToLocalStorage(setItems);

        entryStartTime = getCurrentTime();
        if (localStorage.getItem('start_time') !== null) {
            entryStartTime = localStorage.getItem('start_time');
        } else {
            localStorage.setItem('start_time', entryStartTime);
        }
        startWatch();
    }
});

$("#stopTimer").click(function (e) {
    e.preventDefault();

    $('#tmActivityId').removeAttr('disabled');
    $('#tmTaskId').removeAttr('disabled');
    $('#tmProjectId').removeAttr('disabled');
    $('#tmNotesErr').html("");

    $('#loader').show();
    stopTime();
    storeTimeEntry();
});

//create a function to start the stop watch
function startTime() {
    /* check if seconds, minutes, and hours are equal to zero and start the stop watch */
    if (seconds == 0 && minutes == 0 && hours == 0) {
        startWatch();
    }
}

function stopTime() {
    seconds = minutes = hours = 0;
}

function storeTimeEntry() {
    let startTime = localStorage.getItem('start_time');
    let endTime = getCurrentTime();

    $.ajax({
        url: storeTimeEntriesUrl,
        type: 'POST',
        data: $('#timeTrackerForm').serialize() + '&start_time=' + startTime + '&end_time=' + endTime,
        success: function (result) {
            if (result.success) {
                $('#loader').hide();
                swal({
                    "title": "Success",
                    "text": "Time Entry stored successfully!",
                    "type": "success"
                });
                stopWatch();
                $("#stopTimer").hide();
                $("#timer").html('<h3><b>00:00:00</b></h3>');
                $("#startTimer").show();
                clearTimeout(clearTime);

                var removeItems = ['user_id', 'activity_id', 'task_id', 'clockRunning', 'start_time', 'seconds', 'minutes', 'hours'];
                removeItemsFromLocalStorage(removeItems);
                location.reload();
            }
        },
        error: function (result) {
            printErrorMessage("#timeTrackerValidationErrorsBox", result);
            $('#tmActivityId').attr('disabled', true);
            $('#tmTaskId').attr('disabled', true);
            $('#tmProjectId').attr('disabled', true);
        },
        complete: function () {
        }
    });
}

function getCurrentTime(datetime = null) {
    var dt = (datetime === null) ? new Date($.now()) : new Date(datetime);
    var date = (dt.getDate() < 10) ? '0' + dt.getDate() : dt.getDate();
    var month = ((dt.getMonth() + 1) < 10) ? ('0' + (dt.getMonth() + 1)) : (dt.getMonth() + 1);
    var hours = (dt.getHours() < 10) ? '0' + dt.getHours() : dt.getHours();
    var minutes = (dt.getMinutes() < 10) ? '0' + dt.getMinutes() : dt.getMinutes();
    var seconds = (dt.getSeconds() < 10) ? '0' + dt.getSeconds() : dt.getSeconds();

    return dt.getFullYear() + '-' + month + '-' + date + ' ' + hours + ':' + minutes + ':' + seconds;
}

function setItemToLocalStorage(items) {
    $.each(items, function (key, value) {
        localStorage.setItem(key, value);
    });
}

function removeItemsFromLocalStorage(items) {
    $.each(items, function (index, value) {
        localStorage.removeItem(value);
    });
}

$("#tmProjectId").on('change', function (e) {
    e.preventDefault();
    $("#tmTaskId").attr('disabled', true);

    var projectId = $('#tmProjectId').val();
    loadTimerData(projectId);
});

function loadTimerData(projectId) {
    $.ajax({
        url: myTasksUrl + '?project_id=' + projectId,
        type: 'GET',
        success: function (result) {
            $('#tmTaskId').find('option').remove().end().append('<option value="">Select Task</option>');
            $('#tmTaskId').val("").trigger('change');

            $(result.tasks).each(function (i, e) {
                $("#tmTaskId").append($('<option></option>').attr('value', e.id).text(e.title));
            });

            $('#tmActivityId').find('option').remove().end().append('<option value="">Select Activity</option>');
            $('#tmActivityId').val("").trigger('change');
            $(result.activities).each(function (i, e) {
                $("#tmActivityId").append($('<option></option>').attr('value', e.id).text(e.name));
            });

            $("#tmTaskId").removeAttr('disabled');
            // if timer is running then set values as it is
            if (localStorage.getItem('clockRunning') !== null) {
                $('#tmActivityId').val(localStorage.getItem('activity_id')).trigger("change");
                $('#tmTaskId').val(localStorage.getItem('task_id')).trigger("change");

                $('#tmTaskId').attr('disabled', true);
                $('#tmActivityId').attr('disabled', true);
            } else {
                $('#tmActivityId').val(localStorage.getItem('activity_id')).trigger("change");
                $('#tmTaskId').val(localStorage.getItem('task_id')).trigger("change");
            }
        }
    });
}

function loadProjects() {
    $.ajax({
        url: myProjectsUrl,
        type: 'GET',
        success: function (result) {
            $(result.data).each(function (i, e) {
                $("#tmProjectId").append($('<option></option>').attr('value', e.id).text(e.name));
            });
            if (localStorage.getItem('clockRunning') !== null) {
                $('#tmProjectId').val(localStorage.getItem('project_id')).trigger("change");
                $('#tmProjectId').attr('disabled', true);
            }
        }
    });
}

function getUserLastTaskWork() {
    $.ajax({
        url: lastTaskWorkUrl,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                if (result.data) {
                    let lastTask = result.data;
                    if (isClockRunning == null) {
                        let setItems = {
                            'user_id': loginUserId,
                            'activity_id': lastTask.activity_id,
                            'task_id': lastTask.task_id,
                            'project_id': lastTask.project_id
                        };
                        setItemToLocalStorage(setItems);
                        $('#tmProjectId').val(lastTask.project_id).trigger("change");
                    }
                }
            }
        }
    });
}
