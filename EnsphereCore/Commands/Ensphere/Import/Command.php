<?php namespace EnsphereCore\Commands\Ensphere\Import;

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
	protected $name = 'ensphere:import';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Puts files back for working on module.';

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
		$this->importMigrations();
		$this->importSeeds();
		$this->call( "ensphere:update" );
	}

	/**
	 * [exportMigrations description]
	 * @return [type] [description]
	 */
	private function importMigrations()
	{
		$databaseFolder = base_path( 'resources/database/migrations/' );
		if( ! file_exists( $databaseFolder ) ) {
			return $this->line( 'no migrations to move' );
		}
		$this->moveFilesToDatabaseMigrations( $databaseFolder );
	}

	/**
	 * [importSeeds description]
	 * @return [type] [description]
	 */
	private function importSeeds()
	{
		$databaseFolder = base_path( 'resources/database/seeds/' );
		if( ! file_exists( $databaseFolder ) ) {
			return $this->line( 'no seeds to move' );
		}
		$this->moveFilesToDatabaseSeeds( $databaseFolder );
	}

	/**
	 * [moveFilesToDatabaseSeeds description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	private function moveFilesToDatabaseSeeds( $folder )
	{
		$moved = array();
		foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $file ) {
			if( $file->getExtension() === 'php' ) {
				copy( $folder . $file->getBasename(), base_path( 'database/seeds/vendor/' . $this->currentStructure['vendor'] . '/' . $this->currentStructure['module'] . '/' . $file->getBasename() ) );
				$moved[] = $file->getBasename() . ' moved to database/seeds';
				unlink( $folder . $file->getBasename() );
			}
		}
		if( ! $moved ) {
			$this->line( 'no seeds to move' );
		} else {
			foreach( $moved as $message ) {
				$this->info( $message );
			}
		}
	}

	/**
	 * [moveFilesToResources description]
	 * @return [type] [description]
	 */
	private function moveFilesToDatabaseMigrations( $folder )
	{
		$moved = array();
		foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $file ) {
			if( $file->getExtension() === 'php' ) {
				copy( $folder . $file->getBasename(), base_path( 'database/migrations/vendor/' . $this->currentStructure['vendor'] . '/' . $this->currentStructure['module'] . '/' . $file->getBasename() ) );
				$moved[] = $file->getBasename() . ' moved to database/migrations';
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