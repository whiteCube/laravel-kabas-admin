<?php 

namespace WhiteCube\Admin;

use Illuminate\Support\Facades\Storage as Store;

class Storage {

    /**
     * Get the values from json file
     * @param string $locale
     * @param string $file
     * @return object
     */
    static function values($locale, $file)
    {
        return json_decode(Store::disk('admin_values')->get($locale . '/static/' . $file));
    }

    /**
     * Get a structure json file
     * @param string $structure
     * @return object
     */
    static function structure($structure)
    {
        return json_decode(Store::disk('admin_structures')->get($structure));
    }

    /**
     * Get the list of structure files
     * @return array
     */
    static function structures($disk, $directory = '')
    {
        return Store::disk($disk)->files($directory);
    }

    /**
     * Read values and update them with new ones
     * @param string $locale
     * @param string $file
     * @param array $data
     * @return void
     */
    static function update($locale, $file, $data)
    {
        $previous = (array) Storage::values($locale, $file);
        $new = array_replace_recursive($previous, $data);
        return self::save($locale, $file, $new);
    }

    /**
     * Write data to json
     * @param string $locale
     * @param string $file
     * @param array $data
     * @return void
     */
    static function save($locale, $file, $data)
    {
        return Store::disk('admin_values')->put($locale . '/static/' . $file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Get the last modified date for a file
     * @param string $locale
     * @param string $file
     * @return string
     */
    static function lastModified($locale, $file)
    {
        return Store::disk('admin_values')->lastModified($locale . '/static/' . $file);
    }

}