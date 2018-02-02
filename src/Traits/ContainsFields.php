<?php

namespace WhiteCube\Admin\Traits;

use ArrayIterator;
use WhiteCube\Admin\Field;
use WhiteCube\Admin\Facades\Admin;

trait ContainsFields {

    /**
     * The list of untranslated fields
     * @var array
     */
    protected $shared = [];

    /**
     * The list of translated fields
     * @var array
     */
    protected $translated = [];

    /**
     * Create instances for each field
     * @param object $structure
     * @return array
     */
    protected function createFields($structure)
    {
        $fields = [];
        foreach ($structure as $key => $struct) {
            $fields[$key] = new Field($key, $struct);
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
        return $this->items[$key] ?? null;
    }

    /**
     * Set the value of a specific field
     * @param string $key
     * @param mixed $value
     * @param string $locale
     * @return void
     */
    public function set($key, $value, $locale = false)
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
        if(is_array($values)) $values = (object) $values;
        foreach ($this->items as $key => $item) {
            $this->set($key, $values->$key ?? null, $locale);
        }
    }

    /**
     * Merge request data to field values
     * @param array $data
     * @return void
     */
    public function merge($data)
    {
        foreach($data as $locale => $values) {
            $this->setAll($values, $locale);
        }
    }

    /**
     * Fill the fields with a model's values
     * @param Eloquent $model
     * @return void
     */
    public function fill($model)
    {
        foreach($this->items as $key => $field) {
            if (!in_array($key, $model->translatedAttributes ?? [])) {
                $this->shared[] = $key;
                $field->setValue($model->$key);
            } else {
                foreach (Admin::locales() as $locale) {
                    $this->translated[] = $key;
                    $field->setValue($model->translate($locale)->$key, $locale);
                }
            }
        }
    }

    /**
     * Check if there are any untranslated/shared fields
     * @return boolean
     */
    public function hasShared()
    {
        return count($this->shared) > 0;
    }

    /**
     * Check if there are any translated fields
     * @return boolean
     */
    public function hasTranslated()
    {
        return count($this->translated) > 0;
    }
    
    /**
     * Generate assoc array containing all values
     * @param locale $locale
     * @return array
     */
    public function compress($locale)
    {
        $data = [];
        foreach($this->items as $key => $field) {
            $data[$key] = $field->value($locale)->get();
        } 
        return $data;
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