<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\House;

class HouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        House::create ([
            'house' =>'system',
            'description' =>'system',
            'course_id'=>1,
            'user_id' => 1
        ]);
        House::create ([
            'house' =>'Flynn',
            'description' =>'Flynn Math B class',
            'user_id' => 1,
            'course_id'=>1,
            'start_date' => $faker->dateTimeThisYear,
            'end_date' => $faker->dateTimeThisYear,
        ]);
        House::create ([
            'house' =>'Campbell',
            'description' =>'Campbell Math B class',
            'user_id' => 1,
            'course_id'=>1,
            'start_date' => $faker->dateTimeThisYear,
            'end_date' => $faker->dateTimeThisYear,
        ]);
    }
}
