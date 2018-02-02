<?php 

namespace WhiteCube\Admin;

class Field
{
    protected $values = [];
    public $type;
    public $label;
    public $description;
    public $structure;

    /**
     * Create an instance
     * @param string $key
     * @param object $structure
     */
    public function __construct($key, $structure)
    {
        $this->key = $key;
        $this->structure = $structure;
        $this->label = $this->structure->label;
        $this->description = $this->structure->description ?? null;
        $this->type = $this->structure->type;
    }

    /**
     * Check if this field can contain other fields
     * @return boolean
     */
    public function isNestable()
    {
        return ($this->structure->type == 'repeater' ||
                $this->structure->type == 'flexible' ||
                $this->structure->type == 'group' ||
                $this->structure->type == 'gallery');
    }

    /**
     * Add prefixes to the fields contained by this field
     * @param object $subfields
     * @param string $locale
     * @param string $name
     * @return void
     */
    protected function prefixSubfields($subfields, $locale, $name)
    {
        if (!isset($subfields)) return;
        if (isset($subfields->options)) {
            return $this->prefixSubfields($subfields->options, $locale, $name);
        }
        foreach ($subfields as $key => $field) {
            if (isset($field->options)) {
                $this->prefixSubfields($field->options, $locale, $name . '[' . $key . ']');
            }
            $field->name = $locale . '|' . $name . '>' . $key;
        }
    }

    /**
     * Render some html to put into the page
     * @param string $locale
     * @return void
     */
    public function render($locale)
    {
        if (isset($this->structure->controllers->show)) {
            return $this->callUserShowMethod($locale);
        }
        
        if ($this->isNestable() && isset($this->structure->options)) {
            $this->prefixSubfields($this->structure->options, $locale, $this->key);
        }

        $value = $this->value($locale);

        return '<genericfield 
                    name="' . $this->name($locale) . '" 
                    description="'. $this->description .'"
                    :structure="' . htmlspecialchars(json_encode($this->structure, ENT_QUOTES)) . '" 
                    :value="' . htmlspecialchars(json_encode($value->get() ?? '')) . '" ></genericfield>';
    }

    /**
     * Call a custom render process
     * @param string $locale
     * @return string
     */
    protected function callUserShowMethod($locale)
    {
        $parts = explode('@', $this->structure->controllers->show);
        $controller = new $parts[0];
        return call_user_func([$controller, $parts[1]], $this, $locale);
    }

    /**
     * Set the value of this field
     * @param mixed $value
     * @param string $locale
     * @return void
     */
    public function setValue($value, $locale = 'shared')
    {
        if(isset($this->values[$locale])) {
            $this->values[$locale]->setRaw($value);
        } else {
            $this->values[$locale] = new Value($value, $this->type);
        }
    }

    /**
     * Read the value for the specified locale
     * @param string $locale
     * @return mixed
     */
    public function value($locale)
    {
        return $this->values[$locale] ?? new Value(null, $this->type);
    }

    /**
     * Check if this field is shared
     * @return boolean
     */
    public function isShared()
    {
        return isset($this->values['shared']);
    }

    public function isTranslated()
    {
        dd($this->values);
        return '';
    }

    /**
     * Check if this field is tabbed
     * @return boolean
     */
    public function isTabbedGroup()
    {
        return $this->type == 'group' && ($this->structure->tabbed ?? false);
    }

    /**
     * Compute the name attribute of the field
     * @param string $locale
     * @return string
     */
    protected function name($locale)
    {
        $name = $locale . '|' . $this->key;
        if ($locale == 'shared') $name = $this->key;
        return $name;
    }

}
