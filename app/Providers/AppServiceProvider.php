<?php namespace Ensphere\Ensphere\Providers;

use Illuminate\Support\ServiceProvider;
use Libs\Helper;

class AppServiceProvider extends ServiceProvider {

	/**
	 * [isModule description]
	 * @return boolean [description]
	 */
	public static function isModule() {
		return file_exists( __DIR__ . "/../../../../../vendor" );
	}


	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom( __DIR__ . '/../../resources/views', 'ensphere.ensphere' );
		if( self::isModule() ) {
			$this->publishes( \Libs\Config\Publish::bower([
				__DIR__ . '/../../public/package/ensphere/ensphere/' => base_path( 'public/package/ensphere/ensphere/' )
			], __DIR__ ));
		}
	}

	/**
	 * THESE ARE APPLICATION CONTRACTS.
	 * REGISTER MODULE CONTRACTS IN THE REGISTRATION FILE SO THEY CAN BE EXTENDED PER APPLICATION
	 */
	public function register()
	{
		if( ! self::isModule() ) {
			$contracts = \Libs\Providers\Service::contracts([
				// THESE ARE APPLICATION CONTRACTS.

			]);
			foreach( $contracts as $blueprint => $contract ) {
				$this->app->bind( $blueprint, $contract );
			}
		}
	}

}
