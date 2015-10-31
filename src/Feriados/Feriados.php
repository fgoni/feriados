<?php

/**
 * Created by PhpStorm.
 * User: facundo.goni
 * Date: 26/10/2015
 * Time: 03:03 PM
 */
namespace Fgoni\Feriados;

use Carbon\Carbon;
use stdClass;

class Feriados
{

    protected $json, $hoy, $fechas, $eventos, $feriados, $inamovibles, $trasladables, $noLaborales;

    public function __construct()
    {
        Carbon::setLocale('es');
        setlocale(LC_ALL, 'es_ES');

        $this->fechas = array();
        $this->eventos = array();
        $this->hoy = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
        $this->json = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'feriados.json');
        $this->feriados = json_decode($this->json);
        $this->inamovibles = $this->feriados->feriados_inamovibles;
        $this->trasladables = $this->feriados->feriados_trasladables;
        $this->noLaborales = $this->feriados->dias_no_laborables;
        $this->cargarInamovibles();
        $this->cargarTrasladables();
    }

    public function hoy()
    {
        return $this->hoy;
    }

    public function setDay(Carbon $day)
    {
        return $this->hoy = $day;
    }

    public function esFeriado()
    {
        $cantFechas = sizeof($this->fechas);
        for ($i = 0; $i < $cantFechas; $i++) {
            if ($this->hoy->isSameDay($this->fechas[$i])) {
                return true;
            }
        }
        return false;
    }

    public function faltan()
    {
        $fechaInamovibles = $this->hoy;
        $fechaTrasladables = $this->hoy;
        $eventoInamovibles = null;
        $eventoTrasladables = null;
        $cantInamovibles = sizeof($this->inamovibles);
        $cantTrasladables = sizeof($this->trasladables);

        for ($i = 0; $i < $cantInamovibles; $i++) {
            if ($this->hoy->lte($this->fechas[$i])) {
                $fechaInamovibles = $this->fechas[$i];
                $eventoInamovibles = $this->eventos[$i];
                break;
            }
        }
        for ($i = $cantInamovibles; $i < $cantTrasladables + $cantInamovibles; $i++) {
            if (!$this->hoy->gte($this->fechas[$i])) {
                $fechaTrasladables = $this->fechas[$i];
                $eventoTrasladables = $this->eventos[$i];
                break;
            }
        }

        /***
         * Revisar el código para FERIADOS PUENTE.
         */
        if ($fechaInamovibles) {
            if (!$fechaTrasladables || $fechaInamovibles->lte($fechaTrasladables)) {
                $data = [
                    'diferencia' => $this->hoy->diffInDays($fechaInamovibles),
                    'evento' => $eventoInamovibles,
                    'fecha' => Carbon::instance($fechaInamovibles)->format('d/m/Y'),
                    'diaDeLaSemana' => ucfirst(utf8_encode(Carbon::instance($fechaInamovibles)->formatLocalized('%A'))),
                ];

                if (0 === strpos($eventoInamovibles, 'Feriado puente')) {
                    $data['puente'] = [
                        'diferencia' => $this->hoy->diffInDays($fechaInamovibles),
                        'evento' => $eventoInamovibles,
                        'fecha' => Carbon::instance($fechaInamovibles)->format('d/m/Y'),
                        'instancia' => Carbon::instance($fechaInamovibles),
                        'diaDeLaSemana' => ucfirst(utf8_encode(Carbon::instance($fechaInamovibles)->formatLocalized('%A'))),
                    ];
                }
                return $data;
            }
        }
        if ($fechaTrasladables) {
            return [
                'diferencia' => $this->hoy->diffInDays($fechaTrasladables),
                'evento' => $eventoTrasladables,
                'fecha' => Carbon::instance($fechaTrasladables)->format('d/m/Y'),
                'instancia' => Carbon::instance($fechaTrasladables),
                'diaDeLaSemana' => ucfirst(utf8_encode(Carbon::instance($fechaTrasladables)->formatLocalized('%A'))),
            ];
        }
        return 0;
    }

    private function cargarInamovibles()
    {
        $cantInamovibles = count($this->inamovibles);
        for ($i = 0; $i < $cantInamovibles; $i++) {
            array_push($this->fechas, Carbon::createFromFormat('d/m', $this->inamovibles[$i]->fecha));
            array_push($this->eventos, $this->inamovibles[$i]->evento);
        }
    }

    private function cargarTrasladables()
    {
        $cantTrasladables = count($this->trasladables);
        for ($i = 0; $i < $cantTrasladables; $i++) {
            array_push($this->fechas, Carbon::createFromFormat('d/m', $this->trasladables[$i]->fecha_traslado));
            array_push($this->eventos, $this->trasladables[$i]->evento);
        }
    }


    public function hoyEsViernes()
    {
        return $this->hoy->dayOfWeek === Carbon::FRIDAY;
    }

    public function cuantoFalta()
    {
        return $this->faltan()['diferencia'];
    }

    public function proximoFeriado()
    {
        $array = $this->faltan();
        $proximoFeriado = new stdClass();
        foreach ($array as $key => $value) {
            $proximoFeriado->$key = $value;
        }
        return $proximoFeriado;
    }

    public function inamovibles()
    {
        return $this->inamovibles;
    }

    public function trasladables()
    {
        return $this->trasladables;
    }

    public function noLaborales()
    {
        return $this->noLaborales;
    }

    public function feriados()
    {
        return $this->json;
    }

    public function fechas()
    {
        return $this->fechas;
    }

    public function eventos()
    {
        return $this->eventos;
    }
}
