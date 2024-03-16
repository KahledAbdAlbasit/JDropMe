<?php

namespace App\Chat\Users\Service;

use App\Chat\Users\Requests\userRequest;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isNull;

class usersService{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if(Auth::user()->email_verified_at == null) {
            return response()->json(['error' => 'Your account not verified'], 401);
        } else {
            return $this->createNewToken($token);
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'info_code' => 'required|numeric|unique:users',
            'name' => 'required|string|between:2,100',
            'country_code' => 'required',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6',//|confirmed
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $infoCode = rand(100000, 999999);

        $verificationCode = Str::random(6);

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'info_code' => $infoCode,
                'password' => bcrypt($request->password),
                'verification_code' => $verificationCode,
            ]
        ));

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['access_token' => $token,'message' => 'تم ارسال الكود بنجاح', 'verificationCode' => $verificationCode]);
        // return response()->json(['user' =>
        //     [
        //     'info_code' => $user['info_code'],
        //     'name' => $user['name'],
        //     'phone' => '+'.$user['country_code'] .' '. $user['phone'],
        //     ]],
        //     200
        // );
    }


    public function verification(Request $request)
    {

        $validator = Validator::make($request->all(), [
                    'verification_code' => 'required',
                ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $verification_code = $request->verification_code;

        if(Auth::user()->verification_code == $verification_code) {
            $user = User::where(function ($query) use ($verification_code) {
                $query->where('verification_code', $verification_code)
                ->update(['email_verified_at' => now()]);

            })->orderBy('created_at', 'asc')->get();
            // return response()->json(["Your email verified"],200);
        } else {
            return response()->json([ "Wrong code"], 403);
        }

        if(!$user) {
            return response()->json([
                "message" => "Verification code is invalid."
            ], 403);
        } else {
            $this->logout();
            return response()->json(["Your email verified"], 200);
        }

        //return $this->createNewToken($token);

    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }



}
