<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\House;
use App\Http\Requests\CreateTrackRequest;
use Auth;
use App\Http\Requests\UpdateRequest;
use App\Track;

class HouseTrackController extends Controller
{
    public function __construct(){
    }

	   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 
	public function index(House $house){
        return $house;
        $house = $house->tracks()-> with(['unit'=>function($query){$query->select('number_of','unit');} ])
                ->select('description','id','track','level_id')->with('level')
                ->with(['skills' => function ($query) {
                    $query->select('track_id','skill')->orderBy('skill_order');}])
                ->orderBy('pivot_track_order')->get();
        if (!$house) {
            return response()->json(['message' => 'This class does not exist', 'code'=>404], 404);
        }

        return response()->json(['message' => 'Class tracks listed', 'class'=>$house,'code'=>201], 201);
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTrackRequest $request, House $houses)
    {    
        $track = $request->all();
        $new_track = Auth::user()->tracks()->create($track);
        $houses->tracks()->attach($new_track->id,['track_order'=>$houses->maxTrack($houses->id)? $houses->maxTrack($houses->id)->track_order + 1:1]);
        return response()->json(['message' => 'Track correctly added', 'tracks'=>$houses->tracks,'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(House $houses, Track $tracks)
    {
        return $houses->tracks()->with('skills.questions')->whereTrackId($tracks->id)->get();   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, House $houses, Track $tracks)
    {
        $msg =  $houses->tracks()->updateExistingPivot($tracks->id, [$request->get('field')=>$request->get('value')]) ? $request->field." successfully Updated" : "The track does not belong to the class.";
        return response()->json(['message'=>$msg, 'house'=>$houses->tracks, 'code'=>201], 201);
    }

    /**
     * Remove the tracks from the class.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(House $house, Track $track)
    {
        try {
            $house->tracks()->detach($track);
            $tracks=$house->tracks()->with(['owner','skills.user','field','status','level'])->get();
        } catch(\Exception $exception){
            return response()->json(['message'=>'Unable to remove track from class', 'code'=>500], 500);
        }
        return response()->json(['message'=>'Track removed successfully','tracks'=>$tracks, 'code'=>201],201);
    }
}