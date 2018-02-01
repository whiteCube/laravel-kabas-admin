<?php

namespace WhiteCube\Admin\Containers;

use WhiteCube\Admin\Custom;
use WhiteCube\Admin\Storage;

class CustomsContainer extends BaseContainer {

    /**
     * Load the custom items from disk
     * @return array
     */
    public function load()
    {
        $this->items = [];
        $filenames = Storage::structures('admin_structures', 'customs');
        foreach ($filenames as $file) {
            $this->items[str_replace('customs/', '', $file)] = new Custom($file);
        }
        $this->sort();
    }

    /**
     * Get the list of customs sorted alphabetically
     * @return array
     */
    public function sort()
    {
        $this->sorted = $this->items;
        usort($this->sorted, function ($a, $b) {
            return strcmp($a->config()->name(), $b->config()->name());
        });
    }

}