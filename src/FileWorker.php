<?php 

namespace WhiteCube\Admin;

use Illuminate\Support\Facades\Storage;

class FileWorker {

    /**
     * List of file names to ignore
     * @var array
     */
    protected $excludes = [
        '.DS_Store'
    ];

    /**
     * Get an array of filenames found in the specified directory
     * @param  string $directory  The directory to parse
     * @return array
     */
    public function files(string $disk, $directory = '') : array
    {
        $files = Storage::disk($disk)->files($directory);
        return array_diff($files, $this->excludes);
    }

    /**
     * Get the content of a file
     * @param  string $file The file name
     * @return string
     */
    public function get(string $disk, string $file) : string
    {
        return Storage::disk($disk)->get($file);
    }

}