<?php 

namespace WhiteCube\Admin\Controllers;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ModelController extends BaseController {

    public function list($file)
    {
        return view('admin::models')->with([
            'model' => Admin::model($file)
        ]);
    }

    public function show($file, $id)
    {
        $item = call_user_func(Admin::model($file)->config->model . '::find', $id);
        $model = Admin::model($file);
        $model->setValues($item);
        return view('admin::model')->with([
            'model' => $model,
            'item' => $item
        ]);
    }

    public function process(Request $request)
    {
        dd($request->all());
    }

}