<?php
namespace Fgoni\Feriados\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: facundo.goni
 * Date: 26/10/2015
 * Time: 03:40 PM
 */
class Feriados extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Feriados';
    }
}