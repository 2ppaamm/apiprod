<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Skill;
use App\Http\Requests\UpdateRequest;
use App\Http\Requests\CreateSkillRequest;

class SkillController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $skills = Skill::with('tracks')
        ->select('id','skill','description')->get();        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSkillRequest $request)
    {
        $skill = $request->all();
        $skill = Skill::create($skill);
        return response()->json(['message' => 'Skill correctly added', 'code'=>201,'skill'=>$this->show($skill->id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Skill $skills)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $skill->$field = $value;
        $skill->save();

        return response()->json(['message'=>'Skill update.','skill' => $skill, 'code'=>200], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Skill $skills)
    {
        return response()->json(['message'=>'Skill fetched.', 'skill'=>$skills, 'code'=>201],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skill $skills)
    {
        if(sizeof($skills->questions) > 0)
        {
            return response()->json(['message'=>'There are questions in this skill. Delete all questions first.'], 409);
        }
        $skills->delete();
        return response()->json(['message'=>'Skill has been deleted.'], 200);
    }
}