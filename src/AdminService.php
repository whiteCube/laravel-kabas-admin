<?php 

namespace WhiteCube\Admin;

use WhiteCube\Admin\Containers\PagesContainer;
use WhiteCube\Admin\Containers\ModelsContainer;
use WhiteCube\Admin\Containers\CustomsContainer;

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
     * @var CustomsContainer
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
        $this->customs = new CustomsContainer();
    }

    /**
     * Start loading values
     * @return void
     */
    public function load()
    {
        $this->pages->load();
        $this->models->load();
        $this->customs->load();
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
     * @return CustomsContainer
     */
    public function customs()
    {
        return $this->customs;
    }

}