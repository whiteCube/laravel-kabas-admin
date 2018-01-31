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
            Storage::update($locale, $this->structure->file(), $data);
        }
    }
}
