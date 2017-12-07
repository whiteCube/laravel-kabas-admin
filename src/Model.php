<?php 

namespace WhiteCube\Admin;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Model {

    public $structure;
    public $file;
    protected $items;
    public $url;
    public $name;
    public $fields;

    public function __construct($structure)
    {
        $this->structure = $structure;
        $this->file = str_replace('models/', '', $structure);
        $this->url = str_replace('.json', '', $structure);
        $this->fields = json_decode(Storage::disk('admin_structures')->get($this->structure));
        $this->config = $this->fields->kabas;
        $this->name = $this->fields->kabas->name;
        $this->items = $this->loadItems();
    }

    protected function loadItems()
    {
        return call_user_func($this->config->model . '::all');
    }

    public function items()
    {
        return $this->items;
    }

    public function value($key, $lang)
    {
        return $this->values[$lang]->$key ?? 'not found';
    }

    public function setValues($values)
    {
        foreach($this->values as $lang => $data) {
            $this->values[$lang] = (object) array_merge((array) $data, $values[$lang]);
        }
    }

    public function save()
    {
        foreach($this->values as $lang => $data) {
            Storage::disk('admin_values')->put($lang . '/static/' . $this->file, json_encode($data));
        }
    }

    public function lastModified()
    {
        $timestamps = [];
        foreach(Admin::locales() as $locale) {
            $timestamps[$locale] = Storage::disk('admin_values')->lastModified($locale . '/static/' . $this->file);
        }
        sort($timestamps);
        return Carbon::createFromTimestamp($timestamps[count($timestamps) - 1]);
    }

    public function sharedFields()
    {
        return array_diff_key((array) $this->fields, ['kabas' => '', 'translated' => '']);
    }

}