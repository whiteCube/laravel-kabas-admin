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
        $page = Admin::pages()->get($route);
        return view('admin::page', ['page' => $page]);
    }

    public function process(Request $request)
    {
        $page = Admin::pages()->get($request->route);
        $bag = new RequestBag($request);
        $bag->upload();
        $page->meta()->merge($bag->meta());
        $page->fields()->merge($bag->fields());
        $page->save();
        return redirect()->back();
    }
}
