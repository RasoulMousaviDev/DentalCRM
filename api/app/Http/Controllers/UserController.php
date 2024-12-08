<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $users = User::when($request->input('id'), function ($query, $id) {
            $query->where('id', $id);
        })->when($request->input('role'), function ($query, $role) {
            $ids = Role::where('name','like', "%{$role}%")->pluck('id');
            $query->whereHas('roles', function (Builder $query) use ($ids) {
                $query->whereIn('id', $ids);
            });
        })->when($request->input('roles'), function ($query, $roles) {
            $query->whereHas('roles', function (Builder $query) use ($roles) {
                $query->whereIn('id', $roles);
            });
        })->when($request->input('status'), function ($query, $status) {
            $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
            $query->where('status', $status);
        });

        $columns = ['name', 'mobile', 'email'];

        foreach ($columns as $column)
            $users->when($request->input($column), function ($query, $value) use ($column) {
                $query->where($column, 'like', "%{$value}%");
            });

        $users->when($request->input('query'), function ($query, $value) use ($columns) {
            $query->whereAny($columns, 'like', "%{$value}%");
        });

        $dateFields = ['created_at', 'updated_at'];

        foreach ($dateFields as $field) {
            $users->when($request->input($field), function ($query, $value) use ($field) {
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }


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

        $form['role_id'] = $roles[0];

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

        in_array($user->role_id, $roles) || $form['role_id'] = $roles[0];

        $user->update($form);

        $user->refresh();

        $user->load('roles:id,title');

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $access = Auth::user()->hasRole('super-admin') && $user->id != auth()->id();

        if ($access) {
            $user->roles()->detach();

            $user->delete();

            return response()->json(['message' => __('messages.deleted-successfully')]);
        }

        return response()->json(['message' => __('messages.have-not-access')], 403);
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
