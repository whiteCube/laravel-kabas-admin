<?php 

namespace WhiteCube\Admin;

class Field {

    protected $values = [];
    public $type;
    public $label;
    public $structure;

    public function __construct($key, $structure)
    {
        $this->key = $key;
        $this->structure = $structure;
        $this->label = $this->structure->label;
        $this->type = $this->structure->type;
    }

    public function isRepeatable()
    {
        return ($this->structure->type == 'repeater' ||
                $this->structure->type == 'flexible' ||
                $this->structure->type == 'gallery');
    }

    protected function prefixSubfields($options, $lang, $name)
    {
        if(!isset($options)) return;
        if(isset($options->options)) {
            return $this->prefixSubfields($options->options, $lang, $name);
        }
        foreach($options as $key => $field) {
            if(isset($field->options)) {
                $this->prefixSubfields($field->options, $lang, $name . '[' . $key . ']');
            }
            $field->name = $lang . '|' . $name . '>' . $key;
        }
    }

    public function render($lang)
    {
        if(isset($this->structure->controllers->show)) {
            return $this->callUserShowMethod($lang);
        }
        if(isset($this->structure->options)) {
            $this->prefixSubfields($this->structure->options, $lang, $this->key);
        }

        return '<genericfield 
                    name="' . $lang . '|' . $this->key . '" 
                    :structure="' . htmlspecialchars(json_encode($this->structure, ENT_QUOTES)) . '" 
                    :value="' . htmlspecialchars(json_encode($this->values[$lang] ?? '')) . '" ></genericfield>';
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

    public function value($lang)
    {
        // echo "\">";
        // dd($this, $this->values[$lang]);
        return $this->values[$lang] ?? '';
    }

}