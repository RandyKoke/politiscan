<?php

namespace App\Http\Controllers;

use App\Models\Question;

class QuestionController extends Controller
{
    public function index()
    {
        // On renvoie toutes les questions
        return response()->json(Question::all());
    }
}
