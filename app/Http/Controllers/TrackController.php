<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Track;
use App\Http\Requests\CreateTrackRequest;
use App\Course;
use App\Http\Requests\UpdateRequest;
use Auth;

class TrackController extends Controller
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
        return $tracks = Track::with('skills')
        ->with('level')->with('field')->with('status')->with('houses')
        ->select('id','track','description','field_id', 'level_id','status_id')->get();        
    }

    public function create(){
        $user=Auth::user();
        $public_tracks = $user->is_admin ? Track::all()->select('id','track'):Track::whereStatusId(3)->select('id','track')->get();
        $my_tracks = $user->tracks()->select('id','track')->get();

        return response()->json(['levels'=> \App\Level::select('id','level','description')->get(), 'statuses'=>\App\Status::select('id','status','description')->get(),'fields'=>\App\Field::select('id','field','description')->get(), 'my_tracks'=>$my_tracks, 'public_tracks'=>$public_tracks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTrackRequest $request)
    {
        $user = Auth::user();
        $house_id = $request->house_id;
        $track = Track::firstOrCreate(['track'=>$request->track,'description'=>$request->description, 'level_id'=>$request->level_id, 'status_id'=>$request->status_id, 'field_id'=>$request->field_id, 'user_id'=>$user->id]);

        //connect skills as well
        $skills = Track::whereTrack($track->track)->first()->skills;
        for ($i=0; $i<sizeof($skills); $i++) {
            $track->skills()->attach($skills[$i],['skill_order'=>$skills[$i]->pivot->skill_order]);
        }

        $houses = \App\House::findorfail($house_id);
        if ($houses) {
           $houses->tracks()->syncWithoutDetaching($track->id,['track_order'=>$houses->maxTrack($houses->id)? $houses->maxTrack($houses->id)->track_order + 1:1]);
        }

        $new_track = Track::whereId($track->id)->with(['field','level','status','owner','skills'])->first();
        return response()->json(['message' => 'Track correctly added and attached.', 'track'=>$new_track,'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track)
    {
        if (!$track) {
            return response()->json(['message' => 'This track does not exist.', 'code'=>404], 404);
        }

        return response()->json(['message'=>'Track with skills and questions fetched.','tracks'=>$track,'skills'=>$track->skills,'code'=>201],201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Track $track)
    {
        $logon_user = Auth::user();
        if ($logon_user->id != $track->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update track','code'=>401], 401);     
        }
        $track->fill($request->all())->save();

        return response()->json(['message'=>'Track updated','track' => $track, 'code'=>201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $tracks)
    {
        $logon_user = Auth::user();
        if ($logon_user->id != $track->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to delete track','code'=>401], 401);   
        }  
        if(sizeof($tracks->skills) > 0 || sizeof($tracks->courses)>0 || sizeof($tracks->houses)>0)
        {
            return response()->json(['message'=>'There are skills or this track belongs to a course or class. Delink them first.'], 409);
        }
        $tracks->delete();
        return response()->json(['message'=>'Track has been deleted.'], 200);
    }
}