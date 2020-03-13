<?php

if (!function_exists('artify_path')) {
    function artify_path($path = null)
    {
        return dirname(__DIR__,1) . DIRECTORY_SEPARATOR . ltrim($path, '/');
    }
}
