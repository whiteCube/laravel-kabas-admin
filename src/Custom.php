<?php 

namespace WhiteCube\Admin;

use Carbon\Carbon;
use WhiteCube\Admin\Traits\Getters;
use WhiteCube\Admin\Config\CustomConfig;

class Custom {

    use Getters;

    /**
     * This page's structure
     * @var Structure
     */
    protected $structure;

    /**
     * This custom's route name
     * @var string
     */
    protected $route;
    
    /**
     * Internal configuration for this custom
     * @var CustomConfig
     */
    protected $config;

    /**
     * The html output to display in the custom page
     * @var string
     */
    protected $output;

    /**
     * Create an instance
     * @param string $file
     */
    public function __construct($file)
    {
        $this->structure = new Structure($file);
        $this->route = $this->structure->route();
        $this->config = new CustomConfig($this->structure->config());
    }

    /**
     * Run the user's class
     * @return void
     */
    public function run($params)
    {
        $name = $this->config()->controller();
        $controller = new $name($params);
        $method = $this->config->method();
        $this->output = $controller->$method();
    }

}
