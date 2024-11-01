<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvayRequest;
use App\Http\Requests\UpdateSurvayRequest;
use App\Models\Survay;
use Illuminate\Http\Request;

class SurvayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $survays = Survay::when($request->input('query'), function ($query, $value){
            $query->whereAny(['title', 'desc'], 'like', "%{$value}%");
        });

        $survays = $survays->latest()->paginate($rows);

        return response()->json($this->paginate($survays));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurvayRequest $request)
    {
        $form = $request->only(['title', 'desc', 'status']);

        Survay::create($form)->save();

        $survay = Survay::latest()->first();

        return response()->json($survay);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurvayRequest $request, Survay $survay)
    {
        $form = $request->only($survay->fillable);

        $survay->update($form);

        $survay->refresh();

        return response()->json($survay);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survay $survay)
    {
        $survay->questions()->delete();

        $survay->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
