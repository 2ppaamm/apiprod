<?php

use Illuminate\Database\Seeder;
use App\ResultType;

class ResultTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = ResultType::create ([
            'result_type' =>'first',
            'description'=> 'First Score'
        ]);
        $type = ResultType::create ([
            'result_type' =>'highest',
            'description'=> 'Highest Score'
        ]);
        $type = ResultType::create ([
            'result_type' =>'last',
            'description'=> 'Latest Score'
        ]);
        $type = ResultType::create ([
            'result_type' =>'average',
            'description'=> 'Average Score'
        ]);
    }
}
