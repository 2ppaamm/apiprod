<?php

use Illuminate\Database\Seeder;
use App\Difficulty;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Difficulty::create ([
            'difficulty' =>1,
            'short_description' => '1 - Knowledge and Comprehension',
            'description'=> 'Recall of information; Discovery; Observation; Listing;
            Locating; Naming + Understanding; Translating; Summarising; Demonstrating; Discussing'
        ]);
        Difficulty::create ([
            'difficulty' =>2,
            'short_description' => '2 - Application and Analysis',
            'description'=> 'Using and applying knowledge; Using problem solving methods; Manipulating;
            Designing; Experimenting + Identifying and analyzing patterns; Organization of ideas; recognizing trends'
        ]);
        Difficulty::create ([
            'difficulty' =>3,
            'short_description' => '3 - Synthesis and Evaluation',
            'description'=> 'Using old concepts to create new ideas; Design and Invention;
            Composing; Imagining; Inferring; Modifying; Predicting; COmbining;
            + Assessing theories, Comparison of ideas; Evaluating outcomes; Solving;
            Judging; Recommending; Rating'
        ]);
    }
}
