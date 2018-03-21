<?php 

namespace WhiteCube\Admin\Controllers;

use Carbon\Carbon;
use WhiteCube\Admin\Value;
use Illuminate\Http\Request;
use WhiteCube\Admin\Facades\Admin;
use WhiteCube\Admin\Request\Bag as RequestBag;
use Illuminate\Routing\Controller as BaseController;

class ModelController extends BaseController
{
    public function list($route)
    {
        $model = Admin::models()->get($route);
        if(request()->search) {
            // TODO: rechercher sur les colonnes qui sont dans "searchable" dans la structure
        }
        $items = $model->all();
        return view('admin::models')->with([
            'model' => $model,
            'items' => $items
        ]);
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
        $model = Admin::model($file);
        return view('admin::add-model')->with([
            'model' => $model
        ]);
    }

    public function create($file, Request $request)
    {
        $requestbag = new RequestBag($request);
        $structure = str_replace('models/', '', $requestbag->structure());
        $model = Admin::model($structure);
        $item = new $model->config->model;
        foreach ($requestbag->items() as $key => $value) {
            if (isset($model->fields->$key) && $model->fields->$key->type == 'date') {
                $value = Carbon::createFromFormat('d F Y', $value);
            }
            if (in_array($key, Admin::locales())) {
                $this->addTranslatedValues($item, $key, $value);
            } else {
                $item->$key = $value;
            }
        }
        $item->save();
        return  redirect()->route('kabas.admin.model.item', ['file' => $structure, 'id' => $item->id]);
    }

    public function destroy($file, $id)
    {
        $model = Admin::models()->get($file);
        $item = call_user_func($model->config()->model() . '::find', $id);
        dd($item);
        $item->delete();
        return redirect()->route('kabas.admin.model', ['file' => $model->structure()->file()]);
    }
}
