<?php

if (!function_exists('db_language')) {
    function db_language()
    {
        return app('db.language');
    }
}