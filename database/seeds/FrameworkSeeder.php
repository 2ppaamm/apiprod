<?php

use Illuminate\Database\Seeder;

class FrameworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $framework = App\Framework::create ([
            'framework' =>'Maxile',
            'description'=> 'Mathematics framework created by All Gifted'
        ]);

        $framework = App\Framework::create ([
            'framework' =>'Lexile',
            'description'=> 'The Lexile Framework for Reading is an educational tool that uses a measure called a Lexile to match readers with books, articles and other leveled reading resources. Readers and books are assigned a score on the Lexile scale, in which lower scores reflect easier readability for books and lower reading ability for readers.'
        ]);
    }
}
