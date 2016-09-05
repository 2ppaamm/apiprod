<?php

use Illuminate\Database\Seeder;
use App\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unit = Unit::create ([
            'unit' =>'hours',
            'description'=> 'hours'
        ]);
        $unit = Unit::create ([
            'unit' =>'days',
            'description'=> '24 hours'
        ]);
        $unit = Unit::create ([
            'unit' =>'months',
            'description'=> '12 months in a year'
        ]);
        $unit = Unit::create ([
        	'unit' => 'years',
        	'description' => '12 months in a year'
        ]);
    }
}