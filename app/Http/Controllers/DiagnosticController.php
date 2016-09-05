<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Level;
use App\Question;
use Auth;
use App\Http\Requests\CreateQuizAnswersRequest;
use DateTime;
use App\User;
use Config;
use App\Error;

class DiagnosticController extends Controller
{
    public function __construct(){
 //       $this->middleware('cors');
        $this->middleware('auth0.jwt');
        $currentuser =  User::whereId(3)->first();
                \Auth::login($currentuser);
    }

    /**
     * Sends a list of questions of the test number to the front end
     *
     * One question from the highest skill of each track from the appropriate level
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
    	$user = Auth::user();
        //first time user error
        if (!$user->date_of_birth) return response()->json(['message'=>'First time user', 'code'=>203]);
            $test = count($user->currenttest)<1 ? !count($user->completedtests) ? 
            $user->tests()->create(['test'=>$user->name."'s test",'description'=> $user->name."'s diagnostic test", 'diagnostic'=>TRUE]):
            $user->tests()->create(['test'=>$user->name."'s test",'description'=> $user->name."'s Test ".count($user->completedtests)+1, 'diagnostic'=>FALSE]):
            $user->currenttest[0];
   	
        return $test->fieldQuestions($user);                // output
    }

    /**
     * Checks answers and then sends a new set of questions, according to correctness of 
     * questions.  Checks the following
     * 1. Correctness
     * 2. Answered
     * 3. Difficulty Cleared
     * 4. Skill Cleared
     * 5. Track Cleared
     * 6. Test Cleared
     *
     * @return \Illuminate\Http\Response
     */
    public function answer(CreateQuizAnswersRequest $request){
        $user = Auth::user();
        $old_maxile = $user->maxile_level;
        $test = \App\Test::find($request->test);
        if (!$test){
            return response()->json(['message' => 'Invalid Test Number', 'code'=>405], 405);    
        }

        foreach ($request->question_id as $key=>$question_id) {
            $answered = FALSE;
            $correctness = FALSE;
            $question = Question::find($question_id);
            if (!$question){
                $user->errorlogs()->create(['error'=>'Question '.$question_id.' not found']);
                return response()->json(['message'=>'Error in question. No such question', 'code'=>403]);                
            }
            $assigned = $question->users()->whereUserId($user->id)->first();
            if (!$assigned) {
                $user->errorlogs()->create(['error'=>'Question '.$question_id.' not assigned to '. $user->name]);
                return response()->json(['message'=>'Question '.$question_id.' not assigned to '. $user->name, 'code'=>403]);                                
            }
            if ($question->type_id == 2) {
                $answers = $request->answer[$key];
                $correct3 = sizeof($answers) > 3 ? $answers[3] == $question->answer3 ? TRUE : FALSE : TRUE;
                $correct2 = sizeof($answers) > 2 ? $answers[2] == $question->answer2 ? TRUE : FALSE : TRUE;
                $correct1 = sizeof($answers) > 1 ? $answers[1] == $question->answer1 ? TRUE : FALSE : TRUE;
                $correct = sizeof($answers) > 0 ? $answers[0] == $question->answer0 ? TRUE : FALSE : TRUE;
                $correctness = $correct + $correct1 + $correct2 + $correct3 > 3? TRUE: FALSE;
            } else $correctness = $question->correct_answer != $request->answer[$key] ? FALSE:TRUE;
            $answered = $question->answered($user, $correctness, $test);
            $track = $question->skill->tracks->intersect($user->testedTracks)->first();
            $skill_maxile = $question->skill->handleAnswer($user->id, $question->difficulty_id, $correctness, $track, $test->diagnostic);
            $track_maxile = $track->calculateMaxile($user, $test->diagnostic);
            //return count($test->uncompletedQuestions);
            $user_maxile = $user->calculateUserMaxile();             
        }
        return $test->fieldQuestions($user, $test);
    }
    	// Initialize output
/*    	$user = Auth::user();
    	$message =[];
    	$new_question_batch = [];

    	$test = \App\Test::find($request->test);

        //return $test->diagnostic ? $this->answerDiagnostic($request, $test) : null;

        foreach ($request->question_id as $key=>$question_id) {
            $correctness=FALSE;
            $answered = FALSE;
            $new_skill = null;
            $new_difficulty = null;

            $question = Question::find($question_id);
            if (!$question){
                $user->errorlogs()->create(['error'=>'Question ',$question_id.' not found']);
                return response()->json(['message'=>'Error in question. No such question', 'code'=>403]);                
            }

            return $track = $question->skill->tracks->intersect($user->testedTracks)->first(); // find track
            $level = Level::find($track->level_id);                                     // find level
            $max_track_maxile = 100/count($level->tracks);                              // Max maxile on this track
            $max_skill_maxile = $max_track_maxile/count($track->skills);                // Max maxile on this skill
            $maxile_earned = $question->difficulty_id*$max_skill_maxile/Config::get('app.difficulty_levels');                                                // Maxile based on this difficulty
            $correctness = $question->correct_answer != $request->answer[$key] ? FALSE:TRUE; // Answer correct?
            $answered = $question->answered($user, $correctness);                       // log as answered

            $skill_change = $correctness? $question->skill->answerRight($user->id, $question->difficulty_id, $correctness, $track, $max_skill_maxile, $maxile_earned) : $question->skill->answerWrong($user, $question, $track, $maxile_earned);      
            // Change skill if needed
            $current_skill_order =  $track->skills()->whereSkillId($question->skill->id)->select('skill_order')->first();
            if ($skill_change == 2) {
                $new_skill = $track->skills()->where('skill_order','>', $current_skill_order)->select('skill_order')->orderBy('skill_order', 'asc')->first();
                $new_difficulty = 1;
            }
            if ($skill_change == -1) {
                $new_skill = $track->skills()->where('skill_order','<', $current_skill_order)->select('skill_order')->orderBy('skill_order', 'desc')->first(); 
                $new_difficulty = Config::get('app.difficulty_levels');
            }
            if ($skill_change == 1) {
                $new_skill = $question->skill;
                $new_difficulty = $question->difficulty_id + 1;
            }
            $new_question = Question::whereSkillId($new_skill->id)->whereDifficultyId($new_difficulty)->inRandomOrder()->first();
            return $test->uncompletedQuestions;
            if (!count($test->questions) < Config::get('app.questions_per_test')) {
                $test->testee()->updateExistingPivot($user->id, ['test_completed'=>TRUE, 'completed_date'=>new DateTime('now'), 'attempts' =>$test->attempts($user->id) + 1]);                                            //update record
                $test = \App\Test::create(['test'=>$user->name." Test".count($user->completedtests)+1,'description'=> $user->name."Test".count($user->completedtests)+1, 'diagnostic'=>FALSE]);
            }
            return $new_question->assigned($user, $test);
        }
        $user->update(['next_skill_id' => $new_skill, 'next_difficulty_id'=>$new_difficulty]);
        return response()->json(['message' => 'More questions', 'test'=>$test, 'questions'=>$test_questions, 'code'=>201]);
    }

    /**
     * Create a new test for existing user after checking results
     *
     * @return \Illuminate\Http\Response
     */
    public function answerDiagnostic($request, $test){
        $user = Auth::user();
        $total_maxile = 0;
        $current_level = 0;
        foreach ($request->question_id as $key=>$question_id) {
            $correct=FALSE;
            $answered = FALSE;
            $question = Question::find($question_id);
            if (!$question){
                $user->errorlogs()->create(['error'=>'Question ',$question_id.' not found']);
                return response()->json(['message'=>'Error in question. No such question', 'code'=>403]);                
            }
            $track = $question->skill->tracks->intersect($user->testedTracks)->first();
            $current_level = Level::find($track->level_id);
            $base_maxile = $current_level->start_maxile_level;                  
            $correct = $question->correct_answer != $request->answer[$key] ? FALSE:TRUE; // correct?
            $answered = $question->answered($user, $correct);                       // log             
            $correct ? $track->passTrack($user, $current_level->end_maxile_level) : $track->failTrack($user, $base_maxile);
        }

        $total_maxile = $user->trackMaxile()->whereIn('track_id', $current_level->tracks()->lists('id'))->select('track_maxile')->get()->avg('track_maxile');

        $new_level = Level::whereStartMaxileLevel(round($total_maxile /100)*100)->first();
        foreach($questions = $new_level->untestedSkills($user) as $question) {
            $question->assigned($user, $test);
        }
        return $test->uncompletedQuestions;
        $test_questions = count($test->uncompletedQuestions)<1 ? $questions : $test->uncompletedQuestions; 
        //$test_questions = Question::where('id','>',215)->get();
        return response()->json(['message' => 'Request executed successfully', 'test'=>$test, 'questions'=>$test_questions, 'code'=>201]);
    }
    /**
     * Create a new test for existing user
     *
     * @return \Illuminate\Http\Response
     */
    public function createNewTest(Boolean $diagnostic){
        $test = $diagnostic ? $user->tests()->create(['test'=>$user->name." Test".count($user->completedtests)+1,'description'=> $user->name."Test".count($user->completedtests)+1, 'diagnostic'=>FALSE]):null;
    }


}