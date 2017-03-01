<?php

namespace Ensphere\Ensphere\Exceptions;

use EnsphereCore\Libs\Exceptions\ExceptionHandler;
use Exception;

class Handler extends ExceptionHandler
{

    /**
     * @param Exception $e
     */
    public function report( Exception $e )
    {
        parent::report( $e );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render( $request, Exception $e )
    {
        return parent::render( $request, $e );
    }

}
