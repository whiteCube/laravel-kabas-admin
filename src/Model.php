<?php 

namespace WhiteCube\Admin;

use WhiteCube\Admin\Facades\Admin as Admin;
use Carbon\Carbon;

class Model
{
    use Pageable;

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
        $this->fields = $this->loadFields();
        $this->groups = $this->extractTabbedGroups();
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

    public function setValues($item)
    {
        foreach ($this->fields as $key => $field) {
            if ($key == 'kabas' || $key == 'translated') {
                continue;
            }
            $field->setValue($item->$key, 'shared');
        }
        foreach (Admin::locales() as $locale) {
            if (!isset($this->fields->translated)) {
                return;
            }
            foreach ($this->fields->translated as $key => $field) {
                $field->setValue($item->translate($locale)->$key, $locale);
            }
        }
    }

    public function save()
    {
        foreach ($this->values as $lang => $data) {
            Storage::values($lang, $file, $data);
        }
    }

    public function lastModified()
    {
        $timestamps = [];
        foreach (Admin::locales() as $locale) {
            $timestamps[$locale] = Storage::lastModified($locale, $file);
        }
        sort($timestamps);
        return Carbon::createFromTimestamp($timestamps[count($timestamps) - 1]);
    }

    public function sharedFields()
    {
        return array_diff_key((array) $this->fields, ['kabas' => '', 'translated' => '']);
    }

    public function hasSharedFields()
    {
        $count = 0;
        foreach ($this->fields as $key => $field) {
            if ($key == 'kabas' || $key == 'translated') {
                continue;
            }
            $count++;
        }
        return $count;
    }
}
