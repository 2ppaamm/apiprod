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
        return count($this->questions) ? number_format($this->questions()->sum('correct')/count($this->questions) * 100, 2, '.', '') : 0;
    }

    public function fieldQuestions($user){
        $level = null;
        $questions = null;
        $message = '';        
        if (!count($this->uncompletedQuestions)) {    // no more questions
            if ($this->diagnostic) {                  // if diagnostic check new level, get qns
                if (count($this->questions)) {
                    if (!$user->maxile_level) {
                        $message = 'Completed test at beginning level. Please check if child is too young for course.';
                        return $this->completeTest($message, $user);
                    } else {  // end of diagnostic test
                        $level = Level::where('level', '=', round($user->calculateUserMaxile($this)/100)*100)->first();
                        if ($user->maxile_level > $level->start_maxile_level){
                            if (count($this->questions) == count($this->questions()->where('question_answered','>=','1')->get())) {
                                $message = "Diagnostic test completed";
                                return $this->completeTest($message, $user);
                            }
                        }
                    }
                } else $level = Level::find(2); // start of diagnostic test

                // get questions, then log track, assign question to user               
                foreach ($level->tracks as $track){
                    $new_question = Question::whereIn('skill_id', $track->skills->lists('id'))->whereDifficultyId(3)->inRandomOrder()->first();
                    if ($new_question){
                        $new_question->assigned($user, $this);
                        $track->users()->sync([$user->id], false);        //log tracks for user
                    }
                }

            } elseif (!count($this->questions)) {           // not diagnostic, new test
                $level = Level::whereLevel(round($user->maxile_level/100)*100)->first();  // get userlevel
                $new_questions = collect([]);
                $tracks_to_test = count($user->tracksFailed) ? !$level->tracks->intersect($user->tracksFailed) ? $level->tracks->intersect($user->tracksFailed) : $user->tracksFailed : $level->tracks;                         // test failed tracks
                if (count($tracks_to_test) < 2) {  // test 3 tracks a day
                    $next_level = Level::where('level','>',$level->level)->first();
                    $tracks_to_test->merge($next_level->tracks()->take(2-count($tracks_to_test))->get());
                } else $tracks_to_test = $tracks_to_test->take(2);
                // non diagnostic, log track_user
                foreach ($tracks_to_test as $track){
                    $track->users()->sync([$user->id], false);          //log tracks for user
                    $skills_to_test = $track->skills->intersect($user->skill_user()->whereSkillPassed(FALSE)->get());
                    $n = 0;
                    while (count($new_questions) < 10 && $n < count($skills_to_test)){
                        $difficulty_passed = $skills_to_test[$n]->users()->whereUserId($user->id)->first() ? $skills_to_test[$n]->users()->whereUserId($user->id)->select('difficulty_passed')->first()->difficulty_passed : 0;
                        //find 5 questions in the track that are not already fielded and higher difficulty if some difficulty already passed
                        $skill_questions = Question::whereSkillId($skills_to_test[$n]->id)->where('difficulty_id','>', $difficulty_passed)->whereNotIn('id', $user->myQuestions()->lists('question_id'))->take(5)->get();
                        $new_questions = $skill_questions ? $skill_questions->merge($new_questions) : $skill_to_test[$n]->pass();
                        $n++;           
                    }
                    if (!$new_questions) {
                        return $this->completeTest($message, $user);

                    } else {
                        foreach ($new_questions as $new_question){
                            $new_question ? $new_question->assigned($user, $this) : null;
                        }                        
                    }
                }
            }
        }        
        // when there are questions linked to test
        $questions = $this->uncompletedQuestions()->get();
        if (!count($questions)){                //no more questions unanswered
            if (!count($this->questions)){      // new test
                $message = 'Exceeding level... no question can be fielded. Please print this screen and contact administrator at info.all-gifted@gmail.com';
            }
            if (count($this->questions) < $this->questions()->sum('question_answered')){
                $message = 'Test ended successfully';
            }
            return $this->completeTest($message, $user);
        }
        // field unanswered questions
        $test_questions = count($questions)< 6 ? $questions : $questions->take(5);
        return response()->json(['message' => 'Request executed successfully', 'test'=>$this->id, 'questions'=>$test_questions, 'code'=>201]);
    }

    public function completeTest($message, $user){
        $attempts = $this->attempts($user->id);
        $attempts = $attempts ? $attempts->attempts : 1;
        $maxile = $user->calculateUserMaxile($this);
        $user->enrolclass($maxile);
        $user->game_level = $user->game_level + $this->questions()->sum('correct');  // add kudos
        $user->save();
        $this->testee()->updateExistingPivot($user->id, ['test_completed'=>TRUE, 'completed_date'=>new DateTime('now'), 'result'=>$result = $this->markTest($user->id), 'attempts'=> $attempts + 1]);
        return response()->json(['message'=>$message, 'test'=>$this->id, 'percentage'=>$result, 'score'=>$user->calculateUserMaxile($this), 'maxile'=> $user->calculateUserMaxile($this),''=>$user->game_level, 'code'=>206], 206);
    }
}