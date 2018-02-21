<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        User::create ([
            'name' =>'All Gifted',
            'firstName' =>'All Gifted',
            'lastName' => 'Administrator',
            'email'=> 'info.all-gifted@gmail.com',
            'password' => Hash::make('123456'),
            'is_admin' => TRUE,
            'date_of_birth' => '2002-02-20',
            'last_test_date' => $faker->dateTimeThisYear,
            'next_test_date' => $faker->dateTimeThisYear,
            'maxile_level' => 0,
            'image' =>'http://www.johndoe.pro/img/John_Doe.jpg'
        ]);
        User::create ([
            'name' =>'pamela',
            'firstName' =>'Pamela',
            'lastName' => 'Lim',
            'email'=> 'pamelaliusm@gmail.com',
            'password' => Hash::make('123456'),
            'is_admin' => TRUE,
            'date_of_birth' => '2009-02-20',
            'last_test_date' => $faker->dateTimeThisYear,
            'next_test_date' => $faker->dateTimeThisYear,
            'maxile_level' => 200,
            'image' =>'http://www.all-gifted.com/images/pamlim.jpg'

        ]);

        User::create ([
            'name' =>'japher',
            'firstName' =>'Japher',
            'lastName'  => 'Lim',
            'email'=> 'japher_lim@yahoo.com.sg',
            'password' => Hash::make('123456'),
            'is_admin' => TRUE,
            'date_of_birth' => '2009-03-29',
            'last_test_date' => $faker->dateTimeThisYear,
            'next_test_date' => $faker->dateTimeThisYear,
            'game_level' => 2500,
            'maxile_level' => 500,
            'image' => 'https://placeimg.com/50/50/people'
        ]);

/*        User::create ([
            'name' =>'Elvin',
            'firstName' =>'Elvin',
            'lastName' => 'Ong',
            'email'=> 'elvinong@thedottsolutions.com',
            'password' => Hash::make('123456'),
            'is_admin' => TRUE,
            'date_of_birth' => '1986-03-29',
            'last_test_date' => $faker->dateTimeThisYear,
            'next_test_date' => $faker->dateTimeThisYear,
            'maxile_level' => 0,
            'image' =>'http://www.johndoe.pro/img/John_Doe.jpg'
        ]);

        for ($i =0; $i<14; $i++){
            User::create([
                'name'=>$faker->firstName,
                'firstname' => $faker->firstName,
                'lastname' => $faker->lastName,
                'email'=> $faker->email,
                'password' => Hash::make('password'),
                'is_admin' => FALSE,
                'date_of_birth' => $faker->dateTimeBetween($startDate='-19 years', $endDate='-6 years'),
                'last_test_date' => $faker->dateTimeThisYear,
                'next_test_date' => $faker->dateTimeThisYear,
                'game_level' => $faker->randomNumber(4),
                'maxile_level' => $faker->randomNumber(3),
                'image' => 'https://placeimg.com/50/50/people'
            ]);
        }
*/    }
}
