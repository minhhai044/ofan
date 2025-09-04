<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
            $user = $this->authService->login($request->validated());

            if (!$user) {
                return $this->errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
            }

            // Create Sanctum token for API authentication
            /** @var \App\Models\MyUserModel $user **/
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 'Login successful');
        } catch (\Throwable $th) {
            Log::error('Login error: ' . $th->getMessage());
            return $this->errorResponse('Login failed', Response::HTTP_INTERNAL_SERVER_ERROR);
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
            // Delete current access token
            $request->user()->currentAccessToken()->delete();

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
