<?php 

namespace WhiteCube\Admin\Accessors\Meta;

class Item {

    protected $name;
    protected $value;

    public function __construct(string $name, ?string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Render the HTML for this element
     * @return string
     */
    public function render() : string
    {
        return '<meta name="' . $this->name . '" content="' . $this->value . '">';
    }

    /**
     * Change the value of this element
     * @param string
     */
    public function set(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the name of this meta element
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * Get the value of this meta element
     * @return string
     */
    public function value() : string
    {
        return $this->value;
    }

}