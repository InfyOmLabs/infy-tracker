<?php

/**
 * @return int
 */
function getLoggedInUserId()
{
    return Auth::id();
}

/**
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
function getLoggedInUser()
{
    return Auth::user();
}

/**
 * @param string $str
 * @param string $delimiter
 *
 * @return array
 */
function explode_trim_remove_empty_values_from_array($str, $delimiter = ',')
{
    $arr = explode($delimiter, trim($str));
    $arr = array_map('trim', $arr);
    $arr = array_filter($arr, function ($value) {
        return strlen($value);
    });

    return array_values($arr);
}

function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function roundToQuarterHour($totalMinutes)
{
    $hours = intval($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    if ($hours > 0) {
        printf('%02d:%02d h', $hours, $minutes);
    } else {
        printf('%02d:%02d m', $hours, $minutes);
    }
}

function getColor($opacity = 1, $colorCode = null)
{
    if (empty($colorCode)) {
        $colorCode = getColorCode();
    }

    return 'rgba(' . $colorCode . ', ' . $opacity . ')';
}

function getColorCode()
{
    return rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(1, 255);
}

/**
 * return random color.
 *
 * @param int $userId
 *
 * @return string
 */
function getRandomColor($userId)
{
    $colors = ['329af0', 'fc6369', 'ffaa2e', '42c9af', '7d68f0'];
    $index = $userId % 5;

    return $colors[$index];
}

/**
 * return avatar url.
 *
 * @return string
 */
function getAvatarUrl()
{
    return 'https://ui-avatars.com/api/';
}

/**
 * return avatar full url.
 *
 * @param int $userId
 * @param string $name
 *
 * @return string
 */
function getUserImageInitial($userId, $name)
{
    return getAvatarUrl() . "?name=$name&size=30&rounded=true&color=fff&background=" . getRandomColor($userId);
}
