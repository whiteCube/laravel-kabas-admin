<?php

namespace WhiteCube\Admin\Traits;

use ArrayIterator;
use WhiteCube\Admin\Field;

trait ContainsFields {

    /**
     * Create instances for each field
     * @param object $structure
     * @return array
     */
    protected function createFields($structure)
    {
        $fields = [];

        foreach ($structure as $key => $structure) {
            $fields[$key] = new Field($key, $structure);
        }

        return $fields;
    }

    /**
     * Get a specific field
     * @param string $key
     * @param string $locale
     * @return Field
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * Set the value of a specific field
     * @param string $key
     * @param mixed $value
     * @param string $locale
     * @return void
     */
    public function set($key, $value, $locale)
    {
        $this->items[$key]->setValue($value, $locale);
    }

    /**
     * Set all fields of the specified locale 
     * to the specified values
     * @param array $values
     * @param string $locale
     * @return void
     */
    public function setAll($values, $locale)
    {
        foreach ($this->items as $key => $item) {
            $this->set($key, $values->$key ?? null, $locale);
        }
    }

    /**
     * Make class iterable
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

}