<?php namespace Ensphere\Ensphere\Providers;

use Illuminate\Support\ServiceProvider;
use EnsphereCore\Libs\Config\Publish;
use EnsphereCore\Libs\Providers\Service;

class AppServiceProvider extends ServiceProvider
{


	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom( __DIR__ . '/../../resources/views', 'ensphere.ensphere' );
		if( is_module( __DIR__ ) ) {
			$this->publishes( Publish::bower([
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
		if( ! is_module( __DIR__ ) ) {
			$contracts = Service::contracts([

				/** THESE ARE APPLICATION CONTRACTS. */

			]);
			foreach( $contracts as $blueprint => $contract ) {
				$this->app->bind( $blueprint, $contract );
			}
		}
	}

}
