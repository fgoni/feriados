<?php

use Carbon\Carbon;
use Fgoni\Feriados\Feriados;

class FeriadosTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Feriados::hoyEsViernes
     */
    public function testSiEsViernes()
    {
        $feriados = new Feriados();
        $nextFriday = new Carbon('next friday');
        $feriados->setDay($nextFriday);

        $this->assertEquals(true, $feriados->hoyEsViernes());
    }

    public function testSiEsFeriado()
    {
        $feriados = new Feriados();
        $nextFeriado = $feriados->proximoFeriado();
        $feriados->setDay($nextFeriado->instancia);

        $this->assertEquals(true, $feriados->esFeriado());
    }
}
