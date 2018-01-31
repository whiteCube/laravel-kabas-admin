<?php 

namespace WhiteCube\Admin;
use Illuminate\Support\Facades\Storage;
use WhiteCube\Admin\Containers\PagesContainer;
use WhiteCube\Admin\Containers\ModelsContainer;

class AdminService {

    /**
     * The configuration data
     * @var Repository
     */
    protected $config;

    /**
     * The pages
     * @var PagesContainer
     */
    protected $pages;

    /**
     * The models
     * @var ModelsContainer
     */
    protected $models;

    /**
     * The custom pages
     * @var array
     */
    protected $customs;

    /**
     * Create an instance
     * @param Repository $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->pages = new PagesContainer();
        $this->models = new ModelsContainer();
    }

    /**
     * Start loading values
     * @return void
     */
    public function load()
    {
        $this->pages->load();
        $this->models->load();
    }

    /**
     * Return the config repository
     * @return Repository
     */
    public function config()
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

    /**
     * Get a list of the pages defined in the application
     * @return PagesContainer
     */
    public function pages()
    {
        return $this->pages;
    }

    /**
     * Get the list of models
     * @return ModelsContainer
     */
    public function models()
    {
        return $this->models;
    }

    /**
     * Load and/or get the list of custom pages
     * @return array
     */
    public function customs() : array
    {
        if (count($this->customs)) return $this->customs;
        $customs = [];
        // foreach ($this->fileworker->files('admin_structures', 'customs') as $file) {
        //     $customs[$file] = new Custom($file);
        // }
        // usort($customs, function ($a, $b) {
        //     return strcmp($a->config()->name(), $b->config()->name());
        // });
        // $this->customs = $customs;
        return $customs;
    }

    /**
     * Get a single custom page
     * @param string $route
     * @return Custom
    */
    public function custom($route)
    {
        foreach ($this->customs() as $custom) {
            if ($custom->route() == $route) return $custom;
        }
        return null;
    }

}