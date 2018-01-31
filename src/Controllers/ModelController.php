<?php 

namespace WhiteCube\Admin\Controllers;

use Carbon\Carbon;
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
        $requestbag = new RequestBag($request);
        $structure = str_replace('models/', '', $request->structure);
        $model = Admin::model($structure);
        $item = call_user_func($model->config->model . '::find', $request->id);
        foreach ($requestbag->fields() as $key => $value) {
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

    protected function addTranslatedValues($item, $locale, $values)
    {
        foreach ($values as $key => $value) {
            $item->translateOrNew($locale)->$key = $value;
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
        $model = Admin::model($file);
        $item = call_user_func($model->config->model . '::find', $id);
        $item->delete();
        return redirect()->route('kabas.admin.model', ['file' => $model->file]);
    }
}
