<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $AuthService;
    public function __construct(AuthService $AuthService)
    {
        $this->AuthService = $AuthService;
    }
    public function form_login()
    {
        return view('auth.login');
    }
    public function form_register()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $this->AuthService->register($request->validated());
            return redirect()->route('home');
        } catch (\Throwable $th) {
            Log::error('Lỗi đăng ký: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Đăng ký không thành công');
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $this->AuthService->login($request->validated());
            return redirect()->route('home');
        } catch (\Throwable $th) {
            Log::error('Lỗi đăng nhập: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Đăng nhập không thành công');
        }
    }
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('form_login')->with('success', 'Đăng xuất thành công');
        } catch (\Throwable $th) {
            Log::error('Lỗi đăng xuất: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Đăng xuất không thành công');
        }
    }
}
