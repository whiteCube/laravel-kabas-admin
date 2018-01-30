<?php

namespace WhiteCube\Admin\Config;
use WhiteCube\Admin\Traits\Getters;

class PageConfig {

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