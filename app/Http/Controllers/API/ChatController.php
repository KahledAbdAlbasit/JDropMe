<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'content' => 'required',
        ]);
            $senderPhoneNumber = Auth::user()->phone;
            $receiverPhoneNumber = $request->input('receiver_id');
            //check if $senderPhoneNumber exist in database
            $isUserExistInDB = User::where('phone',$receiverPhoneNumber)->first();
            if(!isset($isUserExistInDB)){
                return response()->json(['message'=>"The user with this phone number does not exist."],
                404);
            }

            $message = Message::create([
            'sender_id' => $senderPhoneNumber,
            'receiver_id' => $receiverPhoneNumber,
            'content' => $request->input('content'),
        ]);

        return response()->json(['message' => $message], 201);
    }

    public function getMessages(Request $request)
{
    $receiverId = $request->input('receiver_id');
    $senderPhoneNumber = Auth::user()->phone;

    $messages = Message::where(function ($query) use ($receiverId,$senderPhoneNumber) {
        $query->where('receiver_id', $receiverId);
    
    })->orderBy('created_at', 'asc')->get();
       // $messages = Message::where('receiver_id',$receiverId)->get();
    return response()->json(['messages' => $messages], 200);
}
}
