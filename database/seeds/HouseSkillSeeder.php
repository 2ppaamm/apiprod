<?php

use App\House_skill;
use Illuminate\Database\Seeder;
use App\Skill;
use App\House;
use App\Track;
use App\Level;

class HouseSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$level = Level::create([
    		'level' => 'level1',
    		'description' => 'Just testing level1',
    		]);
    	
    	$track = Track::create([
    		'track' => 'Track1',
    		'description' => 'Just testing track1',
    		]);

    	$skill = Skill::create([
    		'skill' => 'Skill1',
    		'description' => 'Just testing skill1',
    		'track_id' =>1,
    		'level_id' =>1
    		]);

        $house_skill = House_skill::create ([
            'house_id' =>1,
            'skill_id'=> 1,
            'start_date' => '2016-03-01',
            'end_date' => '2016-03-08'
        ]);
    }
}
