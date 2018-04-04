<?php 

namespace WhiteCube\Admin\Controllers;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Routing\Controller as BaseController;

class CustomController extends BaseController {

    /**
     * Show a custom page
     * @param string $route
     * @return View
     */
    public function show($route, $params = [])
    {
        if(is_string($params)) {
            $params = explode('/', $params);
        }
        $custom = Admin::customs()->get($route);
        $custom->run($params);
        return view('admin::custom')->with([
            'custom' => $custom
        ]);
    }

}