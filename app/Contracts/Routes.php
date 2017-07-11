<?php

namespace Ensphere\Ensphere\Contracts;

use Ensphere\Ensphere\Contracts\Blueprints\RoutesBlueprint;
use Illuminate\Routing\Router;


class Routes implements  RoutesBlueprint
{

    protected $menuItems = [];

    /**
     * @param Router $router
     * @return mixed
     */
    public function routes( Router $router )
    {
        $router->group( [ 'prefix' => 'api', 'middleware' => [ 'web' ] ], function( $router ) {

            $router->get( 'render', [ 'uses' => 'ApiController@render' ] );

        });
    }

    protected function menuItems()
    {
        // use LukeSnowden\Menu\Menu;
        //foreach( $this->menuItems as $menuItem ) {
        //    Menu::addItem( $menuItem )->toMenu( 'main' );
        //}
    }
}
