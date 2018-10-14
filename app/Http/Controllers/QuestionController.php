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
            return Question::with('solutions','author','difficulty', 'skill.tracks.level','skill.tracks.field','type','status')->simplePaginate(100);
        });
//        return $questions->items();
        return response()->json(['next'=>$questions->nextPageUrl(), 'previous'=>$questions->previousPageUrl(),'questions'=>$questions->items()], 200);
    }


    public function create(){
        return response()->json(['statuses'=>\App\Status::select('id','status','description')->get(), 'difficulties'=>\App\Difficulty::select('id','difficulty','description')->get(),'type'=>\App\Type::select('id','type','description')->get(),'skills'=>\App\Skill::select('id','skill','description')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuestionRequest $request)
    {
//        return Auth::user();
        $question = $request->all();
        Question::create($question);
        return response()->json(['message' => 'Question correctly added', 'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::find($id);
        if(!$question) {
            return response()->json(['message'=>'This question does not exist', 'code'=>404]);
        }

        return response()->json(['question' => $question, 'code'=>200], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Question $question)
    {
        if(Gate::denies('modify_question', $question)){
            return response()->json(['message'=> 'Access denied. You are not authorized to modify this question.', 'code'=>403],403);
        }

        $field = $request->get('field');
        $value = $request->get('value');
     
        $question->$field = $value;
        $question->save();

        return response()->json(['message'=>'Question fetched','question' => $question, 'code'=>200], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $questions)
    {
        $questions->delete();
        return response()->json(['message'=>'Question has been deleted.'], 200);
    }
}
