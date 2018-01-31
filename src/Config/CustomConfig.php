<?php

namespace WhiteCube\Admin\Config;
use WhiteCube\Admin\Traits\Getters;

class CustomConfig {

    use Getters;

    /**
     * The name of the resource
     * @var string
     */
    protected $name;

    /**
     * The class to instantiate
     * @var string
     */
    protected $controller;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->name = $structure->name;
        $this->controller = $structure->controller;
    }

}