<?php

namespace WhiteCube\Admin;

use IteratorAggregate;
use WhiteCube\Admin\Traits\ContainsFields;

class FieldsContainer implements IteratorAggregate {

    use ContainsFields;

    /**
     * The list of Field items
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

    /**
     * Get all tabbed groups
     * @return array
     */
    public function tabbed()
    {
        return array_filter($this->items, function($item) {
            return $item->isTabbedGroup();
        });
    }

    /**
     * Get general fields
     * @return array
     */
    public function general()
    {
        return array_filter($this->items, function ($item) {
            return !$item->isTabbedGroup();
        });
    }

}