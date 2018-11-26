<?php

namespace WhiteCube\Admin;

use WhiteCube\Admin\Traits\Getters;
use WhiteCube\Admin\Facades\Admin;

class Structure {

    use Getters;

    /**
     * The structure file name
     * @var string
     */
    protected $file;

    /**
     * The raw content of the json file
     * @var object
     */
    protected $content;

    /**
     * Create an instance
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->content = $this->loadContent();
    }

    /**
     * Load the json file containing the structure
     * @return object
     */
    protected function loadContent()
    {
        return Storage::structure($this->file);
    }

    /**
     * Get the structure of all fields, excluding the configuration key
     * @return object
     */
    public function fields()
    {
        $content = clone $this->content;
        unset($content->kabas);
        return $content;
    }

    /**
     * Get the structure of the meta fields
     * @return object
     */
    public function meta()
    {
        return $this->content->kabas->meta;
    }

    /**
     * Get the config object
     * @return object
     */
    public function config()
    {
        return $this->content->kabas;
    }

    /**
     * Get the prefixed meta structure as a string
     * @return string
     */
    public function prefixedMeta($lang)
    {
        $prefixed = new \stdClass;
        foreach ($this->meta() as $key => $value) {
            $prefixed->{$lang .'|meta#' . $key} = $value;
        }
        return htmlentities(json_encode($prefixed));
    }

    /**
     * Extract the route name from the file name
     * @return string
     */
    public function route()
    {
        return str_replace(['.json', 'customs/', 'models/'], '', $this->file);
    }

    /**
     * Returns the array of searchable fields
     * @return array
     */
    public function search()
    {
        return $this->content->kabas->search_query ?? false;
    }

    /**
     * Returns the ORDER BY clause if there is one
     * @return string?
     */
    public function order()
    {
        return $this->content->kabas->orderBy ?? false;
    }

    public function validation()
    {
        $rules = [];
        foreach(Admin::locales() as $locale) {
            foreach($this->fields() as $key => $field) {
                if(!isset($field->validation)) continue;
                $rules[$locale . '|' . $key] = $field->validation;
            }
        }
        return $rules;
    }

}