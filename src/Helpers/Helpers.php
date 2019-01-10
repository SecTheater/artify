<?php

if (!function_exists('artify_path')) {
    function artify_path($path = null)
    {
        return base_path('vendor/sectheater/artify/src').DIRECTORY_SEPARATOR.ltrim($path, '/');
    }
}
