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
     * The filters
     */
    protected $filters;

    /**
     * Should we show the create button on the list page
     * @var bool
     */
    protected $createButton;

    /**
     * Should we show the delete button on the list page
     * @var bool
     */
    protected $deleteButton;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->name = $structure->name;
        $this->model = $structure->model;
        $this->filters = isset($structure->filters) ? $structure->filters : null;
        $this->columns = $structure->columns;
        $this->createButton = $structure->createButton ?? null;
        $this->deleteButton = $structure->deleteButton ?? null;
    }

}
