<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexConsultantRequest;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function index(IndexConsultantRequest $request)
    {
        $date = $request->only('from', 'to');

        $period = collect($date)->values()->map(fn($d, $i) => Carbon::parse($d)
            ->setTimezone('Asia/Tehran')
            ->{$i ? 'endOfDay' : 'startOfDay'}()
            ->format('Y-m-d H:i:s'));

        $consultants = User::with([
            'patients.appointments.status:id,name'
        ])->whereHas('roles', function ($query) {
            $query->whereIn('name', ['phone-consultant', 'on-site-consultant']);
        })->with(['patients.appointments' => function ($query) use ($period) {
            $query->whereBetween('due_date', $period);
        }])->get();

        $days = [];
        $period = CarbonPeriod::create(...$period);
        foreach ($period as $date) {
            $days[jdate($date)->format('Y/m/d')] = jdate($date)->format('l');
        }

        $items = $consultants->map(function ($consultant) use ($days) {
            $data = [
                'name' => $consultant->name,
            ];

            foreach ($days as $key => $_) {
                $data[$key] = [
                    'total' => 0,
                    'visited' => 0,
                    'canceled' => 0,
                ];
            }

            foreach ($consultant->patients as $patient) {
                foreach ($patient->appointments as $appointment) {
                    $appointmentDate = jdate(Carbon::parse($appointment->getRawOriginal('due_date'))
                        ->setTimezone('Asia/Tehran'))->format('Y/m/d');

                    $data[$appointmentDate]['total']++;

                    $status = $appointment->toArray()['status'];
                    if (in_array($status['name'], ['in-person-visit', 'online-visit', 'under-treatment', 'treatment-completed', 'periodic-visit'])) {
                        $data[$appointmentDate]['visited']++;
                    } elseif (in_array($status['name'], ['canceled', 'refunded'])) {
                        $data[$appointmentDate]['canceled']++;
                    }
                }
            }

            return $data;
        })->toArray();

        $sum = ['name' => 'جمع'];
        foreach ($days as $key => $_) {
            $sum[$key] = [
                'total' => 0,
                'visited' => 0,
                'canceled' => 0,
            ];
        }

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name') {
                    foreach ($value as $k => $v) {
                        $sum[$key][$k] += $v;
                    }
                }
            }
        }

        $items[] = $sum;

        return response()->json(compact('items', 'days'));
    }
}
