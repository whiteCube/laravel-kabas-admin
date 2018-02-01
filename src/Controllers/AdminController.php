<?php 

namespace WhiteCube\Admin\Controllers;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController {

    /**
     * Show the admin index
     * @return View
     */
    public function index()
    {
        return view('admin::index')->with([
            'pages' => Admin::pages()->sorted(),
            'locales' => Admin::locales()
        ]);
    }

}