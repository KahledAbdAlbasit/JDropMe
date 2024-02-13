<?php

namespace App\Chat\Users\Service;

use App\Chat\Users\Requests\userRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class usersService{
    public function register(userRequest $request)
    {
        $validator = Validator::make($request->all(), [

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]//bcrypt to password hash
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
}
