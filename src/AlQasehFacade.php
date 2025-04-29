<?php

namespace Osama\AlQaseh;

use Illuminate\Support\Facades\Facade;

class AlQasehFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alqaseh';
    }
}