<?php

namespace Ensphere\Ensphere\Http\Controllers;

use Illuminate\Http\Request;
use Purposemedia\FrontContainer\Http\Controllers\Controller;
use Ensphere\Ensphere\Contracts\Blueprints\ApiBlueprint;

class ApiController extends Controller
{

    /**
     * @var ApiBlueprint
     */
    protected $repo;

    /**
     * ApiController constructor.
     *
     * @param ApiBlueprint $contract
     */
    public function __construct( ApiBlueprint $contract )
    {
        $this->repo = $contract;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function render( Request $request )
    {
        $response = [];
        foreach( (array) $request->input( 'actions' ) as $action ) {
            $response[] = [
                'callback'  => $action[ 'callback' ],
                'response'  => $this->repo->{camel_case( $action[ 'method' ] )}( $request, isset( $action[ 'modelId' ] ) ? $action[ 'modelId' ] : null ),
                'guid'      => $action[ 'guid' ]
            ];
        }
        return $response;
    }

}

