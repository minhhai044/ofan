<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductService
{


    public function getAllProduct($paginate = 0, $filters = [], $relation = [], $is_active = false, $page = null)
    {
        $query = Product::with($relation)->latest('id');

        $dateRangeable = ['page'];

        // Áp dụng các bộ lọc nếu có
        foreach ($filters as $field => $value) {


            if (isset($value) && $value !== '' && !in_array($field, $dateRangeable, true)) {
                $query->where($field, 'like', '%' . $value . '%');
            }


            if (isset($value) && $value !== '' && in_array($field, $dateRangeable, true)) {
                if ($field === 'created_at_start') {
                    $query->whereDate('created_at', '>=', $value);
                } elseif ($field === 'created_at_end') {
                    $query->whereDate('created_at', '<=', $value);
                }
            }
        }

        // Nếu có is_active thì sẽ lọc những user đang kích hoạt
        if ($is_active) {
            $query->where('is_active', 1);
        }

        // Nếu có paginate sẽ thực hiện phân trang
        if ($paginate > 0) {
            return $query->paginate($paginate);
        } else {
            if (is_null($page)) {
                return $query->get();
            }
            $limit = 20;
            $offset = $page * $limit;
            return $query->offset($offset)->limit($limit)->get();
        }
    }
    public function storeProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['images'] = [];
            $data['slug'] = generateSlug($data['name']);
            if (!empty($data['featured_images'])) {
                foreach ($data['featured_images'] ?? [] as $featured_images) {
                    $data['images'][] = [
                        'id' => generateSlugIds(),
                        'status' => 0,
                        'image' => createImageStorage('ProductImages', $featured_images)
                    ];
                }
            }

            if (!empty($data['featured_image'])) {

                $data['images'][] = [
                    'id' => generateSlugIds(),
                    'status' => 1,
                    'image' => createImageStorage('ProductImages', $data['featured_image'])
                ];
            }

            $product = Product::query()->create($data);

            if (!empty($filters)) {
                foreach ($filters as $filter) {
                    if ($filter->product_filter_id) {
                        $product->productFilters()->create([
                            'product_filter_id' => $filter->product_filter_id,
                            'maintenance_schedule' => $filter->maintenance_schedule,
                            'quantity' => $filter->quantity,
                            'is_active' => $filter->is_active,
                        ]);
                    }
                }
            }


            if (!empty($accessories)) {
                foreach ($accessories as $accessory) {
                    if ($accessory->product_accessory_id) {
                        $product->productAccessories()->create([
                            'product_accessory_id' => $accessory->product_filter_id,
                            'quantity' => $accessory->quantity,
                            'is_active' => $accessory->is_active,
                        ]);
                    }
                }
            }
        });
    }
}
