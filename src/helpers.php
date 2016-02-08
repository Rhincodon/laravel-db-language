<?php

if (!function_exists('db_language')) {
    function db_language($constantName = null, $constantValue = null)
    {
        if (trim($constantName) !== '' && $constantValue !== '') { // Only for default language
            return app('Rhinodontypicus\DBLanguage\DbLanguage')->getAndSetForFirstLanguage($constantName, $constantValue);
        }

        if (trim($constantName) !== '') {
            return app('Rhinodontypicus\DBLanguage\DbLanguage')->get($constantName);
        }

        return app('Rhinodontypicus\DBLanguage\DbLanguage');
    }
}
