<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Support\Facades\Facade;

class DbLanguageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return DbLanguage::class;
    }
}
