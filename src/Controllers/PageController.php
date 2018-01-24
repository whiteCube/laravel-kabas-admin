<?php 

namespace WhiteCube\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use WhiteCube\Admin\Facades\Admin as Admin;
use WhiteCube\Admin\RequestBag;

class PageController extends BaseController
{
    public function show($route)
    {
        return view('admin::page')->with([
            'page' => Admin::page($route)
        ]);
    }

    public function process(Request $request)
    {
        $requestbag = new RequestBag($request);
        $page = Admin::page($requestbag->structure());
        $page->setValues($requestbag->items());
        $page->save();
        return redirect()->back();
    }
}
