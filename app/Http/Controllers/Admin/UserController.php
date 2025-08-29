<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers(1, [], []);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Xử lý lưu người dùng
    }

    public function edit(Request $request)
    {
        // Lấy thông tin người dùng cần chỉnh sửa
        return view('admin.users.edit');
    }

    public function update(Request $request)
    {
        // Xử lý cập nhật người dùng
    }
}
