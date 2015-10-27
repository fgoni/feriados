<?php namespace Fgoni\Feriados;

use Illuminate\Support\ServiceProvider;

class FeriadoServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->singleton('Feriados', function ($app) {
            return new Feriados();
        });
	}

}
