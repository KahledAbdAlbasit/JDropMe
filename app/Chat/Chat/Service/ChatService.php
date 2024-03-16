<?php

namespace App\Chat\Chat\Service;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatService
{

    // function to send message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'content' => 'required',
        ]);
        $senderPhoneNumber = Auth::user()->phone;
        $receiverPhoneNumber = $request->input('receiver_id');

        //check if $senderPhoneNumber exist in database
        $isUserExistInDB = User::where('phone', $receiverPhoneNumber)->first();
        if(!isset($isUserExistInDB)) {
            return response()->json(
                ['message' => "The user with this phone number does not exist."],
                404
            );
        }

        $message = Message::create([
        'sender_id' => $senderPhoneNumber,
        'receiver_id' => $receiverPhoneNumber,
        'content' => $request->input('content'),
        ]);

        return response()->json(['message' => [
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'content' => $message->content,]], 201);
    }

    // function toget all message from database by receiver id
    public function getMessages(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $senderPhoneNumber = Auth::user()->phone;

        // check if number exist or not
        $isUserExistInDB = User::where('phone', $receiverId)->first();
        if(!isset($isUserExistInDB)) {
            return response()->json(
                ['message' => "The user with this phone number does not exist."],
                404
            );
        }

        $messages = Message::where(function ($query) use ($receiverId, $senderPhoneNumber) {
            $query->where('receiver_id', $receiverId);
        })->orderBy('created_at', 'asc')->get();

        // to get readed Messages for detected number
        $messages2 = Message::where(function ($query) use ($receiverId, $senderPhoneNumber) {
            $query->where(['read_at' => null])->update(['read_at' => now()]);
        })->orderBy('created_at', 'asc')->get();

        $message = $messages->map(function ($message) {
            return [
            'sender_id' => $message['sender_id'],
            'receiver_id' => $message['receiver_id'],
            'content' => $message['content'],
            'read_at' => $message->read_at
            ];
        });

        return response()->json(['messages' => $message], 200);

        // $messages = Message::where('receiver_id',$receiverId)->get();
        // return response()->json(['messages' => [$messages]], 200);
        //return response()->json(['message' => [$messages->sender_id,$messages->receiver_id,$messages->content,$messages->read_at,]], 201);

    }


    // function to get not read  messages from database by receiver id
    public function notRead(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $senderPhoneNumber = Auth::user()->phone;

        // check if number exist or not
        $isUserExistInDB = User::where('phone', $receiverId)->first();
        if(!isset($isUserExistInDB)) {
            return response()->json(
                ['message' => "The user with this phone number does not exist."],
                404
            );
        }

        // to get readed Messages for detected number
        $messages = Message::where(function ($query) use ($receiverId, $senderPhoneNumber) {
            $query->where('receiver_id', $receiverId)
            ->where(['read_at' => null]);
        })->orderBy('created_at', 'asc')->get();

        $message = $messages->map(function ($message) {
            return [
            'sender_id' => $message['sender_id'],
            'receiver_id' => $message['receiver_id'],
            'content' => $message['content'],
            'read_at' => $message->read_at
            ];
        });

        return response()->json(['messages' => $message], 200);

    }


    // function to get readed meesage from database by receiver id
    public function readed(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $senderPhoneNumber = Auth::user()->phone;

        // check if number exist or not
        $isUserExistInDB = User::where('phone', $receiverId)->first();
        if(!isset($isUserExistInDB)) {
            return response()->json(
                ['message' => "The user with this phone number does not exist."],
                404
            );
        }

        // to get readed Messages for detected number
        $messages = Message::where(function ($query) use ($receiverId, $senderPhoneNumber) {
            $query->where('receiver_id', $receiverId)
            ->whereNotNull('read_at');
        })->orderBy('created_at', 'asc')->get();

        // response
        $message = $messages->map(function ($message) {
            return [
            'sender_id' => $message['sender_id'],
            'receiver_id' => $message['receiver_id'],
            'content' => $message['content'],
            'read_at' => $message->read_at
            ];
        });

        return response()->json(['messages' => $message], 200);

    }
    public function verification() {}
}
