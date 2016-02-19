<?php namespace Ensphere\Ensphere\Contracts;

use Illuminate\Database\Eloquent\Model;

abstract class Contract
{

    /**
     * [$model description]
     * @var [type]
     */
    protected $model;

    /**
     * [$viewPath description]
     * @var string
     */
    protected $viewPath = '';

    /**
     * [$rules description]
     * @var array
     */
    protected $rules = array();

    /**
     * [__construct description]
     * @param Model $model [description]
     */
    public function __construct( Model $model ) {
        $this->model = $model;
    }

    /**
     * [find description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function find( $id ) {
        return $this->model->findorfail( $id );
    }

    /**
     * [view description]
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function view( $view, $data = array() ) {
        return view()->make( $this->viewPath . $view, $data );
    }

    /**
     * [paginate description]
     * @param  integer $amount [description]
     * @return [type]          [description]
     */
    public function paginate( $amount = 10 ) {
        return $this->model->paginate( $amount );
    }


}