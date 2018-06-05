<?php

if (!function_exists('artify_path')) {
    function artify_path($path = null)
    {
        return base_path('vendor/secmohammed/artify/src').DIRECTORY_SEPARATOR.ltrim($path, '/');
    }
}
