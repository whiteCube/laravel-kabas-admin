<?php

namespace WhiteCube\Admin;

use IteratorAggregate;
use WhiteCube\Admin\Traits\ContainsFields;

class MetaContainer implements IteratorAggregate {

    use ContainsFields;

    /**
     * The list of meta fields
     * @var array
     */
    protected $items;

    /**
     * Create an instance
     * @param object $structure
     */
    public function __construct($structure)
    {
        $this->items = $this->createFields($structure);
    }

}