<?php

namespace WhiteCube\Admin\Containers;

use WhiteCube\Admin\Page;
use WhiteCube\Admin\Model;
use WhiteCube\Admin\Storage;

class ModelsContainer extends BaseContainer
{

    /**
     * Load the model items from disk
     * @return array
     */
    public function load()
    {
        $this->items = [];
        $filenames = Storage::structures('admin_structures', 'models');
        foreach ($filenames as $file) {
            $this->items[str_replace('models/', '', $file)] = new Model($file);
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