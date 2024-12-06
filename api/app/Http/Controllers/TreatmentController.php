<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTreatmentRequest;
use App\Http\Requests\StoreTreatmentRequest;
use App\Http\Requests\UpdateTreatmentRequest;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Mockery\Expectation;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::orderBy('order')->with('services.options')->get();

        return response()->json(['items' => $treatments]);
    }

    public function store(StoreTreatmentRequest $request)
    {
        $form = $request->only(['title', 'order', 'status']);

        Treatment::create($form)->save();

        $treatment = Treatment::latest()->first();

        return response()->json($treatment);
    }


    public function reorder(ReorderTreatmentRequest $request, Treatment $treatment)
    {
        $rows = $request->get('orders');

        foreach ($rows as $row)
            Treatment::find($row['id'])->update(['order' => $row['order']]);

        return $this->index($request, $treatment);
    }

    public function update(UpdateTreatmentRequest $request, Treatment $treatment)
    {
        $form = $request->only($treatment->fillable);

        $treatment->update($form);

        $treatment->refresh();

        return response()->json($treatment);
    }

    public function destroy(Treatment $treatment)
    {
        try {
            foreach ($treatment->services as $service)
                $service->options()->delete();

            $treatment->services()->delete();
            
            $treatment->delete();

            return response()->json(['message' => __('message.deleted-successfully')]);
        } catch (\Throwable $e) {
            return response()->json(['message' => __('message.using-this-item')], 400);
        }
    }
}
