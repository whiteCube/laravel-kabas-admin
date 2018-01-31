<?php

namespace WhiteCube\Admin\Containers;

class BaseContainer {

    /**
     * The list of items
     * @var array
     */
    protected $items;

    /**
     * The list of items sorted alphabetically
     * @var array
     */
    protected $sorted;

    /**
     * Get all items
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get the list of sorted items
     * @return array
     */
    public function sorted()
    {
        return $this->sorted;
    }

    /**
     * Get a single item by route name
     * @param string $route
     * @return mixed
     */
    public function get($route)
    {
        return $this->items[$route . '.json'] ?? null;
    }

}