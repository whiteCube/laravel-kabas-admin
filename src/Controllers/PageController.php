<?php 

namespace WhiteCube\Admin\Controllers;

use Illuminate\Routing\Controller as BaseController;
use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Http\Request;

class PageController extends BaseController {

    public function show($file)
    {
        return view('admin::page')->with([
            'page' => Admin::page($file . '.json')
        ]);
    }

    public function process(Request $request)
    {
        $data = $this->getData($request);
        $page = Admin::page($request->structure);
        $page->setValues($data);
        $page->save();
        return redirect()->back();
    }

    protected function getData($request) 
    {
        $raw = $request->all();
        unset($raw['_token']);
        unset($raw['structure']);
        $data = [];
        foreach($raw as $key => $item) {
            $parts = explode('|', $key);
            $data[$parts[0]][$parts[1]] = $item;
        }
        return $data;
    }

}