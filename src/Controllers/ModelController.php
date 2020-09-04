<?php

namespace WhiteCube\Admin\Controllers;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use WhiteCube\Admin\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use WhiteCube\Admin\Facades\Admin;
use WhiteCube\Admin\Request\Bag as RequestBag;
use App\Http\Controllers\Controller as BaseController;

class ModelController extends BaseController
{
    public function list($route, Request $request)
    {
        $model = Admin::models()->get($route);

        $items = $model;

        if($request->search) {
            $sql = str_replace('%s', '%' . $request->search . '%', $model->structure()->search());
            $sql = str_replace('%i', $request->search, $sql);
            if($model->structure()->order()) {
                $sql .= ' ORDER BY ' . $model->structure()->order();
            }
            $items = call_user_func($model->config()->model() . '::hydrate', DB::select($sql));
        } else {
            if($model->structure()->order()) {
                $items = $model->orderBy($model->structure()->order())->get();
            } else {
                $items = $model->get();
            }
        }

        $items = new LengthAwarePaginator($items->forPage($request->page, 25), count($items), 25, request()->page, [
            'path' => request()->url,
            'query' => request()->query()
        ]);

        $items = $this->getRelatedData($model, $items);

        return view('admin::models')->with([
            'model' => $model,
            'items' => $items
        ]);
    }

    public function getRelatedData($model, $items)
    {
        foreach($model->config()->columns() as $key => $column) {
            if(!isset($column->references)) continue;

            foreach($items as $item) {
                $parts = explode('@', $column->references);
                $classname = $parts[0];
                $col = $parts[1];
                $related = call_user_func($classname . '::find', $item->$key);
                $relatedColumn = $column->column;
                $item->$key = $related->$relatedColumn ?? '';
            }

        }

        return $items;
    }

    public function show($route, $id)
    {
        $model = Admin::models()->get($route);
        $item = $model->find($id)->first();
        $model->fields()->fill($item);
        return view('admin::model')->with([
            'model' => $model,
            'item' => $item
        ]);
    }

    public function process(Request $request)
    {
        $bag = new RequestBag($request);
        $bag->upload();
        $structure = str_replace('models/', '', $request->structure);
        $model = Admin::models()->get($structure);

        $this->runValidation($model->structure()->validation(), $request);

        // TODO: Do not hardcode 'id' as the primary key,
        // determine it in the json file instead.
        $item = $model->find($request->id)->first();

        foreach ($bag->fields() as $key => $value) {
            $this->fill($model, $item, $key, $value);
        }

        $item->save();
        return  redirect()->route('kabas.admin.model.item', ['file' => $structure, 'id' => $item->id]);
    }

    /**
     * Update a model's value
     * @param mixed $model
     * @param mixed $item
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function fill($model, $item, $key, $value)
    {
        $field = $model->fields()->get($key);

        if($field->type == 'password') {
            if(!$value) {
                return;
            }

            $value = bcrypt($value);
        }

        if (in_array($key, Admin::locales())) {
            $this->addTranslatedValues($model, $item, $key, $value);
        } else {
            if ($field->type == 'image' || $field->type == 'file') {
                if (isset($value['file']) && $file = $value['file'] && $this->isFile($value)) {
                    $path = $file->store('uploads', 'public');
                    $value['path'] = 'storage/' . $path;
                }
            }

            $raw = $value;
            $value = new Value($value, $field->type);

            if($value->type() == 'date') {
                if(!$value->empty()) {
                    $value->setRaw(Carbon::createFromFormat('d/m/Y - H:i', $raw));
                } else {
                    $value->setRaw(null);
                }
            }

            if($value->type() == 'checkbox') {
                if($value->get() == '1') $value->setRaw(true);
                else $value->setRaw(false);
            }

            if($value->type() == 'model') {
                $relation = class_basename($item->$key());


                if($relation == 'BelongsToMany') {
                    $item->save();
                    return $item->$key()->sync($value->get());
                } else if($relation == 'BelongsTo') {
                    return $item->$key()->associate($value->get());
                } else if($relation == 'HasOne' || $relation == 'HasMany') {
                    if(count($item->$key)) {
                        $item->$key->each(function($rel) use ($item, $field) {
                            $rel->{$field->structure->foreign} = null;
                            $rel->save();
                        });
                    }

                    $related = call_user_func($field->structure->model . '::find', $value->get());
                    return $item->$key()->save($related);
                }
            }

            if($value->type() == 'image' || $value->type() == 'file') {
                $value->setRaw(json_encode($value->get()));
            }

            $item->$key = $value->get();
        }
    }

    protected function addTranslatedValues($model, $item, $locale, $values)
    {
        foreach ($values as $key => $value) {
            $field = $model->fields()->get($key);
            if($field->type == 'flexible') {
                $value = $this->extractFiles($_REQUEST[$locale . '|' . $key]);
            }
            $value = new Value($value, $field->type);
            $item->translateOrNew($locale)->$key = $value->get();
        }
    }

    protected function extractFiles($json)
    {
        preg_match_all("/url\(data:?\w+\/(?<extension>\w+);(?<encoding>\w+),(?<data>[^\)]*)\)/", $json, $matches);
        foreach($matches[0] as $index => $match) {
            $image = base64_decode($matches['data'][$index]);
            $generated = $this->generateName($matches['extension'][$index]);
            $name = storage_path('app/public/uploads/' . $generated);
            file_put_contents($name, $image);
            $json = str_replace($match, 'uploads/' . $generated, $json);
        }
        return $json;
    }

    /**
     * Generate a hashed name
     * @param string $extension
     * @return string
     */
    protected function generateName($extension)
    {
        return sha1(microtime()) . '.' . $extension;
    }

    public function add($file)
    {
        $model = Admin::models()->get($file);
        $classname = $model->config()->model();
        $instance = new $classname;
        $translated = $instance->translatedAttributes ?? [];
        $shared = array_diff(array_keys($model->fields()->all()), $instance->translatedAttributes ?? []);

        return view('admin::add-model')->with([
            'model' => $model,
            'instance' => new $classname,
            'translated' => $translated,
            'shared' => $shared
        ]);
    }

    public function create($file, Request $request)
    {
        $bag = new RequestBag($request);
        $bag->upload();
        $structure = str_replace('models/', '', $request->structure);
        $model = Admin::models()->get($structure);

        $classname = $model->config()->model();
        $item = new $classname;

        $this->runValidation($model->structure()->validation(), $request);

        foreach ($bag->fields() as $key => $value) {
            $this->fill($model, $item, $key, $value);
        }

        $item->save();

        return redirect()->route('kabas.admin.model.item', ['file' => $structure, 'id' => $item->id]);
    }

    public function del($file, $id)
    {
        $model = Admin::models()->get($file);
        $item = $model->find($id)->first();
        $model->fields()->fill($item);
        return view('admin::modeldelete')->with([
            'model' => $model,
            'item' => $item
        ]);
    }

    public function destroy($file, $id)
    {
        $model = Admin::models()->get($file);
        $item = call_user_func($model->config()->model() . '::find', $id);
        $item->delete();
        $type = str_replace('.json', '', str_replace('models/', '', $model->structure()->file()));
        return redirect()->route('kabas.admin.model', ['file' => $type]);
    }

    public function runValidation($rules, $data)
    {
        $this->validate($data, $rules);
    }


    /**
     * Check if value is an uploaded file
     * @param mixed $value
     * @return boolean
     */
    protected function isFile($value)
    {
        return is_object($value) && str_contains(get_class($value), 'UploadedFile');
    }
}
