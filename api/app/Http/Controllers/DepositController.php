<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Appointment;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositController extends Controller
{

    public function index()
    {

        $deposits = Deposit::with([
            'appointment' => fn($q) => $q->with([
                'patient' => fn($q) => $q->without([
                    'mobiles',
                    'city',
                    'province',
                    'leadSource',
                    'status'
                ])->select('id', 'name')
            ])->select('id', 'patient')
        ])->latest()->paginate(10);

        return response()->json($this->paginate($deposits));
    }

    public function store(StoreDepositRequest $request)
    {
        $form = $request->only(['amount', 'payment_date']);

        $appointment = Appointment::find($request->get('appointment'));

        $appointment->deposits()->create($form)->save();

        $appointment = $appointment->deposits()->with([
            'appointment' => fn($q) => $q->with([
                'patient' => fn($q) => $q->without([
                    'mobiles',
                    'city',
                    'province',
                    'leadSource',
                    'status'
                ])->select('id', 'name')
            ])->select('id', 'patient')
        ])->latest()->first();

        return response()->json($appointment);
    }

    public function update(UpdateDepositRequest $request, Deposit $deposit)
    {
        $form = $request->only(['status']);

        $form['refund_date'] = Carbon::now()->toIso8601String();

        $deposit->update($form);

        return response()->json(['message' => __('messages.diposit-refunded')]);
    }
}
