<?php

namespace WhiteCube\Admin\Request;

use WhiteCube\Admin\Traits\Getters;

class FieldDescriptor {

    use Getters;

    /**
     * The lang of this field
     * @var string
     */
    protected $lang;

    /**
     * The key of this field
     * @var string
     */
    protected $key;

    /**
     * Create an instance
     * @param string $key
     */
    public function __construct($key)
    {
        $parts = explode('|', $key);
        $this->lang = count($parts) == 1 ? false : $parts[0];
        $this->key = count($parts) == 1 ? $parts[0] : $parts[1];
    }

    /**
     * Check if this field is the page title
     * @return boolean
     */
    public function isTitle()
    {
        return $this->key == 'kabas_title';
    }

    /**
     * Check if this field is a meta field
     * @return boolean
     */
    public function isMeta()
    {
        return starts_with($this->key, 'meta#');
    }

    /**
     * Get the unprefixed key
     * @return string
     */
    public function unprefixed()
    {
        return str_replace('meta#', '', $this->key);
    }

}