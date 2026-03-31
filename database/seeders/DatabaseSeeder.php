<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\Party;
use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // -----------------------------
        // PARTIS POLITIQUES
        // -----------------------------

        $ps = Party::create([
            'name' => 'Parti Socialiste',
            'short_name' => 'PS',
        ]);

        $mr = Party::create([
            'name' => 'Mouvement Réformateur',
            'short_name' => 'MR',
        ]);

        $ecolo = Party::create([
            'name' => 'Ecolo',
            'short_name' => 'ECOLO',
        ]);

        $ptb = Party::create([
            'name' => 'PTB',
            'short_name' => 'PTB',
        ]);

        // -----------------------------
        // QUESTIONS
        // -----------------------------

        $questions = [
            "L'État doit augmenter les impôts pour financer les services publics",
            "La Belgique doit sortir du nucléaire",
            "Il faut réduire les aides sociales",
            "L'immigration doit être davantage contrôlée",
            "L'État doit intervenir davantage dans l'économie",
            "Il faut augmenter le salaire minimum",
            "Les entreprises doivent payer plus d'impôts",
            "La transition écologique doit être une priorité",
            "La sécurité doit être renforcée même si cela limite certaines libertés",
            "L'enseignement doit être davantage financé par l'État"
        ];

        $questionModels = [];

        foreach ($questions as $text) {
            $questionModels[] = Question::create([
                'text' => $text
            ]);
        }

        // -----------------------------
        // POSITIONS DES PARTIS
        // (-2 = pas d'accord, +2 = d'accord)
        // -----------------------------

        foreach ($questionModels as $index => $question) {

            // PS
            Position::create([
                'party_id' => $ps->id,
                'question_id' => $question->id,
                'position_value' => [2, 1, -1, 0, 2, 2, 2, 2, 0, 2][$index]
            ]);

            // MR
            Position::create([
                'party_id' => $mr->id,
                'question_id' => $question->id,
                'position_value' => [-1, -1, 2, 2, -1, -1, -1, 1, 2, -1][$index]
            ]);

            // ECOLO
            Position::create([
                'party_id' => $ecolo->id,
                'question_id' => $question->id,
                'position_value' => [1, 2, -1, -1, 1, 1, 1, 2, -1, 1][$index]
            ]);

            // PTB
            Position::create([
                'party_id' => $ptb->id,
                'question_id' => $question->id,
                'position_value' => [2, 1, -2, -1, 2, 2, 2, 2, -1, 2][$index]
            ]);
        }
    }
}
