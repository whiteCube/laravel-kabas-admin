<?php 

namespace WhiteCube\Admin\Request;

use Illuminate\Http\Request;

class Bag
{
    /**
     * The raw request data
     * @var array
     */
    protected $raw;

    /**
     * The route hidden in the request
     * @var string
     */
    protected $route;

    /**
     * The page title
     * @var string
     */
    protected $title;

    /**
     * The meta values, by lang
     * @var array
     */
    protected $meta;

    /**
     * The values of the fields, by lang
     * @var array
     */
    protected $fields;

    /**
     * A class to handle file uploads
     * @var FileUploader
     */
    protected $uploader;

    /**
     * Create an instance
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->raw = $request->all();
        $this->route = $this->raw['route'] ?? false;
        $this->extract();
        $this->uploader = new FileUploader($request, $this->fields);
    }

    /**
     * Process the raw data
     * @return void
     */
    protected function extract()
    {
        foreach ($this->raw as $key => $item) {
            if ($key == 'route' || $key == '_token' || $key == 'structure') continue;
            $this->map($key, $item);
        }
    }

    /**
     * Sort the data to the proper locations
     * @param string $key
     * @param mixed $item
     * @return void
     */
    protected function map($key, $item)
    {
        if ($key == 'id') return;

        $descriptor = new FieldDescriptor($key);

        if ($descriptor->isTitle()) {
            return $this->setTitle($descriptor, $item);
        }

        if ($descriptor->isMeta()) {
            return $this->addMeta($descriptor, $item);
        }

        if ($descriptor->lang()) {
            return $this->fields[$descriptor->lang()][$descriptor->key()] = $item;
        }

        return $this->fields[$descriptor->key()] = $item;
    }

    /**
     * Set the title
     * @param FieldDescriptor $descriptor
     * @param string $item
     * @return void
     */
    protected function setTitle($descriptor, $item)
    {
        $this->title[$descriptor->lang()] = $item;
    }

    /**
     * Add a meta value
     * @param FieldDescriptor $descriptor
     * @param string $item
     * @return void
     */
    protected function addMeta($descriptor, $item)
    {
        $this->meta[$descriptor->lang()][$descriptor->unprefixed()] = $item;
    }

    /**
     * Get the langs submitted in the request
     * @return void
     */
    public function langs()
    {
        return array_keys($this->fields);
    }

    /**
     * Get only the fields submitted in the request
     * @param string $lang
     * @return array
     */
    public function fields($lang = false) 
    {
        if ($lang) return $this->fields[$lang];
        return $this->fields;
    }

    /**
     * Get only the meta values submitted in the request
     * @param string $lang
     * @return array
     */
    public function meta($lang = false) 
    {
        if($lang) return $this->meta[$lang];
        return $this->meta;
    }

    /**
     * Save uploaded files to disk
     * @return void
     */
    public function upload()
    {
        $this->fields = $this->uploader->upload();
    }

    /**
     * Get the raw request dara
     * @return array
     */
    public function raw()
    {
        return $this->raw;
    }

}
