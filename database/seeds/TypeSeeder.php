<?php

use Illuminate\Database\Seeder;
use App\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = Type::create ([
            'type' =>'MCQ',
            'description'=> 'Multiple choice questions'
        ]);
        $type = Type::create ([
            'type' =>'Number',
            'description'=> 'Fill in the blanks with numbers'
        ]);
        $type = Type::create ([
            'type' =>'DAD',
            'description'=> 'Drag and drop'
        ]);
        $type = Type::create ([
            'type' =>'FIB',
            'description'=> 'Fill in the Blanks'
        ]);
        $type = Type::create ([
            'type' =>'MS',
            'description'=> 'Multi-select'
        ]);
        $type = Type::create ([
            'type' =>'Essay',
            'description'=> 'Essay'
        ]);
    }
}
