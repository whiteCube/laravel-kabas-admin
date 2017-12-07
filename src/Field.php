<?php 

namespace WhiteCube\Admin;

class Field {

    protected $values = [];

    public function __construct($key, $structure)
    {
        $this->key = $key;
        $this->structure = $structure;
    }

    public function render($lang)
    {
        if(isset($this->structure->controllers->show)) {
            return $this->callUserShowMethod($lang);
        }

        return '<genericfield name="' . $lang . '|' .$this->key . '" :structure="' . json_encode($this->structure) . '" value="' . $this->values[$locale] . '" ></genericfield>';
    }

    protected function callUserShowMethod($lang)
    {
        $parts = explode('@', $this->structure->controllers->show);
        $controller = new $parts[0];
        return call_user_func([$controller, $parts[1]], $this, $lang);
    }

    public function setValue($value, $locale)
    {
        $this->values[$locale] = $value;
    }

}