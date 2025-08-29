<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers($paginate = 0, $filters = [], $relation = [], $is_active = false, $page = null)
    {

        $query = User::with($relation)->latest('id');

        $dateRangeable = ['created_at_start', 'created_at_end'];

        // Áp dụng các bộ lọc nếu có
        foreach ($filters as $field => $value) {


            if (!empty($value) && !in_array($field, $dateRangeable, true)) {
                $query->where($field, 'like', '%' . $value . '%');
            }


            if (!empty($value) && in_array($field, $dateRangeable, true)) {
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
}
