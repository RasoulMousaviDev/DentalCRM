<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Appointment;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DepositController extends Controller
{

    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $deposits = Deposit::with([
            'appointment' => fn($q) => $q->with('patient:id,firstname,lastname')->select('id', 'patient')
        ]);

        $deposits = $deposits->when($request->input('query'), function ($query, $value) {
            $query->whereHas('appointment.patient', function (Builder $query) use ($value) {
                $query->whereAny(['firstname', 'lastname'], 'like', "%{$value}%");
            })->orWhereAny(['payment_date', 'refund_date']);
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('appointment.patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('appointment.patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('payment_date'), function ($query, $payment_date) {
            $query->where('payment_date', 'like', "%{$payment_date}%");
        })->when($request->input('refund_date'), function ($query, $refund_date) {
            $query->where('refund_date', 'like', "%{$refund_date}%");
        })->when($request->input('amount'), function ($query, $amount) {
            $query->where('amount', $amount);
        })->when($request->input('status'), function ($query, $status) {
            $query->where('status', $status);
        });

        $deposits = $deposits->latest()->paginate($rows);

        return response()->json($this->paginate($deposits));
    }

    public function store(StoreDepositRequest $request)
    {
        $status = 6;

        $form = $request->only(['amount', 'payment_date']);

        $appointment = Appointment::find($request->get('appointment'));

        $appointment->update(compact('status'));

        $appointment->patient()->update(compact('status'));

        $appointment->deposits()->create($form)->save();

        $appointment = $appointment->deposits()
            ->with('appointment')
            ->with('appointment.patient:id,firstname,lastname')
            ->with('appointment.treatments:id,title')
            ->with('appointment.status:id,value,severity')
            ->latest()->first();

        return response()->json($appointment);
    }

    public function update(UpdateDepositRequest $request, Deposit $deposit)
    {
        $form = $request->only(['status']);

        if ($form['status'] == 14)
            $form['refund_date'] = Carbon::now()->toIso8601String();

        $deposit->update($form);

        $deposit->appointment()->update(compact('status'));

        return response()->json(['message' => __('messages.diposit-refunded')]);
    }
}
