<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\ProductCategory;

class ProductCategoryService
{
    public function getAllProductCategories($paginate = 0, $filters = [], $relation = [], $is_active = false)
    {

        $query = ProductCategory::with($relation);

        $dateRangeable = ['page'];

        // Áp dụng các bộ lọc nếu có
        foreach ($filters as $field => $value) {
            if (isset($value) && $value !== '' && !in_array($field, $dateRangeable, true)) {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        // Nếu có is_active thì sẽ lọc những user đang kích hoạt
        if ($is_active) {
            $query->where('is_active', 1);
        }

        if ($paginate > 0) {
            return $query->paginate($paginate);
        } else {
            return $query->get();
        }
    }


    public function storeProductCategory(array $data)
    {
        $data['slug'] = generateSlug($data['name']);
        if (!empty($data['image'])) {
            $data['image'] = createImageStorage('ProductCategories', $data['image']);
        }
        return ProductCategory::query()->create($data);
    }

    public function findProductCategory(array $relation = [], string $id = '', string $slug = '')
    {
        $query = ProductCategory::with($relation);
        if (!empty($id)) {
            return $query->find($id);
        } elseif (!empty($slug)) {
            return $query->where('slug', $slug)->first();
        } else {
            return false;
        }
    }

    public function updateProductCategory(array $data, string $id)
    {
        $product_category = $this->findProductCategory([], $id);
        if (!empty($data['name'])) {
            $data['slug'] = generateSlug($data['name']);
        }
        if (!empty($data['image'])) {
            deleteImageStorage($product_category->image);
            $data['image'] = createImageStorage('ProductCategories', $data['image']);
        }
        return $product_category->update($data);
    }
}
