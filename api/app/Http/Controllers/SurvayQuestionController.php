<?php

namespace App\Http\Controllers;

use App\Models\Survay;
use Illuminate\Http\Request;

class SurvayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Survay $survay)
    {
        $questions = $survay->questions()->latest()->paginate(10);

        return response()->json($this->paginate($questions));
    }
}
