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
        ->with('level')->with('field')
        ->select('id','track','description','field_id', 'level_id')->get();        
    }

    public function create(){
        $user=Auth::user();
        $public_tracks = $user->is_admin ? Track::with('skills')
        ->with('level')->with('field')
        ->select('id','track')->get():Track::whereStatusId(3)->with('skills')
        ->with('level')->with('field')->select('id','track')->get();
        $my_tracks = $user->tracks;

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
//        $new_track = $user->tracks()->create($track);
        $houses = \App\House::findorfail($house_id);
        if ($houses) {
           $houses->tracks()->attach($track->id,['track_order'=>$houses->maxTrack($houses->id)? $houses->maxTrack($houses->id)->track_order + 1:1]);
        }
        return response()->json(['message' => 'Track correctly added and attached.', 'track'=>$track,'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $track = Track::find($id);
        if (!$track) {
            return response()->json(['message' => 'This track does not exist.', 'code'=>404], 404);
        }

        return Track::whereId($id)->with('skills.questions')->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Track $tracks)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        
        try {
            $tracks->$field = $value;
        }
        catch(\Exception $exception){
            return response()->json(['message'=>'Update of track failed!','code'=> $exception->getCode()]);
        }

        Auth::user()->tracks()->save($tracks);

        return response()->json(['message'=>'Track updated','track' => $tracks, 'code'=>200], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $tracks)
    {
        if(sizeof($tracks->skills) > 0 || sizeof($tracks->courses)>0 || sizeof($tracks->houses)>0)
        {
            return response()->json(['message'=>'There are skills or this track belongs to a course or class. Delink them first.'], 409);
        }
        $tracks->delete();
        return response()->json(['message'=>'Track has been deleted.'], 200);
    }
}