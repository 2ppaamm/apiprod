<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Question;
use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UpdateRequest;

class QuestionController extends Controller
{
    public function __construct(){
        $this->middleware('auth0.jwt');
   }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Cache::remember('questions', 15/60, function(){
            return Question::with('solutions','author','difficulty', 'skill.tracks.level','skill.tracks.field','type','status')->simplePaginate(20);
        });
//        return $questions->items();
        return response()->json(['next'=>$questions->nextPageUrl(), 'previous'=>$questions->previousPageUrl(),'questions'=>$questions->items()], 200);
    }


    public function create(){
        $levels=\App\Level::with(['tracks.skills'=>function($query){
                $query->select('id', 'skill','description');
                }])->select('id','level','description')->get();
        return response()->json(['statuses'=>\App\Status::select('id','status','description')->get(), 'difficulties'=>\App\Difficulty::select('id','difficulty','description')->get(),'type'=>\App\Type::select('id','type','description')->get(),'skills'=>$levels,'code'=>201],201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $question = $request->all();
        $question['user_id'] = $user->id;
        $question = Question::create($question);

        if ($request->hasFile('question_image')) {
            $file = $request->question_image->move(public_path('images\questions\question_image'), $question->id.'.png');            
            $question->image = 'images/questions/question_image'.$question->id.'.png';
        } 

        if ($request->hasFile('answer0_image')) {
            $file = $request->answer0_image->move(public_path('images/questions/answers'), $question->id.'.answer0.png');            
            $question->answer0_image = 'images/questions/answers'.$question->id.'.answer0.png';
        }

        if ($request->hasFile('answer1_image')) {
            $file = $request->answer1_image->move(public_path('images/questions/answers'), $question->id.'.answer1.png');            
            $question->answer1_image = 'images/questions/answers'.$question->id.'.answer1.png';
        }

        if ($request->hasFile('answer2_image')) {
            $file = $request->answer2_image->move(public_path('images/questions/answers'), $question->id.'.answer2.png');            
            $question->answer2_image = 'images/questions/answers'.$question->id.'.answer2.png';
        } 
        if ($request->hasFile('answer3_image')) {
            $file = $request->answer3_image->move(public_path('images/questions/answers'), $question->id.'.answer3.png');            
            $question->answer3_image = 'images/questions/answers'.$question->id.'.answer3.png';
        } 
        return response()->json(['message' => 'Question correctly added', 'question'=>$question, 'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        return response()->json(['question' => $question, 'code'=>201], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
//        if(Gate::denies('modify_question', $question)){
//            return response()->json(['message'=> 'Access denied. You are not authorized to modify this question.', 'code'=>403],403);
//        }
        $user = Auth::user();
        if ($question->user_id != $user->id && !$user->is_admin) {
            return response()->json(['message'=> 'Access denied. You are not authorized to modify this question.', 'code'=>403],403);
        }

        $question->fill($request->all())->save();

        if ($request->hasFile('question_image')) {

            if (file_exists('images\questions\question_image/'.$question->id.'.png')) unlink('images/questions/question_image/'.$question->id.'.png'); 
            $file = $request->question_image->move(public_path('images\questions\question_image/'), $question->id.'.png');

           
            $question->question_image = 'images/questions/question_image/'.$question->id.'.png';
        } else $question->question_image = null; 


        if ($request->hasFile('answer0_image')) {
            $file = $request->answer0_image->move(public_path('images/questions/answers'), $question->id.'.answer0.png');            

            if (file_exists('images\questions\answers'.$question->id.'answer0.png')) unlink('images/questions/answers'.$question->id.'.answer0.png'); 
            
            $question->answer0_image = 'images/questions/answers'.$question->id.'.answer0.png';
        } else $question->answer0_image = null;

        if ($request->hasFile('answer1_image')) {
            $file = $request->answer1_image->move(public_path('images/questions/answers'), $question->id.'.answer1.png');            
            if (file_exists('images\questions\answers'.$question->id.'.answer1.png')) unlink('images/questions/answers'.$question->id.'.answer1.png'); 
            $question->answer1_image = 'images/questions/answers'.$question->id.'.answer1.png';
        }  else $question->answer1_image = null;

        if ($request->hasFile('answer2_image')) {
            $file = $request->answer2_image->move(public_path('images/questions/answers'), $question->id.'.answer2.png');
            if (file_exists('images\questions\answers'.$question->id.'.answer2.png')) unlink('images/questions/answers'.$question->id.'.answer2.png'); 
            $question->answer2_image = 'images/questions/answers'.$question->id.'.answer2.png';
        }  else $question->answer2_image = null; 

        if ($request->hasFile('answer3_image')) {
            $file = $request->answer3_image->move(public_path('images/questions/answers'), $question->id.'.answer3.png');            
            if (file_exists('images\questions\answers'.$question->id.'.answer3.png')) unlink('images/questions/answers'.$question->id.'.answer3.png');            $question->answer3_image = 'images/questions/answers'.$question->id.'.answer3.png';
        }  else $question->answer3_image = null;

        return response()->json(['message'=>'Question updated','question' => $question, 'code'=>200], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
       if (sizeof($question->users)>0){
            return response()->json(['message'=>'This question has been answered by some users on the system. You cannot delete it.','code'=>500],500);
        }
        $question->delete();
        return response()->json(['message'=>'Question has been deleted.'], 200);
    }
}
