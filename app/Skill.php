<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Config;

class Skill extends Model
{
    protected $hidden = ['user_id', 'create_at', 'updated_at','pivot'];
    protected $fillable = ['skill', 'description', 'track_id','image', 'status_id', 'user_id'];

    // Relationships
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function houses() {
        return $this->belongsToMany(House::class)->withPivot('start_date','end_date');
    }

    public function tracks() {
        return $this->belongsToMany(Track::class)->withPivot('start_date','end_date', 'skill_order')->withTimestamps()->select('id','track','description','level_id', 'field_id', 'skill_order');
    }
    //user's skill maxile score
    public function users(){
        return $this->belongsToMany(User::class)->withPivot('skill_test_date','skill_passed','skill_maxile','noOfTries','noOfPasses','difficulty_passed', 'noOfFails')->withTimestamps();
    }

    public function noOfTries($userid){
        return $this->users()->whereUserId($userid)->select('noOfTries')->first()->noOfTries;
    }

    public function noOfPasses($userid){
        return $this->users()->whereUserId($userid)->select('noOfPasses')->first()->noOfPasses;
    }

    public function difficulty_passed($userid){
        return $this->users()->whereUserId($userid)->select('difficulty_passed')->first()->difficulty_passed;
    }

    public function skill_maxile($userid){
        return $this->users()->whereUserId($userid)->select('skill_maxile')->first()->skill_maxile;
    }

    public function skill_passed(){
        return $this->users()->whereUserId(Auth::user()->id)->select('skill_passed', 'skill_test_date', 'difficulty_passed');
    }

    /** 
     * Determines if difficulty passed, skill passed calculate skill maxile
     * 
     */
    public function handleAnswer($userid, $difficulty, $correct, $track, $diagnostic) {
        $userSkill= $this->users()->whereUserId($userid)->select('noOfPasses', 'noOfTries', 'difficulty_passed','noOfFails','skill_maxile','skill_passed')->first();

        if ($userSkill) {
            $noOfTries = $userSkill->noOfTries + 1;
            $noOfPasses = $userSkill->noOfPasses;
            $noOfFails = $userSkill->noOfFails;
            $difficulty_passed = $userSkill->difficulty_passed;
            $skill_passed = $userSkill->skill_passed;
        } else {
            $noOfFails = $noOfPasses = $noOfTries = $difficulty_passed =$skill_passed =0;
        }

        if ($correct) {
            $difficulty_passed <= $difficulty ? $noOfPasses += 1 : 1;
            $noOfFails -= 1;
        }
        if (!$correct) {
            $noOfPasses = 0;
            $noOfFails += 1;
        }
        // determine difficulty passed level
        $difficulty_passed = $diagnostic ? $correct ? $difficulty : 0 : $noOfPasses >= Config::get('app.number_to_pass') ? $difficulty_passed < $difficulty ? $difficulty : $difficulty_passed : $difficulty_passed;
        $skill_passed = $difficulty_passed < Config::get('app.difficulty_levels') ? FALSE : TRUE;
        // calculate skill_maxile
        $skill_maxile = $difficulty_passed ? $skill_passed ? $track->level->end_maxile_level:$track->level->start_maxile_level+(100/Config::get('app.difficulty_levels')*$difficulty_passed) : 0; 
        $record = [
            'skill_test_date' => new DateTime('now'),
            'skill_passed' => $skill_passed,
            'difficulty_passed' => $difficulty_passed,
            'skill_maxile' => max($skill_maxile, $track->level->start_maxile_level),
            'noOfTries'=> $noOfTries,
            'noOfPasses'=>max(0,$noOfPasses),
            'noOfFails'=> max(0,$noOfFails)];
        $this->users()->updateExistingPivot($userid, $record);              //update record
        return $this->users()->select('skill_maxile')->first();
    }
}
