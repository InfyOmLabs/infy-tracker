<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('task-detail', function ($user) {
    return ($user->can('manage_all_tasks')) ? true : false;
});

Broadcast::channel('stopwatch-event.{loggedInUserId}', function ($user) {
    return true;
});
