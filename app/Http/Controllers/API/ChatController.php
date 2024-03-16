<?php

namespace App\Http\Controllers\Api;

use App\Chat\Chat\Service\ChatService;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private ChatService $chatService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    // function to send message
    public function sendMessage(Request $request)
    {
        return $this->chatService->sendMessage($request);
    }

    // function toget all message from database by receiver id
    public function getMessages(Request $request)
    {
        return $this->chatService->getMessages($request);
    }


    // function to get not read  messages from database by receiver id
    public function notRead(Request $request)
    {
        return $this->chatService->notRead($request);
    }


    // function to get readed meesage from database by receiver id
    public function readed(Request $request)
    {
        return $this->chatService->readed($request);
    }
}
