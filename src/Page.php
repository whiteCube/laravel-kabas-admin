<?php 

namespace WhiteCube\Admin;

use WhiteCube\Admin\Facades\Admin as Admin;
use Carbon\Carbon;

class Page {

    use Pageable;

    public $structure;
    public $file;
    protected $values;
    public $url;
    public $name;
    public $fields;
    public $title;
    public $meta;
    public $groups;
    protected $defaultFields = 'default-fields.json';

    public function __construct($structure)
    {
        $this->structure = $structure;
        $this->file = str_replace('structures/', '', $structure);
        $this->url = str_replace('.json', '', $structure);
        $this->fields = $this->loadFields();
        $this->groups = $this->extractTabbedGroups();
        $this->config = $this->fields->kabas;
        $this->name = $this->fields->kabas->name;
        unset($this->fields->kabas);
        $this->loadValues();
    }

    protected function loadValues()
    {
        foreach(Admin::locales() as $locale) {
            $values = Storage::values($locale, $this->file);
            $this->insertIntoFields((object) $values, $locale);
        }
    }

    protected function setMetadata($values, $locale)
    {
        $this->title[$locale] = $values->kabas_title;
        $this->meta[$locale] = $values->meta;
    }

    protected function insertIntoFields($values, $locale)
    {
        if(!isset($values->meta)) $values->meta = $this->extractMetaValues($values);
        $this->setMetadata($values, $locale);
        foreach($values as $key => $value) {
            if($key === 'kabas_title' || $key === 'meta') continue;
            if(isset($this->fields->$key)) $this->fields->$key->setValue($value, $locale);
            if(isset($this->groups->$key)) $this->groups->$key->setValue($value, $locale);
        }
    }

    protected function extractMetaValues($values)
    {
        $meta = [];
        foreach($values as $key => $value) {
            if(strpos($key, 'meta#') !== false) {
                $meta[str_replace('meta#', '', $key)] = $value;
            }
        }
        return $meta;
    }

    public function value($key, $lang)
    {
        return $this->fields->$key->value($lang) ?? 'not found';
    }

    public function setValues($values)
    {
        foreach($values as $lang => $data) {
            $this->insertIntoFields((object) $data, $lang);
        }
    }

    public function save()
    {
        foreach($this->getValues() as $lang => $data) {
            Storage::save($lang, $file, $data);
        }
    }

    protected function getValues()
    {
        $values = new \stdClass;
        foreach(Admin::locales() as $locale) {
            $values->$locale = new \stdClass;
            $values->$locale->kabas_title = $this->title[$locale];
            $values->$locale->meta = $this->meta[$locale];
            foreach(array_merge((array) $this->fields, (array) $this->groups) as $key => $field) {
                $values->$locale->$key = $field->value($locale);
            }
        }
        return $values;
    }

    public function lastModified()
    {
        $timestamps = [];
        foreach(Admin::locales() as $locale) {
            $timestamps[$locale] = Storage::lastModified($locale, $this->file);
        }
        sort($timestamps);
        return Carbon::createFromTimestamp($timestamps[count($timestamps) - 1]);
    }

    public function metaGroupStructure($lang)
    {
        $structure = json_decode(file_get_contents(__DIR__ . '/' . $this->defaultFields));
        $structure = $this->makeTranslated($structure, $lang);
        if(!isset($this->config->meta)) {
            unset($structure->meta);
            return htmlentities(json_encode($structure));
        }
        $meta = $this->makeTranslated($this->config->meta, $lang, 'meta');
        foreach($meta as $key => $field) {
            $structure->meta->options->$key = $field;
        }
        return htmlentities(json_encode($structure));
    }

    protected function makeTranslated($structure, $lang, $prefix = false)
    {
        foreach($structure as $key => $field) {
            $append = "$lang|";
            if($prefix) $append .= "$prefix#";
            $structure->$key->name = $append . $key;
        }
        return $structure;
    }

    public function metaGroupValues($lang)
    {
        $values = ["kabas_title" => $this->title[$lang]];
        foreach($this->meta[$lang] as $key => $value) {
            $values["meta"][$key] = $value;
        }
        return htmlentities(json_encode($values));
    }


    public function getRoute()
    {
        try {
            return route($this->url);
        } catch (\Exception $e) {
            return '';
        }
    }

}