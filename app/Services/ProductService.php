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

            if (!empty($data['filters'])) {
                foreach ($data['filters'] as $filter) {
                    if ($filter['product_filter_id']) {
                        $product->productFilters()->create([
                            'product_filter_id' => $filter['product_filter_id'],
                            'maintenance_schedule' => $filter['maintenance_schedule'],
                            'quantity' => $filter['quantity'],
                            'is_active' => $filter['is_active'],
                        ]);
                    }
                }
            }


            if (!empty($data['accessories'])) {
                foreach ($data['accessories'] as $accessory) {
                    if ($accessory['product_accessory_id']) {
                        $product->productAccessories()->create([
                            'product_accessory_id' => $accessory['product_accessory_id'],
                            'quantity' => $accessory['quantity'],
                            'is_active' => $accessory['is_active'],
                        ]);
                    }
                }
            }
        });
    }


    public function findProduct(array $relation = [], string $id = '', string $slug = '')
    {
        $query = Product::with($relation);
        if (!empty($id)) {
            return $query->find($id);
        } elseif (!empty($slug)) {
            return $query->where('slug', $slug)->first();
        } else {
            return false;
        }
    }


    public function updateImage(string $dataId, string $id)
    {
        $product = $this->findProduct([], $id);
        $updatedImages = [];

        foreach ($product->images ?? [] as $image) {
            if ($image['id'] != $dataId) {
                $updatedImages[] = $image;
            }
            deleteImageStorage($image['image']);
        }

        return $product->update([
            'images' => $updatedImages
        ]);
    }

    public function updateProduct(array $data, string $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $product = $this->findProduct(['productFilters', 'productAccessories'], $id);

            $data['slug'] = $product->slug;
            // if (!empty($data['featured_image'])) {
            //     $data['images'] = [];
            //     $featuredImagePath = createImageStorage('ProductImages', $data['featured_image']);
            //     $hasFeatured = false;

            //     foreach ($product->images ?? [] as $dataImage) {
            //         if ($dataImage['status'] == 1) {

            //             $data['images'][] = [
            //                 'id'    => $dataImage['id'],
            //                 'image' => $featuredImagePath,
            //                 'status' => 1
            //             ];
            //             $hasFeatured = true;
            //         } else {
            //             $data['images'][] = $dataImage;
            //         }
            //     }

            //     if (!$hasFeatured) {
            //         $data['images'][] = [
            //             'id'    => generateSlugIds(),
            //             'status' => 1,
            //             'image' => $featuredImagePath
            //         ];
            //     }
            // }

            // if (!empty($data['featured_images'])) {
            //     foreach ($data['featured_images'] ?? [] as $featured_images) {
            //         $data['images'][] = [
            //             'id' => generateSlugIds(),
            //             'status' => 0,
            //             'image' => createImageStorage('ProductImages', $featured_images)
            //         ];
            //     }
            // }
            // Khởi tạo từ ảnh hiện có (nếu lưu JSON)
            // Nếu $product->images là Eloquent Collection, hãy ->toArray() trước.

            $oldImages = $product->images ?? [];

            $newImages = [];

            if (!empty($data['featured_image'])) {
                $featuredPath = createImageStorage('ProductImages', $data['featured_image']);
                $updated = false;

                foreach ($oldImages as $img) {
                    if ((int)($img['status'] ?? 0) === 1) {
                        if ($img['image'] !== $featuredPath) {
                            deleteImageStorage($img['image']); // xoá file cũ
                        }
                        $newImages[] = [
                            'id'     => $img['id'],
                            'status' => 1,
                            'image'  => $featuredPath,
                        ];
                        $updated = true;
                    } else {
                        $newImages[] = $img;
                    }
                }

                if (!$updated) {
                    $newImages[] = [
                        'id'     => generateSlugIds(),
                        'status' => 1,
                        'image'  => $featuredPath,
                    ];
                }
            } else {
                $newImages = $oldImages;
            }

            // 2. Thêm gallery mới
            if (!empty($data['featured_images'])) {
                foreach ($data['featured_images'] as $file) {
                    if (empty($file)) continue;
                    $newImages[] = [
                        'id'     => generateSlugIds(),
                        'status' => 0,
                        'image'  => createImageStorage('ProductImages', $file),
                    ];
                }
            }

            // 3. Đảm bảo chỉ 1 ảnh featured
            $hasFeatured = false;
            foreach ($newImages as &$img) {
                if ((int)$img['status'] === 1 && !$hasFeatured) {
                    $hasFeatured = true;
                } else {
                    $img['status'] = 0;
                }
            }
            unset($img);

    
            $oldPaths = collect($oldImages)->pluck('image')->all();
            $newPaths = collect($newImages)->pluck('image')->all();

            foreach (array_diff($oldPaths, $newPaths) as $removedPath) {
                deleteImageStorage($removedPath);
            }
            $data['images'] = array_values($newImages);


            $product->update($data);

            if (!empty($data['filters'])) {
                $kept = [];

                foreach ($data['filters'] ?? [] as $f) {
                    if (empty($f['product_filter_id'])) continue;

                    $row = [
                        'maintenance_schedule' => (int)($f['maintenance_schedule'] ?? 0),
                        'quantity'             => (int)($f['quantity'] ?? 1),
                        'is_active'            => (int)($f['is_active'] ?? 1),
                    ];

                    $model = $product->productFilters()->updateOrCreate(
                        ['product_filter_id' => (int)$f['product_filter_id']],
                        $row
                    );

                    $kept[] = $model->id;
                }

                $product->productFilters()
                    ->when($kept, fn($q) => $q->whereNotIn('id', $kept))
                    ->delete();
            }


            if (!empty($data['accessories'])) {
                $kept_accessories = [];

                foreach ($data['accessories'] ?? [] as $f) {
                    if (empty($f['product_accessory_id'])) continue;

                    $row = [
                        'quantity'             => (int)($f['quantity'] ?? 1),
                        'is_active'            => (int)($f['is_active'] ?? 1),
                    ];

                    $model = $product->productAccessories()->updateOrCreate(
                        ['product_accessory_id' => (int)$f['product_accessory_id']],
                        $row
                    );

                    $kept_accessories[] = $model->id;
                }

                $product->productAccessories()
                    ->when($kept_accessories, fn($q) => $q->whereNotIn('id', $kept_accessories))
                    ->delete();
            }
        });
    }
}
