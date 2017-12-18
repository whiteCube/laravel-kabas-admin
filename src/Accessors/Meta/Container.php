<?php 

namespace WhiteCube\Admin\Accessors\Meta;

class Container {

    protected $items = [];

    /**
     * Add metas
     * @param  object $items
     * @return $this
     */
    public function load($items)
    {
        foreach($items as $key => $value) {
            $this->add($key, $value);
        }
        return $this;
    }

    /**
     * Add a single meta element
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function add(string $key, ?string $value = '')
    {
        $this->items[$key] = new Item($key, $value);
        return $this;
    }

    /**
     * Output all meta tags
     * @return void
     */
    public function render()
    {
        $rendered = '';
        foreach($this->items as $item) {
            $rendered .= $item->render() . PHP_EOL;
        }
        echo $rendered;
    }

    /**
     * Update or insert a meta element
     * @param array|string $key
     * @param string $value
     */
    public function set($key, string $value)
    {
        if(is_array($key)) return $this->setMutliple($key, $value);
        if(!isset($this->items[$key])) return $this->add($key, $value);
        $this->items[$key]->set($value);
    }

    /**
     * Update or insert a value into multiple meta elements
     * @param array $keys
     * @param string $value
     */
    public function setMutliple($keys, $value)
    {
        foreach($keys as $item) {
            $this->set($item, $value);
        }
    }

}