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

        $columns = ['name', 'mobile', 'email'];

        $users = User::when($request->input('query'), function ($query, $value) use ($columns) {
            $query->whereAny($columns, 'like', "%{$value}%");
        })->when($request->input('roles'), function ($query, $roles) {
            $query->whereHas('roles', function (Builder $query) use ($roles) {
                $query->whereIn('id', $roles);
            });
        })->when($request->input('status'), function ($query, $status) {
            $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
            $query->where('status', $status);
        });

        foreach ($columns as $column)
            $users->when($request->input($column), function ($query, $value) use ($column) {
                $query->where($column, 'like', "%{$value}%");
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
