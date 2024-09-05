<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPhotoRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Patient;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index(IndexPhotoRequest $request)
    {
        $patient = $request->get('patient');

        $calls = Photo::where('patient_id', $patient)->latest()->paginate(10);

        return response()->json($this->paginate($calls));
    }

    public function store(StorePhotoRequest $request)
    {

        $form = $request->only(['title', 'desc']);

        $image = $request->file('image');

        $form['name'] = $image->getClientOriginalName();

        $patient = Patient::find($request->get('patient'));

        $photo =  $patient->photos()->create($form);

        $id = $photo->id;

        $photo->save();

        Storage::putFileAs("photos/", $image, $id . "_" . $form['name']);

        $photo = Photo::find($id);

        return response()->json($photo);
    }


    public function show(Photo $photo)
    {
        return Storage::download("/photos/" . $photo->id . "_" . $photo->name);
    }


    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        $form = $request->only(['title', 'desc']);

        if ($request->has('image')) {
            $image = $request->file('image');
            $form['name'] = $image->getClientOriginalName();

            Storage::delete('photos/' . $photo->id . "_" . $photo->name);
            Storage::putFileAs("photos/", $image, $photo->id . "_" . $form['name']);
        }

        $photo->update($form);

        $photo->refresh();

        return response()->json($photo);
    }

    public function destroy(Photo $photo)
    {
        Storage::delete('photos/' . $photo->id . "_" . $photo->name);

        $photo->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
