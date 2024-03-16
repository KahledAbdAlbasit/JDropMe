<?php

namespace App\Http\Controllers\API;

use App\Chat\Users\Service\usersService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    private usersService $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(usersService $userService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','verification','logout']]);
        $this->userService = $userService;
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        return $this->userService->login($request);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        return $this->userService->register($request);
    }


    public function verification(Request $request)
    {
        return $this->userService->verification($request);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->userService->logout();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->userService->refresh();
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return $this->userService->userProfile();
    }
}
