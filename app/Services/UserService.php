<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers($paginate = 0, $filters = [], $relation = [], $is_active = false, $page = null)
    {

        $query = User::with($relation)->latest('id');

        $dateRangeable = ['created_at_start', 'created_at_end', 'page'];

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



    /**
     * Hàm thêm mới users
     * createImageStorage là hàm helper tạo ảnh
     * @param array $data : Là các data được gửi lên
     */

    public function storeUsers(array $data)
    {
        if (!empty($data['avatar'])) {
            $data['avatar'] = createImageStorage('AvatarUsers', $data['avatar']);
        }
        if (!empty($data['bank_qr'])) {
            $data['bank_qr'] = createImageStorage('QrBanks', $data['bank_qr']);
        }
        if (!empty($data['branch_id'])) {
            $data['role'] = 1;
        }
        $data['slug'] = generateSlug($data['name']);
        return User::query()->create($data);
    }


    public function findUser(array $relation = [], string $id = '', string $slug = '')
    {
        $query = User::with($relation);
        if (!empty($id)) {
            return $query->find($id);
        } elseif (!empty($slug)) {
            return $query->where('slug', $slug)->first();
        } else {
            return false;
        }
    }


    public function updateUser(array $data, string $id)
    {
        $user = $this->findUser([], $id);
        if (!empty($data['avatar'])) {
            deleteImageStorage($user->avatar);
            $data['avatar'] = createImageStorage('AvatarUsers', $data['avatar']);
        }
        if (!empty($data['bank_qr'])) {
            deleteImageStorage($user->bank_qr);

            $data['bank_qr'] = createImageStorage('QrBanks', $data['bank_qr']);
        }

        if (array_key_exists('branch_id', $data)) {
            if (!empty($data['branch_id'])) {
                $data['role'] = 1;
            } elseif (is_null($data['branch_id'])) {
                $data['role'] = 0;
            }
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $user->update($data);
    }
}
