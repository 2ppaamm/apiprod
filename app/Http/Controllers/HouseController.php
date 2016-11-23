<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\House;
use App\Course;
use App\Http\Requests\CreateHouseRequest;
use Auth;
use App\Http\Requests\UpdateRequest;

class HouseController extends Controller
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
        return $houses = House::select('id','description','start_date','end_date')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateHouseRequest $request)
    {
        $values = $request->all();
        $user = Auth::user();
        $house = $user->houses()->create($values);
        
        //enrol user to the house in house_role_user
        $house->enrolledusers()->attach($user, ['role_id'=>4]);

        //create tracks
        $tracks = Course::find($request->course_id)->tracks;
        for ($i=0; $i<sizeof($tracks); $i++) {
            $house->tracks()->attach($tracks[$i],['track_order'=>$tracks[$i]->pivot->track_order]);
        }
//        $controller = new DashboardController;

        return response()->json(['message'=>$house->house . ' is now added as a new class.','code'=>201, 'class'=>$this->index()], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(House $houses)
    {
        return response()->json(['message'=> 'Class is as displayed.', 'code'=>200, 'house'=>$houses],200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, House $houses)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $houses->update([$field=>$value]);
        return response()->json(['message'=>'Update successful', 'class'=>$house, 'code'=>201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(House $houses)
    {
return $houses->tracks;        
        if (count($houses->tracks) > 0 or count($houses->enrolledusers) > 0) return response()->json(['message'=>'There are tracks or users in the class, cannot delete', 'code'=>404], 404);
        else {
            try {$houses->delete();} 
            catch (\Exception $exception) { return response()->json(['message'=>'Class cannot be deleted', 'code'=>404], 404);}
        }
        return response()->json(['message'=>'Class deleted successfully', 'code'=>204], 204);
    }
}
