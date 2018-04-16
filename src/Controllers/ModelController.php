<?php 

namespace WhiteCube\Admin\Controllers;

use Carbon\Carbon;
use WhiteCube\Admin\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use WhiteCube\Admin\Facades\Admin;
use WhiteCube\Admin\Request\Bag as RequestBag;
use Illuminate\Routing\Controller as BaseController;

class ModelController extends BaseController
{
    public function list($route, Request $request)
    {
        $model = Admin::models()->get($route);

        $items = $model;
        
        if($request->search) {
            $sql = str_replace('%s', '%' . $request->search . '%', $model->structure()->search());
            // TODO: rechercher sur les colonnes qui sont dans "searchable" dans la structure
            $items = new Collection(DB::select($sql));
        } else {
            $items = $model->all();
        }

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
        if (in_array($key, Admin::locales())) {
            $this->addTranslatedValues($model, $item, $key, $value);
        } else {
            $value = new Value($value, $field->type);
            if($value->type() == 'date') $value->setRaw(Carbon::createFromFormat('d F Y', $value->get()));
            $item->$key = $value->get();
        }
    }

    protected function addTranslatedValues($model, $item, $locale, $values)
    {
        foreach ($values as $key => $value) {
            $field = $model->fields()->get($key);
            $value = new Value($value, $field->type);
            $item->translateOrNew($locale)->$key = $value->get();
        }
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
}
