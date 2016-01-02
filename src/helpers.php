<?php

if (!function_exists('db_language')) {
    function db_language($constantName = null, $constantValue = null)
    {
        if (trim($constantName) !== '' && $constantValue !== '') { // Only for default language
            return app('db.language')->getAndSetForFirstLanguage($constantName, $constantValue);
        }
        if (trim($constantName) !== '') {
            return app('db.language')->get($constantName);
        }
        return app('db.language');
    }
}
