<?php

namespace WhiteCube\Admin\Request;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    /**
     * The request instance
     * @var Request
     */
    protected $request;

    /**
     * The fields for the request
     * @var array
     */
    protected $fields;

    /**
     * Create an instance
     * @param Request $request
     * @param array $fields
     */
    public function __construct($request, $fields)
    {
        $this->request = $request;
        $this->fields = $fields;
    }

    /**
     * Handle the upload of request files
     * @return array
     */
    public function upload()
    {
        $this->recursivelyUpload($this->request->all());
        return $this->fields;
    }

    /**
     * Process
     * @param array $values
     * @return void
     */
    protected function recursivelyUpload($values)
    {
        if(!is_array($values)) return;
        foreach ($values as $key => $value) {

            $isFile = $this->isFile($value);
            $hasBase64 = $this->hasBase64($value);
            
            if(!$isFile && !$hasBase64 && is_array($value)) {
                $this->recursivelyUpload($value);
                continue;
            }

            if ($hasBase64) {
                $old = $value;
                $new = $this->uploadBase64($value);
                $field = $this->setField($old, $this->fields, $new);
            }

            if ($isFile) {
                $value->move(public_path('uploads/'), $name);
                $name = $this->generateName($value->getClientOriginalExtension());
                $this->replaceFileValue($path, $name);
            }

        }
    }

    /**
     * Overwrite a field's value
     * @param mixed $needle
     * @param array $haystack
     * @param mixed $replace
     * @return mixed
     */
    protected function setField($needle, &$haystack, $replace)
    {
        foreach ($haystack as $key => &$value) {
            if(is_array($value)) {
                $this->setField($needle, $value, $replace);
                continue;
            }
            if($value == $needle) {
                $value = $replace;
            }
        }
        return false;
    }

    /**
     * Check if value is an uploaded file
     * @param mixed $value
     * @return boolean
     */
    protected function isFile($value)
    {
        return is_object($value) && get_class($value) == UploadedFile::class;
    }

    /**
     * Check if value contains a base64 encoded file
     * @param string $value
     * @return boolean
     */
    protected function hasBase64($value)
    {
        return strpos($value, ';base64,') !== false;
    }

    /**
     * Undocumented function
     * @param string $value
     * @return string
     */
    protected function uploadBase64(&$value)
    {
        preg_replace_callback('/url\(data:image\/.*\)/', function($matches) use (&$value) {
            foreach ($matches as $key => $match) {
                $name = $this->saveBase64File($match);
                $value = str_replace($match, $name, $value);
            }
        }, $value);
        return $value;
    }

    /**
     * Write the base64 encoded file to disk
     * @param [string] $data
     * @return string
     */
    protected function saveBase64File($data)
    {
        $base64_str = substr($data, strpos($data, ",") + 1);
        preg_match('/image\/(.[^;]*);/', $data, $extension);
        $image = base64_decode($base64_str);
        $name = 'uploads/' . $this->generateName($extension[1]);
        file_put_contents(public_path() .'/'. $name, $image);
        return $name;
    }

    /**
     * Generate a hashed name
     * @param string $extension
     * @return string
     */
    protected function generateName($extension)
    {
        return sha1(microtime()) . '.' . $extension;
    }

    /**
     * Perform some formatting on the path array
     * @param array $path
     * @return array
     */
    protected function getPath($path)
    {
        unset($path[count($path) - 1]);
        if(!str_contains($path[0], '|')) return $path;
        $exploded = explode('|',  $path[0]);
        $path[0] = $exploded[0];
        array_splice($path, 1, 0, $exploded[1]);
        return $path;
    }

    /**
     * Update the field value to include the new uploaded file data
     * @param array $path
     * @param string $name
     * @return void
     */
    protected function replaceFileValue($path, $name)
    {
        $pathway = $this->getPath($path);
        $item = &$this->fields;
        $alt = '';

        foreach ($pathway as $i => $part) {
            $item = &$item[$part];
            if($i == count($pathway) - 1) {
                $alt = $item['alt'] ?? false;
            }
        }

        $item = (object) ['path' => 'uploads/' . $name, 'alt' => $alt];
    }

}