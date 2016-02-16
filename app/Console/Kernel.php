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

		'Ensphere\Ensphere\Console\Commands\Ensphere\Rename\Command',
		'Ensphere\Ensphere\Console\Commands\Ensphere\Export\Command',
		'Ensphere\Ensphere\Console\Commands\Ensphere\Assets\Command',
		'Ensphere\Ensphere\Console\Commands\Ensphere\Migrate\Command',
		'Ensphere\Ensphere\Console\Commands\Ensphere\Registration\Command',
		'Ensphere\Ensphere\Console\Commands\Ensphere\Install\Command',

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
