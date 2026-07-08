<?php

if (!function_exists('maskPassword')) {
    function maskPassword($string)
    {
        $length = strlen($string);
        if ($length <= 2) {
            return str_repeat('*', $length);
        }
        return $string[0] . str_repeat('*', $length - 2) . $string[$length - 1];
    }
}