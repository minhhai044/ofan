<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


class HomeController extends Controller
{
    public function index()
    {
        $data = "Trần Minh Hải";
        dd(generateSlug($data));
        return view('admin.index');
    }
}
