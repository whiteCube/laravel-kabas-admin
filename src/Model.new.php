<?php

namespace WhiteCube\Admin;

use WhiteCube\Admin\Traits\Getters;

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
        $this->loadValues();
    }

    protected function loadValues()
    {
        
    }


}