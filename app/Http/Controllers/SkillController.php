<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Skill;
use App\Track;
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
$user->is_admin=TRUE;
        $public_tracks = $user->is_admin ? null: Track::whereStatusId(3)->select('id','track')->get();
        $my_tracks = $user->is_admin? Track::select('id','track')->get():$user->tracks()->select('id','track')->get();

        return response()->json(['statuses'=>\App\Status::select('id','status','description')->get(), 'my_tracks'=>$my_tracks, 'public_tracks'=>$public_tracks]);
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
$user->is_admin=TRUE; //to be deleted in productions

        $track_id = $request->track_id;

        $skill = Skill::firstOrCreate(['skill'=>$request->skill,'description'=>$request->description, 'status_id'=>$request->status_id, 'user_id'=>$user->id]);
        if ($request->hasFile('lesson_link')) {
            $timestamp = time();
            $skill->lesson_link = 'videos/skills/'.$timestamp.'.mp4';

            $file = $request->lesson_link->move(public_path('videos/skills'), $timestamp.'.mp4');

            $skill->save();
        }

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
    public function update(Request $request, Skill $skill)
    {
        $logon_user = Auth::user();
$logon_user->is_admin = TRUE; //to be deleted for live, this makes everyone admin
        if ($logon_user->id != $skill->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update skill','code'=>401], 401);     
        }

        if ($request->hasFile('lesson_link')) {
            if (file_exists($skill->lesson_link)) unlink($skill->lesson_link);
            $timestamp = time();
            $skill->lesson_link = 'videos/skills/'.$timestamp.'.mp4';

            $file = $request->lesson_link->move(public_path('videos/skills'), $timestamp.'.mp4');
        } 

        $skill->fill($request->except('lesson_link'))->save();

        return response()->json(['message'=>'skill updated','skill' => $skill, 201], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Skill $skill)
    {
        return response()->json(['message'=>'Skill fetched.', 'skill'=>$skill, 'code'=>201],201);
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

    public function usersPassed(Skill $skill) {
        return $skill;
    }
}