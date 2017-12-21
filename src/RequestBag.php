<?php 

namespace WhiteCube\Admin;

use Illuminate\Http\Request;

class RequestBag
{
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
    }

    protected function extract()
    {
        foreach ($this->data as $key => $item) {
            if ($key == 'structure' || $key == '_token') {
                continue;
            }
            $this->map($key, $item);
        }
    }

    protected function map($key, $item)
    {
        $descriptor = $this->getFieldDescriptors($key);
        if ($key == 'id') {
            return;
        }
        if ($this->isTitle($descriptor->key)) {
            return $this->setTitle($descriptor, $item);
        }
        if ($this->isMeta($descriptor->key)) {
            return $this->addMeta($descriptor, $item);
        }
        if ($descriptor->lang) {
            return $this->items[$descriptor->lang][$descriptor->key] = $item;
        }

        return $this->items[$descriptor->key] = $item;
    }

    protected function getFieldDescriptors($key)
    {
        $parts = explode('|', $key);
        $lang = count($parts) == 1 ? false : $parts[0];
        $key = count($parts) == 1 ? $parts[0] : $parts[1];
        
        return (object) [
            'lang' => $lang,
            'key'  => $key
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
