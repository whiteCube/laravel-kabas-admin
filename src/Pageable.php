<?php 

namespace WhiteCube\Admin;

trait Pageable {

    protected function loadFields()
    {
        $fields = Storage::structure($this->structure);
        foreach($fields as $key => $field) {
            if($key == 'kabas' || $key == 'translated') continue;
            $fields->$key = new Field($key, $field);
        }
        foreach($fields->translated ?? [] as $key => $field) {
            $fields->translated->$key = new Field($key, $field);
        }
        return $fields;
    }

    protected function extractTabbedGroups()
    {
        $tabbed = new \stdClass;
        foreach($this->fields as $key => $field) {
            if($key == 'kabas' || $key == 'translated' || !$field->isTabbedGroup()) continue;
            $tabbed->$key = $field;
            unset($this->fields->$key);
        }
        return $tabbed;
    }

}