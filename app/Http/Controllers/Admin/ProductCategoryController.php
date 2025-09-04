<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategoryRequest;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    protected $productCategoryService;
    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }
    public function index(Request $request)
    {
        $product_categories = $this->productCategoryService->getAllProductCategories(20, $request->all());

        return view('admin.product_categories.index', compact('product_categories'));
    }

    public function create()
    {
        return view('admin.product_categories.create');
    }

    public function store(ProductCategoryRequest $request)
    {
        try {
            $this->productCategoryService->storeProductCategory($request->validated());
            return redirect()->route('product_categories.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }

    public function edit(string $slug)
    {
        $product_category = $this->productCategoryService->findProductCategory([], '', $slug);
        return view('admin.product_categories.edit', compact('product_category'));
    }
    public function update(ProductCategoryRequest $request, string $id)
    {
        try {
            $this->productCategoryService->updateProductCategory($request->validated(), $id);
            return redirect()->route('product_categories.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
    public function updateStatus(Request $request, string $id)
    {
        try {
            $this->productCategoryService->updateProductCategory($request->all(), $id);
            return back()->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
}
