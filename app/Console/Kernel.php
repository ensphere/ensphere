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

		'EnsphereCore\Commands\Ensphere\Rename\Command',
		'EnsphereCore\Commands\Ensphere\Export\Command',
		'EnsphereCore\Commands\Ensphere\Import\Command',
		'EnsphereCore\Commands\Ensphere\Bower\Command',
		'EnsphereCore\Commands\Ensphere\Migrate\Command',
		'EnsphereCore\Commands\Ensphere\Registration\Command',
		'EnsphereCore\Commands\Ensphere\Install\Command',
		'EnsphereCore\Commands\Ensphere\Install\Update\Command',

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
