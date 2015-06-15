<?php

if (!function_exists('db_language')) {
    function db_language($constantName = null)
    {
        if (trim($constantName) !== '') {
            return app('db.language')->get($constantName);
        }
        return app('db.language');
    }
}