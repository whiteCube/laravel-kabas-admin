<?php

namespace WhiteCube\Admin\Config;
use WhiteCube\Admin\Traits\Getters;

class ModelConfig {

    use Getters;

    /**
     * The name of the resource
     * @var string
     */
    protected $name;

    /**
     * The model class
     * @var string
     */
    protected $model;

    /**
     * The columns to display in the table view
     * @var object
     */
    protected $columns;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->name = $structure->name;
        $this->model = $structure->model;
        $this->columns = $structure->columns;
    }

}