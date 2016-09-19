<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Test extends Model
{
    use RecordLog;
    
    protected $hidden = ['user_id', 'created_at','updated_at','pivot'];
    protected $fillable = ['test', 'description', 'diagnostic', 'number_of_tries_allowed','start_available_time', 'end_available_time','due_time','which_result','image', 'status_id'];

    //relationship
    public function questions(){
        return $this->belongsToMany(Question::class, 'question_user')->withPivot('correct','question_answered','answered_date', 'attempts', 'user_id')->withTimestamps();
    }

    public function skills() {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    public function users(){
        return $this->belongsToMany(User::class, 'question_user')->withPivot('correct','question_answered','answered_date', 'attempts', 'question_id')->withTimestamps();
    }

    public function testee(){
        return $this->belongsToMany(User::class, 'test_user')->withPivot('test_completed', 'completed_date', 'result', 'attempts')->withTimestamps();
    }

    public function tester(){
        return $this->belongsTo(User::class);
    }

    public function houses(){
        return $this->belongsToMany(Test::class)->withTimestamps();
    }

    public function results(){
        return $this->morphMany(Result::class, 'assessment');
    }

    public function activities(){
        return $this->morphMany(Activity::class, 'classwork');
    }

    public function uncompletedQuestions(){
        return $this->questions()->whereQuestionAnswered(FALSE);
    }

    public function attempts($userid){
        return $this->users()->whereUserId($userid)->select('attempts')->first();
    }

    public function markTest($userid){
        return count($this->questions) ? $this->questions()->sum('correct')/count($this->questions) * 100 : 0;
    }

    public function fieldQuestions($user){
        $level = null;
        $questions = null;
        if (!count($this->uncompletedQuestions)) {    // no more questions
            if ($this->diagnostic) {                  // if diagnostic check new level, get qns
                if (count($this->question)) {
                    if (!$user->maxile_level) {
                        return response()->json(['message'=>'Completed test at lowest level', 'code'=>200], 200);
                    } else {
                        $level = Level::where('level', '>=', round($user->calculateUserMaxile($this)/100)*100)->first();
                    }
                } else $level = Level::find(2);
                // get question for each track in level                
                foreach ($level->tracks as $track){
                    $new_question = Question::whereIn('skill_id', $track->skills->lists('id'))->whereDifficultyId(3)->inRandomOrder()->first();
                    if ($new_question){
                        $new_question->assigned($user, $this);
 //                       $track->users()->sync([$user->id], false);        //log tracks for user
                    }
                }
return $user->questions;            } elseif (!count($this->questions)) {           // not diagnostic, new test
                $level = Level::whereLevel(round($user->maxile_level/100)*100)->first();  // get level
                $tracks_to_test = count($user->tracksFailed) ? !$level->tracks->intersect($user->tracksFailed) ? $level->tracks->intersect($user->tracksFailed) : $user->tracksFailed : $level->tracks; // test failed tracks, add 
                if (count($tracks_to_test) < 3) {
                    $next_level = Level::where('level','>',$level->level)->first();
                    $tracks_to_test->merge($next_level->tracks()->take(3-count($tracks_to_test))->get());
                } else $tracks_to_test = $tracks_to_test->take(3);
                foreach ($tracks_to_test as $track){
//                    $track->users()->sync([$user->id], false);          //log tracks for user
                    foreach ($track->skills->diff($user->skill_user()->whereSkillPassed(true)->get()) as $skill) {               // only test unpassed skills
                        $difficulty_passed = $skill->users()->whereUserId($user->id)->first() ? $skill->users()->whereUserId($user->id)->select('difficulty_passed')->first()->difficulty_passed : 0;
                        //find 5 questions in the track that are not already fielded and higher difficulty if some difficulty already passed
                        $new_questions = Question::whereIn('skill_id', $track->skills->lists('id'))->where('difficulty_id','>', $difficulty_passed)->whereNotIn('id', $user->myQuestions()->lists('question_id'))->take(5)->get();  
                        foreach ($new_questions as $new_question){
                            $new_question ? $new_question->assigned($user, $this) : null;
                        }           
                    }
                }
            }
        }
        // when there are questions linked to test
        $questions = $this->uncompletedQuestions()->get();
        if (!count($questions)){                //no question to test
            $attempts = $this->attempts($user->id);
            $attempts = $attempts ? $attempts->attempts : 0;
            if (!count($this->questions)){      // new test
                $this->testee()->updateExistingPivot($user->id, ['test_completed'=>TRUE, 'completed_date'=>new DateTime('now'), 'result'=>$result = $this->markTest($user->id), 'attempts'=> $attempts + 1]);
                return response()->json(['message'=>'Exceeding level... no question can be fielded. Please print this screen and contact administrator at info.all-gifted@gmail.com', 'test'=>$this->id, 'percentage'=>$result, 'score'=>$user->calculateUserMaxile($this), 'maxile'=> $user->calculateUserMaxile($this),'code'=>206], 206);
            }
            count($this->questions) < $this->questions()->sum('question_answered') ? null:
            $this->testee()->updateExistingPivot($user->id, ['test_completed'=>TRUE, 'completed_date'=>new DateTime('now'), 'result'=>$result = $this->markTest($user->id), 'attempts'=> $attempts + 1]);
            return response()->json(['message' => 'Test ended successfully', 'test'=>$this->id, 'percentage'=>$result, 'score'=>$user->calculateUserMaxile($this), 'maxile'=> $user->calculateUserMaxile($this), 'diagnostic', $user->diagnostic, 'code'=>206], 206);
        }
        if (!count($this->questions)) {
            return response()->json(['message'=>'No question can be fielded. Please print this screen and contact administrator at info.all-gifted@gmail.com', 'code'=>404], 404);
        }
        $test_questions = count($questions)< 6 ? $questions : $questions->take(5);
        return response()->json(['message' => 'Request executed successfully', 'test'=>$this->id, 'questions'=>$test_questions, 'code'=>201]);
    }
}