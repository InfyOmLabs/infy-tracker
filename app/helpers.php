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
        return !empty($value);
    });

    return $arr;
}

function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';

}

function roundToQuarterHour($totalMinutes)
{
    $hours = intval($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    if ($hours > 0) {
        printf("%02d:%02d h", $hours, $minutes);
    } else {
        printf("%02d:%02d m", $hours, $minutes);
    }
}

function getChartColors()
{
    return ['#6574cd', '#F66081', '#9561e2', '#ff0052', '#e1c936', '#9e00ff', '#ffef00', '#3f3f3f'];
}

function getBarChartColors()
{
    return ['#6574cd' => '#d8dcf3', '#ff6384' => '#ffccd7', '#36a2eb' => '#b9dff8', '#ffce56' => '#ffe9b3', '#4bc0c0' => '#b4e4e4', '#9966ff' => '#ddccff', '#ff9f40' => '#ffd9b3', '#ffef00' => '#fff899', '#9e00ff' => '#e2b3ff', '#2c3987' =>
        '#b2bae6', '#ff0052' => '#ffb3cb', '#9561e2' => '#e2d4f7', '#f66081' => '#fbb7c5', '#5263c7' => '#c5cbec'];
}