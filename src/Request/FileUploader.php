<?php

namespace WhiteCube\Admin\Request;

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
        $this->recursivelyUploadFiles($this->request->files->all(), []);
        return $this->fields;
    }

    /**
     * Iterate through values and upload each file
     * @param mixed $values
     * @param array $path
     * @return void
     */
    protected function recursivelyUploadFiles($values, $path)
    {
        foreach ($values as $key => $value) {
            $path[] = $key;

            if (!$this->isFile($value)) {
                $this->recursivelyUploadFiles($value, $path);
                continue;
            }

            $name = $this->generateName($value);
            $value->move(public_path('uploads/'), $name);
            $this->replaceFileValue($path, $name);
        }
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
     * Generate a hashed name
     * @param UploadedFile $value
     * @return string
     */
    protected function generateName($value)
    {
        return sha1(microtime()) . '.' . $value->getClientOriginalExtension();
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