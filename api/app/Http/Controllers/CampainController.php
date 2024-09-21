<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampainRequest;
use App\Http\Requests\UpdateCampainRequest;
use App\Models\Campain;
use Illuminate\Http\Request;

class CampainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campains = Campain::latest();

        if (true) {
            $user = auth()->user();
            $campains = $campains->where('user_id', $user->id);
        }

        $campains = $campains->paginate(10);

        return response()->json($this->paginate($campains));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampainRequest $request)
    {
        $form = $request->only(['title', 'desc', 'start_date', 'end_date', 'budget']);

        $user = auth()->user();

        $user->campains()->create($form)->save();

        $campain = $user->campains()->latest()->first();

        return response()->json($campain);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampainRequest $request, Campain $campain)
    {
        $form = $request->only($campain->fillable);

        $campain->update($form);

        $campain->refresh();

        return response()->json($campain);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campain $campain)
    {
        $campain->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
