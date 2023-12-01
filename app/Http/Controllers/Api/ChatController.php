<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Chat\ChatResource;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'send_to' => ['required', 'string', 'max:255'],
            'date' => ['required'],
            'time' => ['required'],
            'subject' => ['required', 'string']
        ]);

        DB::beginTransaction();

        try {
            $chat = Chat::create([
                'receiver' => $request->send_to,
                'date' => $request->date,
                'time' => $request->time,
                'subject' => $request->subject,
            ]);

            activity('create chat')
                ->causedBy(Auth::user()->id)
                ->performedOn($chat)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => "{$chat->subject}",
                    'activity' => 'Create Chat',
                ])
                ->log("Chat for :subject.receiver created successfully");

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'Chat Created!!',
                'data' => new ChatResource($chat)
            ], 200);

        } catch (\Exception $error) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }


    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
