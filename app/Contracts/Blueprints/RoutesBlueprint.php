<?php

namespace Ensphere\Ensphere\Contracts\Blueprints;

use Illuminate\Routing\Router;

interface RoutesBlueprint
{
    /**
     * @param Router $router
     * @return mixed
     */
    public function routes( Router $router );
}
