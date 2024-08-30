<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    public function index()
    {
        $leadSources = LeadSource::all();

        return response()->json(['items' => $leadSources]);
    }
}
