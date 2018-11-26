<?php

namespace WhiteCube\Admin;

use WhiteCube\Admin\Traits\Getters;
use WhiteCube\Admin\Config\ModelConfig;
use WhiteCube\Admin\Containers\FieldsContainer;

class Model {

    use Getters;

    /**
     * This page's fields
     * @var FieldContainer
     */
    protected $fields;

    /**
     * This page's structure
     * @var Structure
     */
    protected $structure;

    /**
     * This page's route name
     * @var string
     */
    protected $route;

    /**
     * Internal configuration for this page
     * @var ModelConfig
     */
    protected $config;

    /**
     * Create an instance
     * @param string $file
     */
    public function __construct($file)
    {
        $this->structure = new Structure($file);
        $this->route = $this->structure->route();
        $this->fields = new FieldsContainer($this->structure->fields());
        $this->config = new ModelConfig($this->structure->config());
    }

    /**
     * Access properties and/or forward eloquent calls
     * @param string $method
     * @param mixed $params
     * @return mixed
     */
    public function __call($method, $params = false)
    {
        if(isset($this->$method)) return $this->$method;
        $method = $this->config->model() . '::' . $method;
        if(!$params) return call_user_func($method);
        return call_user_func($method, $params);
    }

    public function orderBy($orderby)
    {
        return call_user_func($this->config->model() . '::orderByRaw', $orderby);
    }

    public function paginate($amount)
    {
        return call_user_func($this->config->model() . '::paginate', $amount);
    }

}