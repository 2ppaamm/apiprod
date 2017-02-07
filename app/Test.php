<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Test extends Model
{
    use RecordLog;
    
    protected $hidden = ['user_id', 'created_at','updated_at'];
    protected $fillable = ['test', 'description', 'diagnostic', 'number_of_tries_allowed','start_available_time', 'end_available_time','due_time','result','image', 'status_id'];

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
        return count($this->questions()->get()) ? number_format($this->questions()->sum('correct')/count($this->questions()->get()) * 100, 2, '.', '') : 0;
    }

    public function fieldQuestions($user){
        $level = null;
        $questions = collect([]);
        $message = '';
        if (!count($this->uncompletedQuestions)) {    // no more questions
            if ($this->diagnostic) {                  // if diagnostic check new level, get qns
                if (count($this->questions)) {
                    $level = Level::where('level', '=', round($user->calculateUserMaxile($this)/100)*100)->first();
                    if ($user->maxile_level > $level->start_maxile_level){
                       if (count($this->questions) == count($this->questions()->where('question_answered','>=','1')->get())) {
                            $message = "Diagnostic test completed";
                            return $this->completeTest($message, $user);
                        }                        
                    }
                } else $level = Level::find(2); // start of diagnostic test
                // get questions, then log track, assign question to user               
                foreach ($level->tracks as $track) {  //diagnostic => 1 track 1 question
                    $questions = $questions->merge(Question::whereIn('skill_id', $track->skills->lists('id'))->orderBy('difficulty_id','desc')->inRandomOrder()->take(1)->get()); 
                    $track->users()->sync([$user->id], false);        //log tracks for user
                }              

            } elseif (!count($this->questions)) {           // not diagnostic, new test
                $level = max(min(Level::find(7), Level::whereLevel(round($user->maxile_level/100)*100)->first()), Level::find(2));  // get userlevel
                $user->testedTracks()->sync($level->tracks()->lists('id')->toArray(), false);
                $tracks_to_test = count($user->tracksFailed) ? !$level->tracks->intersect($user->tracksFailed) ? $level->tracks->intersect($user->tracksFailed) : $user->tracksFailed : $level->tracks;                         // test failed tracks
                if (count($tracks_to_test) < 3) {  
                    $next_level = Level::where('level','>',$level->level)->first();
                    $tracks_to_test = $tracks_to_test->merge($next_level->tracks()->get());
                }
                $i = 0;
                while (count($questions) < 21 && $i < count($tracks_to_test)) {
                    $tracks_to_test[$i]->users()->sync([$user->id], false);          //log tracks for user
                    $skills_to_test = $tracks_to_test[$i]->skills()->lists('id')->toArray();               
                    $user->skill_user()->sync($skills_to_test, false);
                    $skills_to_test = $tracks_to_test[$i]->skills->intersect($user->skill_user()->whereSkillPassed(FALSE)->get());
                    $n = 0;
                    while (count($questions) < 20 && $n < count($skills_to_test)){
                        $difficulty_passed = $skills_to_test[$n]->users()->whereUserId($user->id)->first() ? $skills_to_test[$n]->users()->whereUserId($user->id)->select('difficulty_passed')->first()->difficulty_passed : 0;
                        //find 5 questions in the track that are not already fielded and higher difficulty if some difficulty already passed
                        $skill_questions = Question::inRandomOrder()->whereSkillId($skills_to_test[$n]->id)->where('difficulty_id','>', $difficulty_passed)
                        //->whereNotIn('id', $user->myQuestions()->lists('question_id'))
                        ->take(5)->get();
                        if (count($skill_questions)){
                            $questions = $skill_questions->merge($questions);
                        } else {
                            $skill_user = $skills_to_test[$n]->forcePass($user->id, $difficulty_passed+1, $tracks_to_test[$i]);
                        }
                        $n++;           
                    }
                    $i++;
                }
                if (!count($questions)) {
                    $questions = Question::inRandomOrder()->take(20)->get();
                }
            }

            foreach ($questions as $question){
                $question ? $question->assigned($user, $this) : null;
            }            
        }

        $new_questions = $this->uncompletedQuestions()->get();

if (!count($new_questions) && count($this->questions)) {
//        if (count($this->questions()->get()) <= $this->questions()->sum('question_answered')){
            $message = 'Test ended successfully';
            return $this->completeTest($message, $user);
        }
//        }
        // field unanswered questions
        $test_questions = count($new_questions)< 6 ? $new_questions : $new_questions->take(5);
        return response()->json(['message' => 'Request executed successfully', 'test'=>$this->id, 'questions'=>$test_questions, 'code'=>201]);
    }

    public function completeTest($message, $user){
        $attempts = $this->attempts($user->id);
        $attempts = $attempts ? $attempts->attempts : 0;
        $maxile = $user->calculateUserMaxile($this);
        $user->enrolclass($maxile);                             //enrol in class of maxile reached
        $user->game_level = $user->game_level + $this->questions()->sum('correct');  // add kudos
        $user->save();                                          //save maxile and game results
        $this->testee()->updateExistingPivot($user->id, ['test_completed'=>TRUE, 'completed_date'=>new DateTime('now'), 'result'=>$result = $this->markTest($user->id), 'attempts'=> $attempts + 1]); 
        return response()->json(['message'=>$message, 'test'=>$this->id, 'percentage'=>$result, 'score'=>$maxile, 'maxile'=> $maxile,'kudos'=>$user->game_level, 'code'=>206], 206);
    }
}