<?php namespace EnsphereCore\Commands\Ensphere\Make\Controller;

use EnsphereCore\Commands\Ensphere\Make\Contract\Command as ContractCommand;
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
	public function make( $name ) {
		$this->name = ucfirst( camel_case( $name ) );
		$this->createController();
		$this->createContract();
		$this->createRequests();
	}

	/**
	 * [createController description]
	 * @return [type] [description]
	 */
	protected function createController()
	{

		$singular = str_singular( $this->name );
		$camelSingular = lcfirst( $singular );

		$tmpl = file_get_contents( __DIR__ . "/Controller.tmpl" );
		$tmpl = str_replace(
			[
				'{{NAME}}',
				'{{NAMESPACE}}',
				'{{DELETE_METHOD}}',
				'{{IDNAME}}' ,
				'{{CREATE_METHOD}}',
				'{{CREATE_REQUEST}}',
				'{{SHOW_CREATE_METHOD}}',
				'{{EDIT_METHOD}}',
				'{{EDIT_REQUEST}}',
				'{{SHOW_EDIT_METHOD}}'
			],
			[
				$this->name,
				$this->namespace,
				"delete{$singular}",
				"{$camelSingular}ID",
				"create{$singular}",
				"Create{$singular}Request",
				"showCreate{$singular}",
				"edit{$singular}",
				"Edit{$singular}Request",
				"showEdit{$singular}"
			],
			 $tmpl );
		$path = app_path( "Http/Controllers/{$this->name}Controller.php" );
		if( ! file_exists( $path ) ) {
			file_put_contents( $path, $tmpl );
		}
	}

	/**
	 * [createContract description]
	 * @return [type] [description]
	 */
	protected function createContract()
	{
		(new ContractCommand)->make( $this->name );
	}

	/**
	 * [createRequests description]
	 * @return [type] [description]
	 */
	protected function createRequests()
	{
		$singular = str_singular( $this->name );
		$create = "Create{$singular}Request";
		$edit = "Edit{$singular}Request";
		Artisan::call( 'make:request', [ 'name' => $edit ]);
		Artisan::call( 'make:request', [ 'name' => $create ]);
	}

}