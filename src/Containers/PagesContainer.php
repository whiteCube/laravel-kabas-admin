<?php

namespace WhiteCube\Admin\Containers;

use WhiteCube\Admin\Page;
use WhiteCube\Admin\Storage;

class PagesContainer extends BaseContainer {

    /**
     * Load the page items from disk
     * @return array
     */
    public function load()
    {
        $this->items = [];
        $filenames = Storage::structures('admin_structures');
        foreach ($filenames as $file) {
            $this->items[$file] = new Page($file);
        }
        $this->sort();
    }

    /**
     * Get the list of pages sorted alphabetically
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