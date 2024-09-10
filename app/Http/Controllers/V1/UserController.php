<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(UserStoreRequest $request)
    {
        $user = new User();
        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {

        if ($request->has('role_id'))
        {
            $user->role_id = $request->role_id;
        }

        if ($request->has('name'))
        {
            $user->name = $request->name;
        }

        if ($request->has('email'))
        {
            $user->email = $request->email;
        }

        if ($request->has('password'))
        {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
