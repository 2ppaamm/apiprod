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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTrackRequest $request)
    {
        $user = Auth::user();
        $track = $request->all();
        $user->tracks()->create($track);
        return response()->json(['message' => 'Track correctly added', 'track'=>$track,'code'=>201]);
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

        Auth::user()->tracks()->save($track);

        return response()->json(['message'=>'Track updated','track' => $track, 'code'=>200], 200);
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