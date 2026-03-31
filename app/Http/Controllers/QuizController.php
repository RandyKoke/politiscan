<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionnaireSession;
use App\Models\Answer;

class QuizController extends Controller
{
    // Démarrer une session de questionnaire
    public function start(Request $request)
    {
        $session = QuestionnaireSession::create([
            'user_id' => $request->user_id,
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        return response()->json($session);
    }

    // Enregistrer une réponse
    public function answer(Request $request)
    {
        Answer::create([
            'session_id' => $request->session_id,
            'question_id' => $request->question_id,
            'answer_value' => $request->answer_value
        ]);

        return response()->json(['message' => 'Réponse enregistrée']);
    }
}
