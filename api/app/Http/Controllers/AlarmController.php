<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $role = $user->role->name;

        $date = Carbon::now()->setTimezone('Asia/Tehran');

        if ($role == 'phone-consultant') {
            $follow_ups = FollowUp::whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user->id);
            })->whereBetween('due_date', [
                $date->startOfDay()->format('Y-m-d H:i:s'),
                $date->format('Y-m-d H:i:s'),
            ])->count();

            $follow_up_backlogs = FollowUp::whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user->id);
            })->whereHas('status', function ($query) {
                $query->whereNot('name', 'pending');
            })->where('due_date', '<', $date->format('Y-m-d H:i:s'))->count();

            $appointments = Appointment::whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user->id);
            })->whereBetween('due_date', [
                $date->format('Y-m-d H:i:s'),
                $date->endOfDay()->format('Y-m-d H:i:s'),
            ])->count();

            $date = $date->subMonth(3);

            $periodic_visits = Appointment::whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user->id);
            })->whereHas('status', function (Builder $query) {
                $query->where('name', 'periodic-visit');
            })->whereBetween('updated_at', [
                $date->startOfDay()->format('Y-m-d H:i:s'),
                $date->endOfDay()->format('Y-m-d H:i:s'),
            ])->count();

            return response()->json([
                'items' => compact('follow_up_backlogs', 'follow_ups', 'appointments', 'periodic_visits')
            ]);
        } else if ($role == 'on-site-consultant') {
            $visits = Appointment::whereHas('status', function (Builder $query) {
                $query->whereIn('name', ['in-person-visit', 'online-visit']);
            })->count();

            return response()->json(['items' => compact('visits')]);
        } else if ($role == 'reception') {
            $appointments = Appointment::whereHas('status', function (Builder $query) {
                $query->where('name', 'appointment-set');
            })->count();

            $visits = Appointment::whereHas('status', function (Builder $query) {
                $query->whereIn('name', ['in-person-visit', 'online-visit']);
            })->count();

            return response()->json(['items' => compact('appointments', 'visits')]);
        }

        return response()->json('');
    }
}
