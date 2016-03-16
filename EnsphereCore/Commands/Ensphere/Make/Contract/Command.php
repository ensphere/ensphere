<?php namespace EnsphereCore\Commands\Ensphere\Make\Contract;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
use Artisan;

class Command {

	use ModuleTrait;

	private $currentStructure;

	private $namespace;

	private $name;

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		$this->currentStructure = $this->getCurrentVendorAndModuleName();
		$this->namespace = $this->currentStructure['camelCasedVendor'] . '\\' . $this->currentStructure['camelCasedModule'];
	}

	/**
	 * [make description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function make( $name )
	{
		$this->name = ucfirst( camel_case( $name ) );
		$this->createContract();
		$this->createBlueprint();
		$this->createModel();
		$this->addToEnsphereRegistration();
		$this->reRegister();
	}

	/**
	 * [createContract description]
	 * @return [type] [description]
	 */
	protected function createContract()
	{
		$singular = str_singular( $this->name );
		$camelSingular = lcfirst( $singular );
		$camelPlural = lcfirst( $this->name );

		$tmpl = file_get_contents( __DIR__ . "/Contract.tmpl" );
		$tmpl = str_replace(
			[
				'{{NAME}}',
				'{{NAMESPACE}}',
				'{{SINGLE}}',
				'{{DELETE_METHOD}}',
				'{{IDNAME}}' ,
				'{{CREATE_METHOD}}',
				'{{CREATE_REQUEST}}',
				'{{SHOW_CREATE_METHOD}}',
				'{{EDIT_METHOD}}',
				'{{EDIT_REQUEST}}',
				'{{SHOW_EDIT_METHOD}}',
				'{{CAMEL_PLURAL}}',
				'{{CAMEL_SINGULAR}}'
			],
			[
				$this->name,
				$this->namespace,
				$singular,
				"delete{$singular}",
				"{$camelSingular}ID",
				"create{$singular}",
				"Create{$singular}Request",
				"showCreate{$singular}",
				"edit{$singular}",
				"Edit{$singular}Request",
				"showEdit{$singular}",
				$camelPlural,
				$camelSingular
			],
			$tmpl );
		$path = app_path( "Contracts/{$this->name}.php" );
		if( ! file_exists( $path ) ) {
			file_put_contents( $path, $tmpl );
		}
	}

	/**
	 * [createBlueprint description]
	 * @return [type] [description]
	 */
	protected function createBlueprint()
	{
		$tmpl = file_get_contents( __DIR__ . "/Blueprint.tmpl" );
		$tmpl = str_replace( [ '{{NAME}}', '{{NAMESPACE}}' ], [ $this->name, $this->namespace ], $tmpl );
		$path = app_path( "Contracts/Blueprints/{$this->name}.php" );
		if( ! file_exists( $path ) ) {
			file_put_contents( $path, $tmpl );
		}
	}

	/**
	 * [createModel description]
	 * @return [type] [description]
	 */
	protected function createModel()
	{
		$name = str_singular( $this->name );
		$tmpl = file_get_contents( __DIR__ . "/Model.tmpl" );
		$tmpl = str_replace( [ '{{NAME}}', '{{NAMESPACE}}' ], [ $name, $this->namespace ], $tmpl );
		$path = app_path( "Models/{$name}.php" );
		if( ! file_exists( $path ) ) {
			file_put_contents( $path, $tmpl );
		}
	}

	/**
	 * [addToEnsphereRegistration description]
	 */
	protected function addToEnsphereRegistration()
	{
		$registrationPath = base_path( 'EnsphereCore/ensphere-registration.json' );
		$registration = json_decode( file_get_contents( $registrationPath ) );
		$registration->contracts->{"{$this->namespace}\Contracts\Blueprints\\{$this->name}"} = "{$this->namespace}\Contracts\\{$this->name}";
		file_put_contents( $registrationPath, json_encode( $registration, JSON_PRETTY_PRINT ) );
	}

	/**
	 * [reRegister description]
	 * @return [type] [description]
	 */
	protected function reRegister()
	{
		Artisan::call( 'ensphere:register' );
	}


}