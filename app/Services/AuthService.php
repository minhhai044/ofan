<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    // public function register(array $data)
    // {
    //     $data['code_misa'] = codeMisa($data['name'], $data['phone']);
    //     $data['slug'] = generateSlug($data['name']);
    //     $user = User::create($data);
    //     Auth::login($user);
    //     return $user;
    // }

    // public function login(array $data)
    // {
    //     $remember = isset($data['remember']) ? true : false;
    //     if (Auth::attempt(Arr::except($data, ['remember']), $remember)) {
    //         return Auth::user();
    //     }
    //     return false;
    // }

    public function login(array $data)
    {
        $remember = !empty($data['remember']);

        $credentials = Arr::only($data, ['phone', 'password']);

        $credentials['is_active'] = 1;
        
        return Auth::attempt($credentials, $remember);
    }
}
