<?php 

namespace WhiteCube\Admin;

use Illuminate\Support\Facades\Storage as Store;

class Storage {

    static function values($lang, $file)
    {
        return json_decode(Store::disk('admin_values')->get($lang . '/static/' . $file));
    }

    static function structure($structure)
    {
        return json_decode(Store::disk('admin_structures')->get($structure));
    }

    static function save($lang, $file, $data)
    {
        return Store::disk('admin_values')->put($lang . '/static/' . $file, json_encode($data, JSON_PRETTY_PRINT));
    }

    static function lastModified($lang, $file)
    {
        return Store::disk('admin_values')->lastModified($lang . '/static/' . $file);
    }

}