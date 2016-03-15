<?php namespace EnsphereCore\Commands\Ensphere\Database;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;

class Command extends IlluminateCommand {

	use ModuleTrait;

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:database';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Sets your database details';

	/**
	 * [$currentStructure description]
	 * @var [type]
	 */
	private $currentStructure;

	/**
	 * [fire description]
	 * @return [type] [description]
	 */
	public function fire()
	{
		$data = $this->getDotEnvData();
		$data['DB_DATABASE'] = $this->ask( 'Database name?' );
		$data['DB_USERNAME'] = $this->ask( 'Database username?' );
		$data['DB_PASSWORD'] = $this->ask( 'Database password?' );
		$this->saveDotEnvData( $data );
		$this->info( ".env file saved, go create the database '" . $data['DB_DATABASE'] . "' now!" );
	}

	/**
	 * [saveDotEnvData description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function saveDotEnvData( $data )
	{
		$dotEnvPath = base_path( '.env' );
		$dotEnv = "\n";
		foreach( $data as $key => $value ) {
			$dotEnv .= "{$key}={$value}\n";
		}
		file_put_contents( $dotEnvPath, $dotEnv );
	}

	/**
	 * [getDotEnvData description]
	 * @return [type] [description]
	 */
	public function getDotEnvData()
	{
		$fileData = array_filter( explode( "\n", file_get_contents( base_path( '.env' ) ) ) );
		$data = [];
		foreach( $fileData as $keyValuePair ) {
			$split = explode( '=', $keyValuePair, 2 );
			$data[trim($split[0])] = trim($split[1]);
		}
		return $data;
	}

	/**
	 * [getArguments description]
	 * @return [type] [description]
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * [getOptions description]
	 * @return [type] [description]
	 */
	protected function getOptions()
	{
		return [];
	}

}