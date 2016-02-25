<?php namespace EnsphereCore\Commands\Ensphere\Export;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Command extends IlluminateCommand {

	use ModuleTrait;

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:export';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Clears up the module to align with installation.';

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
		$this->currentStructure = $this->getCurrentVendorAndModuleName();
		$this->exportMigrations();
	}

	/**
	 * [exportMigrations description]
	 * @return [type] [description]
	 */
	private function exportMigrations()
	{
		$databaseFolder = base_path( 'database/migrations/vendor/' . $this->currentStructure['vendor'] . '/' . $this->currentStructure['module'] . '/' );
		if( ! file_exists( $databaseFolder ) ) {
			return $this->line( 'no migrations to move' );
		}
		$this->moveFilesToResources( $databaseFolder );
	}

	/**
	 * [moveFilesToResources description]
	 * @return [type] [description]
	 */
	private function moveFilesToResources( $folder )
	{
		$moved = array();
		foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $file ) {
			if( $file->getExtension() === 'php' ) {
				copy( $folder . $file->getBasename(), base_path( 'resources/database/migrations/' . $file->getBasename() ) );
				$moved[] = $file->getBasename() . ' moved to resources';
				unlink( $folder . $file->getBasename() );
			}
		}
		if( ! $moved ) {
			$this->line( 'no migrations to move' );
		} else {
			foreach( $moved as $message ) {
				$this->info( $message );
			}
		}
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