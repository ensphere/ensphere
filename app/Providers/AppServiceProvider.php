<?php namespace Ensphere\Ensphere\Providers;

use Illuminate\Support\ServiceProvider;
use Libs\Helper;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom( __DIR__ . '/../../resources/views', 'ensphere.ensphere' );
		if( Helper::isModule() ) {
			$this->publishes([
				__DIR__ . '/../../public/ensphere/ensphere/' => base_path( 'public/ensphere/ensphere/' ),
			]);
		}
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'Ensphere\Ensphere\Services\Registrar'
		);
	}

}
