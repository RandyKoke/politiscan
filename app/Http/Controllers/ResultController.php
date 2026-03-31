<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Party;
use App\Models\Position;

class ResultController extends Controller
{
    public function calculate($sessionId)
    {
        $answers = Answer::where('session_id', $sessionId)->get();

        if ($answers->isEmpty()) {
            return response()->json([
                'error' => 'Aucune réponse trouvée'
            ], 404);
        }

        $parties = Party::all();
        $results = [];

        foreach ($parties as $party) {

            $score = 0;
            $maxQuestions = 0;

            foreach ($answers as $answer) {

                $position = Position::where('party_id', $party->id)
                    ->where('question_id', $answer->question_id)
                    ->first();

                if ($position) {

                    $diff = abs($answer->answer_value - $position->position_value);

                    // NORMALISATION (clé du fix)
                    $compatibility = 1 - ($diff / 4);

                    if ($compatibility < 0) {
                        $compatibility = 0;
                    }

                    $score += $compatibility;
                    $maxQuestions++;
                }
            }

            $percentage = ($maxQuestions > 0)
                ? ($score / $maxQuestions) * 100
                : 0;

            $results[] = [
                'party' => $party,
                'score' => (float) $percentage
            ];
        }

        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json($results);
    }
}
