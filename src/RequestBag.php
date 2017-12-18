<?php 

namespace WhiteCube\Admin;

use Illuminate\Http\Request;

class RequestBag {

    protected $data;
    protected $structure;
    protected $token;
    protected $items;
    protected $title;
    protected $metas;

    public function __construct(Request $request)
    {
        $this->data = $request->all();
        $this->structure = $this->data['structure'];
        $this->token = $this->data['_token'];
        $this->extract();
        dd($this);
    }

    protected function extract()
    {
        foreach($this->data as $key => $item) {
            if($key == 'structure' || $key == '_token') continue;
            $this->map($key, $item);
        }
    }

    protected function map($key, $item)
    {
        $descriptor = $this->getFieldDescriptors($key);
        if($this->isTitle($descriptor->key)) return $this->setTitle($descriptor, $item);
        if($this->isMeta($descriptor->key)) return $this->addMeta($descriptor, $item);
        $this->items[$descriptor->lang][$descriptor->key] = $item;
    }

    protected function getFieldDescriptors($key)
    {
        $parts = explode('|', $key);
        return (object) [
            'lang' => $parts[0],
            'key'  => $parts[1]
        ];
    }

    protected function isTitle($key)
    {
        return $key == 'kabas_title';
    }

    protected function setTitle($descriptor, $item)
    {
        $this->title[$descriptor->lang] = $item;
    }

    protected function isMeta($key)
    {
        return starts_with($key, 'meta#');
    }

    protected function addMeta($descriptor, $item)
    {
        $this->metas[$descriptor->lang][$descriptor->key] = $item;
    }

    public function __call($key, $args)
    {
        return $this->$key;
    }

}