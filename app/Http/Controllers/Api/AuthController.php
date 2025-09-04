<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());

            // Create Sanctum token for API authentication
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 'User registered successfully', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Registration error: ' . $th->getMessage());
            return $this->errorResponse('Registration failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function login(LoginRequest $request)
    {
        try {
            $payload = $request->validated() + [
                'remember' => $request->boolean('remember'),
            ];

            $ok = $this->authService->login($payload);

            if ($ok) {
                $user = Auth::user();

                /** @var \App\Models\MyUserModel $user **/
                $token = $user->createToken('auth_token')->plainTextToken;

                return $this->successResponse([
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ], 'Đăng nhập thành công!');
            }

            return $this->errorResponse('Thông tin đăng nhập không đúng hoặc tài khoản bị khóa.', Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $th) {
            Log::error('Lỗi đăng nhập: ' . $th->getMessage());
            return $this->errorResponse('Đăng nhập không thành công. Vui lòng thử lại.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            // Xóa access token hiện tại
            $currentToken = $user->currentAccessToken();
            if ($currentToken) {
                $currentToken->delete();
            }

            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('id', $currentToken->id) // chỉ xóa token này
                ->delete();

            return $this->successResponse([], 'Logout successful');
        } catch (\Throwable $th) {
            Log::error('Logout error: ' . $th->getMessage());
            return $this->errorResponse('Logout failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            return $this->successResponse([
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ], 'Profile retrieved successfully');
        } catch (\Throwable $th) {
            Log::error('Profile error: ' . $th->getMessage());
            return $this->errorResponse('Failed to retrieve profile', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
