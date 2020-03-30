<?php

namespace WhiteCube\Admin;

use Carbon\Carbon;
use WhiteCube\Admin\Facades\Admin;
use WhiteCube\Admin\Traits\Getters;
use WhiteCube\Admin\Config\PageConfig;
use WhiteCube\Admin\Containers\MetaContainer;
use WhiteCube\Admin\Containers\FieldsContainer;

class Page {

    use Getters;

    /**
     * This page's fields
     * @var FieldContainer
     */
    protected $fields;

    /**
     * This page's structure
     * @var Structure
     */
    protected $structure;

    /**
     * This page's route name
     * @var string
     */
    protected $route;

    /**
     * This page's meta information
     * @var MetaContainer
     */
    protected $meta;

    /**
     * Internal configuration for this page
     * @var PageConfig
     */
    protected $config;

    /**
     * Create an instance
     * @param string $file
     */
    public function __construct($file)
    {
        $this->structure = new Structure($file);
        $this->route = $this->structure->route();
        $this->fields = new FieldsContainer($this->structure->fields());
        $this->meta = new MetaContainer($this->structure->meta());
        $this->config = new PageConfig($this->structure->config());
        $this->loadValues();
    }

    /**
     * Get the date of the last edit on this page
     * @return Carbon
     */
    public function lastModified()
    {
        $timestamps = [];
        foreach (Admin::locales() as $locale) {
            $timestamps[$locale] = Storage::lastModified($locale, $this->structure->file());
        }
        sort($timestamps);
        return Carbon::createFromTimestamp($timestamps[count($timestamps) - 1]);
    }

    /**
     * Load values from disk into the fields
     * @return void
     */
    protected function loadValues()
    {
        foreach (Admin::locales() as $locale) {
            $values = Storage::values($locale, $this->structure->file());
            $this->fields->setAll($values, $locale);
            $this->meta->setAll($values->meta, $locale);
        }
    }

    /**
     * Write the current data to disk
     * @return void
     */
    public function save()
    {
        foreach (Admin::locales() as $locale) {
            $data = [];
            $meta = $this->meta->compress($locale);
            $fields = $this->fields->compress($locale);
            $data['meta'] = $meta;
            $data = array_merge($data, $fields);
            $data = $this->fixImagePaths($data);
            Storage::update($locale, $this->structure->file(), $data);
        }
    }

    protected function fixImagePaths($data) {
        $this->path = '';
        $structure = $this->structure()->content();
        foreach($data as $key => $value) {
            if(!isset($structure->$key)) continue;
            $struct = $structure->$key;
            $this->path = $key;

            if(!isset($struct->type)) continue;

            if($struct->type == 'group') {
                $this->sanitizeRepeatable($struct, $data);
            }

            if($struct->type == 'image') {
                $this->sanitizeImage($struct, $data);
            }
        }

        return $data;
    }

    protected function sanitizeRepeatable($structure, &$data)
    {
        foreach($structure->options as $key => $option) {
            if($option->type == 'group') {
                $this->path .= '.' . $key;
                return $this->sanitizeRepeatable($option, $data);
            }
            if($option->type != 'image') continue;
            $path = $this->path .= '.' . $key;
            $this->sanitizeImage($data, $path);
        }
    }

    protected function sanitizeImage(&$data, $path) {
        $value = $this->getValue($data, $path);
        if(isset($value['file'])) {
            $value['path'] = $value['file']->path;
            unset($value['file']);
        }

        $this->setValue($data, $path, $value);
    }

    protected function getValue(&$data, $path)
    {
        $pathParts = explode('.', $path);
        $result = $data;
        foreach($pathParts as $part) {
            if(!isset($result[$part])) return false;
            $result = $result[$part];
        }
        return $result;
    }

    protected function setValue(&$array, $path, &$value, $delimiter = '.') {
        $pathParts = explode($delimiter, $path);

        $current = &$array;
        foreach($pathParts as $key) {
            if (!is_array($current)) { $current = array(); }
            $current = &$current[$key];
        }

        $backup = $current;
        $current = $value;

        return $backup;
    }
}
