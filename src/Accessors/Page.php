<?php

namespace WhiteCube\Admin\Accessors;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class Page
{
    protected $route;
    protected $json;
    protected $meta;

    public function __construct(Meta\Container $metas)
    {
        $this->load();
        $this->meta = $metas->load($this->getMeta());
    }

    /**
     * Load the contents of the proper json file for this route
     * @return void
     */
    protected function load()
    {
        $this->route = Route::currentRouteName();
        $this->json = json_decode($this->getFile()) ?? $this->getEmptyJson();
    }

    /**
     * Return the contents of the proper file for this page
     * @return string
     */
    protected function getFile() : string
    {
        if (!Storage::disk('admin_values')->exists(Lang::locale() . '/static/' . $this->route . '.json')) {
            return false;
        }
        return Storage::disk('admin_values')->get(Lang::locale() . '/static/' . $this->route . '.json');
    }

    /**
     * Returns the loaded json's meta object or an empty array
     * @return object|array
     */
    protected function getMeta()
    {
        return isset($this->json->meta) ? $this->json->meta : [];
    }

    /**
     * Returns the page's title
     * @param  string $prefix
     * @param  string $suffix
     * @return string
     */
    public function title(string $prefix = null, string $suffix = null) : string
    {
        if (!isset($this->json->kabas_title)) {
            return false;
        }
        return $prefix . $this->json->kabas_title . $suffix;
    }

    /**
     * Set the title of the page
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->json->kabas_title = $title;
    }

    /**
     * Get the meta container object
     * @return App\Utils\Meta\Container
     */
    public function meta() : Meta\Container
    {
        return $this->meta;
    }

    /**
     * Get a value from this page's json data
     * @param  string $key
     * @return mixed
     */
    public function content(string $key)
    {
        $path = explode('.', $key);
        $result = $this->json;
        foreach ($path as $pathPart) {
            $result = $result->$pathPart;
        }
        return $result;
    }

    /**
     * Get an empty object
     * @return object
     */
    protected function getEmptyJson()
    {
        return (object) ['kabas_title' => '', 'metas' => (object) []];
    }
}
