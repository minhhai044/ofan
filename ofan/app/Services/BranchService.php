<?php

namespace App\Services;

use App\Models\Branch;

class BranchService
{
    public function getAllBranches($filters = [], $relation = [], $is_active = false)
    {

        $query = Branch::with($relation);

        $dateRangeable = ['created_at_start', 'created_at_end'];

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
        return $query->get();
    }


    public function storeBranch(array $data)
    {
        $data['slug'] = generateSlug($data['name']);
        return Branch::query()->create($data);
    }

    public function findBranch(array $relation = [], string $id = '', string $slug = '')
    {
        $query = Branch::with($relation);
        if (!empty($id)) {
            return $query->find($id);
        } elseif (!empty($slug)) {
            return $query->where('slug', $slug)->first();
        } else {
            return false;
        }
    }

    public function updateBranch(array $data, string $id)
    {
        return Branch::query()->find($id)->update($data);
    }
}
