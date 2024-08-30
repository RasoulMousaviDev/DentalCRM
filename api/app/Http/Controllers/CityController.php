<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCityRequest;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(IndexCityRequest $request)
    {
        $province = Province::find($request->get('province'));

        $cities = $province->cities;

        return response()->json(['items' => $cities]);
    }
}
