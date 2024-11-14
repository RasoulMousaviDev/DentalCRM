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
        $treatments = Treatment::orderBy('order')->get();

        return response()->json(['items' => $treatments]);
    }

    public function store(StoreTreatmentRequest $request)
    {
        $form = $request->only(['title', 'cost', 'status']);

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
        $form = $request->only('status');

        $treatment->update($form);

        $treatment->refresh();

        return response()->json($treatment);
    }

    public function destroy(Treatment $treatment)
    {
        try {
            $treatment->delete();

            return response()->json(['message' => __('message.deleted-successfully')]);
        } catch (\Throwable $e) {
            return response()->json(['message' => __('message.using-this-item')], 400);
        }
    }
}
