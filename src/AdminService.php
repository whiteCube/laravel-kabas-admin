<?php 

namespace WhiteCube\Admin;
use Illuminate\Support\Facades\Storage;

class AdminService {

    protected $pages;
    protected $models;

    public function __construct($config, FileWorker $fileworker)
    {
        $this->config = $config;
        $this->fileworker = $fileworker;
    }

    /**
     * Return the config array
     * @return array
     */
    public function config() : array
    {
        return $this->config;
    }

    /**
     * Easy accessor for config keys
     */
    public function __call($method, $args)
    {
        return $this->config['kabas-admin'][$method];
    }

    public function fileworker()
    {
        return $this->fileworker;
    }

    /**
     * Get a list of the pages defined in the application
     * @return array
     */
    public function pages() : array
    {
        if(count($this->pages)) return $this->pages;
        $pages = [];
        foreach($this->fileworker->files('admin_structures') as $file) {
            $pages[$file] = new Page($file);
        }
        usort($pages, function($a, $b) {
            return strcmp($a->config()->name(), $b->config()->name());
        });
        $this->pages = $pages;
        return $pages;
    }

    public function page($route)
    {
        foreach($this->pages() as $page) {
            if($page->route() == $route) return $page;
        }
        return null;
    }

    public function models() : array
    {
        if(count($this->models)) return $this->models;
        $models = [];
         foreach($this->fileworker->files('admin_structures', 'models') as $file) {
            $models[$file] = new Model($file);
        }
        usort($models, function($a, $b) {
            return strcmp($a->name, $b->name);
        });
        $this->models = $models;
        return $models;
    }


    public function model($filename)
    {
        foreach($this->models() as $model) {
            if($model->file == $filename) return $model;
        }
        return null;
    }
}