<?php

namespace WhiteCube\Admin\Traits;

trait Getters {

    public function __call($name, $args)
    {
        return $this->$name;
    }

}