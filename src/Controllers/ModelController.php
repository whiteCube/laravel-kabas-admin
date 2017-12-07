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
        $model = call_user_func(Admin::model($file)->config->model . '::find', $id);
        return view('admin::model')->with([
            'model' => Admin::model($file),
            'item' => $model
        ]);
    }

    public function process(Request $request)
    {
        dd($request->all());
    }

}