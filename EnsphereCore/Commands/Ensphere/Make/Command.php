<?php namespace EnsphereCore\Commands\Ensphere\Make;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EnsphereCore\Commands\Ensphere\Make\Contract\Command as ContractCommand;
use EnsphereCore\Commands\Ensphere\Make\Controller\Command as ControllerCommand;

class Command extends IlluminateCommand {

	use ModuleTrait;

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:make';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Creates blank files';

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
		$name = $this->option( 'name' );
		if( is_null( $name ) ) {
			return $this->error( 'no name entered (artisan ensphere:make contract --name="my contract name"' );
		}
		switch( $this->argument( 'make_command' ) ) {
			case 'contract' :
				(new ContractCommand)->make( $this->option( 'name' ) );
			break;
			case 'controller' :
				(new ControllerCommand)->make( $this->option( 'name' ) );
			break;
		}
	}

	/**
	 * [getArguments description]
	 * @return [type] [description]
	 */
	protected function getArguments()
	{
		return [
			['make_command', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * [getOptions description]
	 * @return [type] [description]
	 */
	protected function getOptions()
	{
		return [
			['name', null, InputOption::VALUE_REQUIRED, 'Class name', null],
		];
	}

}