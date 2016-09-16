<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Http\Controllers\CourseTrackController;
use App\Http\Requests\CreateCourseRequest;

class CourseController extends Controller
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
        return $courses = Course::select('id','description')->get();
    }

    /**
     * Copy to a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(CreateCourseRequest $request, $id)
    {
        $course = Course::find($id)->replicate();
        if (!$course) {
            return response()->json(['message' => 'This course does not exist', 'code'=>404], 404);
        }
        $course->course = $request->input('course');
        $course->description = $request->input('description');
        $course->status_id = 1;
        $course->save();
        $tracks=Course::find($id)->tracks;
        for ($i=0; $i<sizeof($tracks); $i++) {
            $course->tracks()->attach($tracks[$i],['order'=>$tracks[$i]->pivot->order]);
        }
        $controller = new CourseTrackController;
        return $controller->index($course->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCourseRequest $request)
    {
        $values = $request->all();
//        Auth::user()->courses()->save($values);
        Course::create($values);
        return response()->json(['message'=>'Course is now added','code'=>201], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $course = Course::with(['tracks'=> function ($query){  
            $query -> with('unit')
                ->select('description','id','track','level_id')->with('level')
                ->with(['skills' => function ($query) {
                  $query->select('track_id','skill')->orderBy('track_order');}])
                ->orderBy('pivot_track_order'); 
            }])->find($id);

        if (!$course) {
            return response()->json(['message' => 'This course does not exist', 'code'=>404], 404);
        }
        return $course;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        if (!$course) {
            return response()->json(['message'=>'Course not found, cannot update.','code'=>404], 404);
        }
        $course[Request::get('name')] = Request::get('value');
        $course->update();
        return response()->json(['question' => $question, 200], 200);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        if (!$course) {
            return response()->json(['message'=>'Course not found, cannot delete.','code'=>404], 404);
        }
        if (sizeof($course->houses)>0){
            return response()->json(['message'=>'There are classes based this course. Delete those classes first.','code'=>409],409);
        }
        $course->delete();
        return response()->json(['message'=>'Course '.$course->name.' deleted','code'=>201], 201);
    }
}