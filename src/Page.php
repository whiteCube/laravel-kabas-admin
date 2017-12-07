<?php 

namespace WhiteCube\Admin;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Page {

    public $structure;
    public $file;
    protected $values;
    public $url;
    public $name;
    public $fields;
    public $title;
    public $meta;

    public function __construct($structure)
    {
        $this->structure = $structure;
        $this->file = str_replace('structures/', '', $structure);
        $this->url = str_replace('.json', '', $structure);
        $this->fields = $this->loadFields();
        $this->config = $this->fields->kabas;
        $this->name = $this->fields->kabas->name;
        unset($this->fields->kabas);
        $this->loadValues();
    }

    protected function loadFields()
    {
        $fields = json_decode(Storage::disk('admin_structures')->get($this->structure));
        foreach($fields as $key => $field) {
            if($key == 'kabas') { $fields->kabas = $field; continue; }
            $fields->$key = new Field($key, $field);
        }
        return $fields;
    }

    protected function loadValues()
    {
        $values = [];
        foreach(Admin::locales() as $locale) {
            $values[$locale] = $this->loadValue($locale);
            $this->setMetadata($values[$locale], $locale);
            $this->insertIntoField($values[$locale], $locale);
        }
    }

    protected function setMetadata($data, $locale)
    {
        $this->title[$locale] = $data->title;
        $this->meta[$locale] = $data->meta;
    }

    protected function insertIntoField($values, $locale)
    {
        foreach($values as $key => $value) {
            if(!isset($this->fields->$key)) continue;
            $this->fields->$key->setValue($value, $locale);
        }
    }

    protected function loadValue($locale)
    {
        return json_decode(Storage::disk('admin_values')->get($locale . '/static/' . $this->file));
    }

    public function value($key, $lang)
    {
        if(!isset($this->fields->$key)) return 'not found';
        return $this->fields->$key->value($lang) ?? 'not found';
    }

    public function setValues($values)
    {
        // TODO: Rework this to work with the Field class
        // foreach($this->values as $lang => $data) {
        //     $this->values[$lang] = (object) array_merge((array) $data, $values[$lang]);
        // }
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

    public function metaGroupStructure($lang)
    {
        $structure = [
            "title" => (object) [
                "type" => "text",
                "label" => "Title",
                "name" => $lang .'|title',
            ],
            "meta" => (object) [
                "type" => "group",
                "label" => "Meta",
                "options" => (object) []
            ]
        ];

        foreach($this->config->meta as $key => $field) {
            $structure['meta']->options->$key = $field;
        }

        return htmlentities(json_encode($structure));
    }

    public function metaGroupValues($lang)
    {
        $values = [
            "title" => $this->title[$lang]
        ];
        foreach($this->meta[$lang] as $key => $value) {
            $values["meta"][$key] = $value;
        }

        return htmlentities(json_encode($values));
    }

}