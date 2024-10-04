<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $fields = ['name', 'mobile', 'email'];

        $users = User::when($request->input('query'), function ($query, $value) use ($fields) {
            $query->whereAny($fields, 'like', "%{$value}%");
        })->when($request->input('roles'), function ($query, $roles) {
            $query->whereHas('roles', function (Builder $roleQuery) use ($roles) {
                $roleQuery->whereIn('id', $roles);
            });
        })->when($request->filled('status'), function ($query) use ($request) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN);
            $query->where('status', $status);
        });

        foreach ($fields as $field)
            $users->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, 'like', "%{$value}%");
            });

        $users = $users->with('roles:id,title')->latest()->paginate($rows);

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

    private function generatePassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$()_-';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 8; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];

        return $randomString;
    }
}
