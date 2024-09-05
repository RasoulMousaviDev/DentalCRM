<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->with('roles:id,title')->paginate(10);

        return response()->json($this->paginate($users));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $form = $request->only(['name', 'mobile', 'email', 'status']);

        $form['password'] = $this->generatePassword(8);

        $roles = $request->get('roles');

        $user = User::create($form);

        $user->roles()->attach($roles);

        $user->load('roles:id,title');

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $form = $request->only(['name', 'mobile', 'email', 'status']);

        $user->roles()->detach();

        $roles = $request->get('roles');

        $user->roles()->attach($roles);

        $user->update($form);

        $user->refresh();

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->roles()->detach();

        $user->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);

    }

    private function generatePassword($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$()_-';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 8; $i++) 
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        
        return $randomString;
    
    }
}
