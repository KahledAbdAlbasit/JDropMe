<?php

namespace App\Http\Controllers\API;

trait ApiResponseTrait
{
    public function apiResponse($data=null , $message=null , $status=null)
    {
        $array = [
            'data' => $data,
            'messge' => $message,
            'status' => $status,
        ];

        return response($array);
    }
}
[
    ''=>'',
    ''=>'',
]

