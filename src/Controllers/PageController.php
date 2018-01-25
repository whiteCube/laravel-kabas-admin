<?php 

namespace WhiteCube\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use WhiteCube\Admin\Facades\Admin as Admin;
use WhiteCube\Admin\Request\Bag as RequestBag;

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
        $page = Admin::page($request->route);
        $bag = new RequestBag($request);
        $page->meta()->merge($bag->meta());
        $page->fields()->merge($bag->fields());
        dd($page);
        $page->save();
        return redirect()->back();
    }
}
