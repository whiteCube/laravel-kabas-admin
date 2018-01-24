<?php

namespace WhiteCube\Admin;
use WhiteCube\Admin\Traits\Getters;

class Config {

    use Getters;

    /**
     * The name of the resource
     * @var string
     */
    protected $name;

    /**
     * The icon of the resource
     * @var string
     */
    protected $icon;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->name = $structure->name;
        $this->icon = $structure->icon ?? 'liste';
    }

}