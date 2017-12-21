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

        $value = $this->values[$lang];

        if($this->type == 'date') {
            $value = (string) $this->values[$lang];
        }

        $name = $lang . '|' . $this->key;
        if($lang == 'shared') $name = $this->key;

        return '<genericfield 
                    name="' . $name . '" 
                    :structure="' . htmlspecialchars(json_encode($this->structure, ENT_QUOTES)) . '" 
                    :value="' . htmlspecialchars(json_encode($value ?? '')) . '" ></genericfield>';
    }

    protected function callUserShowMethod($lang)
    {
        $parts = explode('@', $this->structure->controllers->show);
        $controller = new $parts[0];
        return call_user_func([$controller, $parts[1]], $this, $lang);
    }

    public function setValue($value, $locale = false)
    {
        if(!$locale) return $this->values = $value;
        $this->values[$locale] = $value;
    }

    public function value($lang)
    {
        return $this->values[$lang] ?? '';
    }

    public function isTabbedGroup()
    {
        return $this->type == 'group' && ($this->structure->tabbed ?? false);
    }

}