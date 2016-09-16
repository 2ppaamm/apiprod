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
use App\Course;

class DiagnosticController extends Controller
{
    public function __construct(){
        $this->middleware('auth0.jwt');
\Auth::login(User::find(3));
    }

    /**
     * Sends a list of questions of the test number to the front end
     *
     * One question from the highest skill of each track from the appropriate level
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        return $request->mastercode;
        $courses = Course::where('course', 'LIKE', '%K to 6 Math%')->lists('id');
        $user = Auth::user();
        $enrolled = $user->validEnrolment($courses);
        //first time user error
        if (!$user->date_of_birth || !count($enrolled)) return response()->json(['message'=>'First time user', 'code'=>203]);
        $test = count($user->currenttest)<1 ? !count($user->completedtests) ? 
            $user->tests()->create(['test'=>$user->name."'s test",'description'=> $user->name."'s diagnostic test", 'diagnostic'=>TRUE]):
            $user->tests()->create(['test'=>$user->name."'s test",'description'=> $user->name."'s Daily Test".count($user->completedtests)+1, 'diagnostic'=>FALSE]):
            $user->currenttest[0];

        return $test->fieldQuestions($user);                // output
    }

    /**
     * Checks answers and then sends a new set of questions, according to correctness of 
     * questions.  Checks the following
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
            // calculate and saves maxile at 3 levels: skill, track and user
            $skill_maxile = $question->skill->handleAnswer($user->id, $question->difficulty_id, $correctness, $track, $test->diagnostic);
            $track_maxile = $track->calculateMaxile($user, $test->diagnostic);
            //return count($test->uncompletedQuestions);
            $user_maxile = $user->calculateUserMaxile($test);             
        }
        return $test->fieldQuestions($user, $test);
    }
    /**
     * Enrolls a student 
     *
     * @return \Illuminate\Http\Response
     */
    public function mastercodeEnrol($request){
        return $request->all();
    }
}