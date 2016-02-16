<?php namespace Ensphere\Ensphere\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'Ensphere\Ensphere\Console\Commands\Inspire',
		'Ensphere\Ensphere\Console\Commands\GenerateAssets',
		'Ensphere\Ensphere\Console\Commands\Registration',
		'Ensphere\Ensphere\Console\Commands\ModuleName',
		'Ensphere\Ensphere\Console\Commands\Export'
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();
	}

}
