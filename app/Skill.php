<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Config;

class Skill extends Model
{
    use RecordLog;

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
            'skill_maxile' => $skill_maxile,
            'noOfTries'=> $noOfTries,
            'noOfPasses'=>max(0,$noOfPasses),
            'noOfFails'=> max(0,$noOfFails)];
        $this->users()->updateExistingPivot($userid, $record);              //update record
        return $this->users()->select('skill_maxile')->first();
    }
    /* Checking if difficulty and skill cleared
     * 1. Retrieve current user information from skill_user
     * 2. If difficulty has been cleared before, getting question wrong resets it
     * 3. If passes difficulty for the first time, increment maxile
     * 4. If skill has been cleared before, getting question wrong resets it
     * 5. If passes skill for the first time, increment maxile
     * return int cleared_status : 0- nothing cleared, 1- difficulty cleared, 2- skill cleared 
      
    public function answerRight($userid, $difficultyid, $track, $max_skill_maxile, $maxile_earned){
        $userSkill= $this->users()->whereUserId($userid)->select('noOfPasses', 'noOfTries', 'difficulty_passed','noOfFails','skill_maxile','skill_passed')->first();
        $difficulty_cleared = $userSkill->noOfPasses + 1 < Config::get('app.number_to_pass') ? $userSkill->difficulty_passed : $userSkill->difficulty_passed+ 1;
        $cleared_status = $difficulty_cleared ? $difficultyid < Config::get('app.difficulty_levels') ? 1 : 2 : 0;

        $record = [
            'skill_test_date' => new DateTime('now'),
            'skill_passed' =>$cleared_status > 1 ? $userSkill->skill_passed +1 : $userSkill->skill_passed,
            'difficulty_passed' => $difficulty_cleared ,
            'skill_maxile' => $cleared_status > 1 ? $max_skill_maxile : $cleared_status ==1 ? $maxile_earned :0,
            'noOfTries'=> $userSkill->noOfTries + 1,
            'noOfPasses'=>$userSkill->noOfPasses + 1,
            'noOfFails'=> 0];
        $this->users()->updateExistingPivot($userid, $record);              //update record
        return $cleared_status;
    }

    public function answerWrong($user, $question, $track, $maxile_earned){
        $userSkill = $this->users()->whereUserId($user->id)->select('noOfPasses', 'noOfTries', 'noOfFails', 'difficulty_passed', 'skill_maxile', 'skill_passed')->first();
        $downgradeSkill = $userSkill->skill_passed > Config::get('app.number_to_fail') ? FALSE : TRUE;
        $downgradeDifficulty = $userSkill->difficulty_passed > Config::get('app.number_to_fail') ? FALSE: TRUE;
        $record = [
            'skill_test_date' => new DateTime('now'),
            'skill_passed' =>$downgradeSkill ? 0 : $userSkill->skill_passed -1,
            'difficulty_passed' => $downgradeDifficulty ? 0 : $userSkill->difficulty_passed -1,
            'skill_maxile' => $downgradeSkill ? max($userSkill->skill_maxile - $maxile_earned,0) : $userSkill->skill_maxile,
            'noOfTries'=> $userSkill->noOfTries + 1,
            'noOfPasses'=> 0,
            'noOfFails'=> $userSkill->noOfFails + 1];
        $this->users()->updateExistingPivot($user->id, $record);              //update record
        return $downgradeSkill ? -1 : 0;
    }
/*    public function passUser($difficultyid, $userid, $max_track_maxile, $maxile_earned){

        return $this->users()->updateExistingPivot($userid,[
            'skill_test_date'=>new DateTime('today'),
            'skill_passed' => TRUE,
            'difficulty_passed' => max($difficultyid, $this->difficulty_passed($userid)),
            'skill_maxile'=>max($this->maxile, $maxile_earned),
            'noOfTries'=> $this->noOfTries($userid)+1,
            'noOfPasses' => $this->noOfPasses($userid)+1]);
    }
*/}
