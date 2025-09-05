<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productCategoryService;
    protected $productService;
    public function __construct(ProductCategoryService $productCategoryService, ProductService $productService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->productService = $productService;
    }
    public function index(Request $request)
    {
        $products = $this->productService->getAllProduct(20, $request->all());
        $categories = $this->productCategoryService->getAllProductCategories(0, [], [], true);

        // dd($products);
        return view('admin.products.index', compact('products', 'categories'));
    }
    public function create()
    {
        $categories = $this->productCategoryService->getAllProductCategories(0, []);
        $list_filter = $this->productCategoryService->findProductCategory(['products'], 1);
        $list_accessory = $this->productCategoryService->findProductCategory(['products'], 2);

        return view('admin.products.create', compact('categories', 'list_filter', 'list_accessory'));
    }
    public function store(ProductRequest $request)
    {
        try {
            $this->productService->storeProduct($request->validated());
            return redirect()->route('products.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
    public function edit(string $slug)
    {
        $product = $this->productService->findProduct(['productFilters.productFilter', 'productAccessories.productAccessory'], '', $slug);
        $list_filter = $this->productCategoryService->findProductCategory(['products'], 1);
        $list_accessory = $this->productCategoryService->findProductCategory(['products'], 2);
        $categories = $this->productCategoryService->getAllProductCategories(0, []);

        return view('admin.products.edit', compact('product', 'list_filter', 'list_accessory', 'categories'));
    }
    public function update(ProductRequest $request, string $id)
    {
        try {
            $this->productService->updateProduct($request->validated(), $id);
            return redirect()->route('products.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
    public function updateStatus(Request $request, string $id)
    {
        try {
            $this->productService->updateProduct($request->all(), $id);
            return redirect()->route('products.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
    public function updateImage(Request $request, string $id)
    {
        try {
            $dataId = $request->input('data');
            if (!$dataId) {
                return back()->with('error', 'Không tìm thấy ID ảnh cần xóa !!!');
            }

            $this->productService->updateImage($dataId, $id);
            return true;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return false;
        }
    }
}
