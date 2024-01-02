<?php

namespace App\Http\Controllers;

use App\Events\GroupChatEvent;
use App\Http\Requests\Api\Chat\ChatRequest;
use App\Http\Resources\Api\Chat\ChatResource;
use App\Http\Resources\Api\GroupDetailsResource;
use App\Http\Resources\Api\GroupMemberResource;
use App\Http\Resources\Api\GroupResource;
use App\Http\Resources\Api\User\UserResource;
use App\Models\Chat;
use App\Models\Group;
use App\Trait\Authorizable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    use Authorizable;

    public function index(): AnonymousResourceCollection
    {
        $groups = Group::all();

        return GroupResource::collection($groups);
    }


    public function show($id): GroupDetailsResource
    {
        $group = Group::with([
            'chats' => function ($query) {
                $query->with('user.department')
                ->when(request('department_id') ?? false, function($query){
                    $query->whereHas('user',function($query){
                        $query->where('department_id',request('department_id'));
                    });
                });
            }
        ])->findOrFail($id);

        return new GroupDetailsResource($group);
    }


    public function members($id)
    {
        $group = Group::with([
            'users' => function($query){
                $query->with('department')
                ->when(request('department_id') ?? false, function($query){
                    
                $query->where('department_id',request('department_id'));
                    
                });
            }
        ])
        
        ->findOrFail($id);

        return GroupMemberResource::collection($group->users);
    }


    public function storeChat(ChatRequest $request, Group $group): JsonResponse
    {
        $chat = new Chat([
            'message' => $request->message,
            'user_id' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        $group->chats()->save($chat);


        activity("group chat created")
            ->causedBy(auth()->user())
            ->performedOn($group)
            ->withProperties([
                'ip'       => Auth::user()->last_login_ip,
                'activity' => "Group chat created",
                'target'   => "{$request->message}",
            ])
            ->log(":causer.name text in group {$request->message}.");

        GroupChatEvent::dispatch($group, $request->message);

        return response()->json([
            'status'  => 'success',
            'message' => 'successfully chat created',
            'chats'   => ChatResource::collection($group->chats()->get()),
        ], 200);
    }
}
