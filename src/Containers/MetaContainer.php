<?php

namespace WhiteCube\Admin\Containers;

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

    /**
     * Get prefixed values to insert into the view
     * @param string $locale
     * @return void
     */
    public function prefixedValues($locale)
    {
        $prefixed = new \stdClass;
        foreach ($this->items as $key => $field) {
            $prefixed->{$locale . '|meta#' . $key} = $field->value($locale)->get();
        }
        return htmlentities(json_encode($prefixed));
    }

}