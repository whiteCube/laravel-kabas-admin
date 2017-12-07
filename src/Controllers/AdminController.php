<?php 

namespace WhiteCube\Admin\Controllers;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController {

    public function index()
    {
        return view('admin::index')->with([
            'pages' => Admin::pages(),
            'locales' => Admin::locales()
        ]);
    }

}