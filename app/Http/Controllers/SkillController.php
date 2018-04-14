<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Skill;
use App\Http\Requests\UpdateRequest;
use App\Http\Requests\CreateSkillRequest;
use Auth;

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

    public function create(){
        $user=Auth::user();
        $public_skills = $user->is_admin ? null: Skill::whereStatusId(3)->select('id','skill')->get();
        $my_skills = $user->is_admin? Skill::select('id','skill')->get():$user->skills()->select('id','skill')->get();

        return response()->json(['statuses'=>\App\Status::select('id','status','description')->get(), 'my_skills'=>$my_skills, 'public_skills'=>$public_skills]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSkillRequest $request)
    {
        $user = Auth::user();
        $track_id = $request->track_id;
        $skill = Skill::firstOrCreate(['skill'=>$request->skill,'description'=>$request->description, 'status_id'=>$request->status_id, 'user_id'=>$user->id]);

        $track = \App\Track::findorfail($track_id);
        if ($track) {
           $track->skills()->syncWithoutDetaching($skill->id,['skill_order'=>$track->maxSkill($track)? $houses->maxSkill($track)->skill_order + 1:1]);
        }

        $new_skill = Skill::whereId($skill->id)->with(['status','user'])->first();
        return response()->json(['message' => 'Skill correctly added and attached.', 'skill'=>$new_skill,'code'=>201]);
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
        $skills->$field = $value;
        $skills->save();

        return response()->json(['message'=>'Skill update.','skill' => $skills, 'code'=>200], 200);
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