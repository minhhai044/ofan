<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return view('admin.product_categories.index');
    }


    public function store(Request $request)
    {
        // Xử lý lưu danh mục sản phẩm
    }

    public function update(Request $request)
    {
        // Xử lý cập nhật danh mục sản phẩm
    }   
}
