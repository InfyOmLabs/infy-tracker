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