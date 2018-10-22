<?php

namespace WhiteCube\Admin;

use WhiteCube\Admin\Traits\Getters;
use Carbon\Carbon;

class Value {

    use Getters;

    /**
     * The raw value
     * @var mixed
     */
    protected $raw;

    /**
     * The type of the field containing this value
     * @var string
     */
    protected $type;

    /**
     * The computed value
     * @var mixed
     */
    protected $value;

    /**
     * Create an instance
     * @param mixed $raw
     * @param string $type
     */
    public function __construct($raw, $type)
    {
        $this->raw = $raw;
        $this->type = $type;
        $this->value = $this->compute();
    }

    /**
     * Export this class as a string
     * @return string
     */
    public function __toString()
    {
        return $this->value ?? '';
    }

    /**
     * Get the computed value
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Set the value and bypass the processing
     * @param mixed $value
     * @return void
     */
    public function setRaw($value)
    {
        $this->raw = $value;
        $this->value = $value;
    }

    /**
     * Do any processing necessary based on the type
     * @return mixed
     */
    protected function compute()
    {
        if($this->type == 'date') return $this->computeDate();
        if($this->type == 'image') return $this->computeImage();
        if($this->type == 'text' && $this->raw instanceof Carbon) return $this->value = (string) $this->raw;
        if($this->type == 'group' || $this->type == 'flexible') return $this->computeGroup();
        return $this->raw;
    }

    /**
     * Process a date value
     * @return string
     */
    protected function computeDate()
    {
        if(is_null($this->raw)) return null;
        return (string) $this->raw;
    }

    /**
     * Process an image value
     * @return string
     */
    protected function computeImage()
    {
        if(is_string($this->raw)) {
            $data = json_decode($this->raw);
            if(isset($data->file)) {
                $data->path = asset($data->file->path);
            } else {
                $data->path = asset($data->path);
            }
            return $data;
        }
        return $this->raw;
    }

    /**
     * Process a group value
     * @return array
     */
    protected function computeGroup()
    {
        if(!$this->raw) return null;
        $items = $this->recursivelyReplacePaths($this->raw);
        return $items;
    }

    /**
     * Traverse group values and change paths to absolute values
     * @param array $items
     * @return array
     */
    protected function recursivelyReplacePaths($items) {
        if (is_string($items)) return $items;
        foreach($items as $key => $value) {
            if ($key == 'path' && gettype($value) == 'string' && !str_contains('http', $value)) {
                $items->$key = asset($value);
                continue;
            } 
            if (gettype($value) == 'object' || gettype($value) == 'array') {
                $this->recursivelyReplacePaths($value);
            }
        }
        return $items;
    }

    public function empty()
    {
        return is_null($this->raw);
    }

}