<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Party;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------
        // Les différents partis politiques francophones sauf les Engagés
        // -----------------------------
        $ps = Party::updateOrCreate(
            ['short_name' => 'PS'],
            ['name' => 'Parti Socialiste']
        );

        $mr = Party::updateOrCreate(
            ['short_name' => 'MR'],
            ['name' => 'Mouvement Réformateur']
        );

        $ecolo = Party::updateOrCreate(
            ['short_name' => 'ECOLO'],
            ['name' => 'Ecolo']
        );

        $ptb = Party::updateOrCreate(
            ['short_name' => 'PTB'],
            ['name' => 'PTB']
        );

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
            $questionModels[] = Question::updateOrCreate(
                ['text' => $text],
                [
                    'category' => null,
                    'weight' => 1
                ]
            );
        }

        // -----------------------------
        // Les différentes positions des partis sur les questions que j'ai retenues
        // (-2 = pas d'accord, +2 = d'accord)
        // -----------------------------
        $psPositions = [2, 1, -1, 0, 2, 2, 2, 2, 0, 2];
        $mrPositions = [-1, -1, 2, 2, -1, -1, -1, 1, 2, -1];
        $ecoloPositions = [1, 2, -1, -1, 1, 1, 1, 2, -1, 1];
        $ptbPositions = [2, 1, -2, -1, 2, 2, 2, 2, -1, 2];

        foreach ($questionModels as $index => $question) {
            Position::updateOrCreate(
                [
                    'party_id' => $ps->id,
                    'question_id' => $question->id,
                ],
                [
                    'position_value' => $psPositions[$index]
                ]
            );

            Position::updateOrCreate(
                [
                    'party_id' => $mr->id,
                    'question_id' => $question->id,
                ],
                [
                    'position_value' => $mrPositions[$index]
                ]
            );

            Position::updateOrCreate(
                [
                    'party_id' => $ecolo->id,
                    'question_id' => $question->id,
                ],
                [
                    'position_value' => $ecoloPositions[$index]
                ]
            );

            Position::updateOrCreate(
                [
                    'party_id' => $ptb->id,
                    'question_id' => $question->id,
                ],
                [
                    'position_value' => $ptbPositions[$index]
                ]
            );
        }
    }
}
