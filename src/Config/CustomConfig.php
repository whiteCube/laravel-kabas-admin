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
     * The method to call on the class
     * @var string
     */
    protected $method;

    /**
     * Should we display this custom in the nav
     * @var bool
     */
    protected $nav;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->name = $structure->name;
        $parts = explode('@', $structure->controller);
        $this->controller = $parts[0];
        $this->method = $parts[1] ?? 'render';
        $this->nav = $structure->nav ?? true;
    }

}