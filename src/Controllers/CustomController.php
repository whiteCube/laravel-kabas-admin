<?php 

namespace WhiteCube\Admin\Controllers;

use WhiteCube\Admin\Facades\Admin as Admin;
use Illuminate\Routing\Controller as BaseController;

class CustomController extends BaseController {

    /**
     * Show a custom page
     * @param string $file
     * @return View
     */
    public function show($file)
    {
        $custom = Admin::custom($file);
        $custom->run();
        return view('admin::custom')->with([
            'custom' => $custom
        ]);
    }

}