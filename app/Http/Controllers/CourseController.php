<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Http\Controllers\CourseTrackController;
use App\Http\Requests\CreateCourseRequest;
use Auth;
use Config;

class CourseController extends Controller
{
    public function __construct(){
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $courses = Course::with('tracks.skills','houses.created_by')->get();
        return response()-> json(['message' => 'Request executed successfully', 'courses'=>Course::all()],200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function open()
    {
        return $courses = Course::with('tracks.skills','houses.created_by')->get();
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
    public function store(Request $request)
    {
        $user = Auth::user();
$user->is_admin=TRUE; //to be deleted in production        
        if (!$user->is_admin){
            return response()->json(['message'=>'Only administrators can create a new courses', 'code'=>403],403);
        }
        $values = $request->all();
        $values['user_id'] = $user->id;

        $course = Course::create($values);

        if ($request->hasFile('image')) {
            $file = $request->image->move(public_path('images/courses'), $course->id.'.png');            
        } 

        $course->image = 'images/courses/'.$course->id.'.png';
        $course->save();

        return response()->json(['message'=>'Course is now added','code'=>201, 'course' => $course], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
         $course = Course::with(['tracks'=> function ($query){  
            $query -> with('unit')
                ->select('description','id','track','level_id')->with('level')
                ->with(['skills' => function ($query) {
                  $query->select('track_id','skill')->orderBy('skill_order');}])
                ->orderBy('track_order'); 
            }])->find($course->id);

        if (!$course) {
            return response()->json(['message' => 'This course does not exist', 'code'=>404], 404);
        }
        return response()->json(['course'=>$course, 'code'=>201], 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $logon_user = Auth::user();
$logon_user->is_admin = TRUE; //to be deleted for live, this makes everyone admin
        if ($logon_user->id != $course->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update course','code'=>401], 401);     
        }

        if ($request->hasFile('image')) {
            unlink('images/courses/'.$course->id.'.png'); 
            $file = $request->image->move(public_path('images/courses'), $course->id.'.png');
        } 
        
        $course->fill($request->all())->save();
        return response()->json(['message'=>'Course updated','course' => $course, 201], 201);
    }

    /**
     * Upload course image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Course  $course
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, Course $course)
    {
return $course;
        $logon_user = Auth::user();
        if ($logon_user->id != $course->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update course image','code'=>401], 401);     
        }
        if ($request->hasFile('image')) {
            unlink('images/courses/'.$course->id.'.png'); 
            $file = $request->image->move(public_path('images/courses'), $course->id.'.png');
        } 
        
        return response()->json(['message'=>'Course Image updated','course' => $course, 201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $logon_user = Auth::user();
        if ($logon_user->id != $course->user_id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to delete course','code'=>401], 401);
        } 
        if (sizeof($course->houses)>0){
            return response()->json(['message'=>'There are classes based on this course. Delete those classes first.','code'=>500],500);
        }
        $course->delete();
        return response()->json(['message'=>'Course '.$course->name.' deleted','code'=>201], 201);
    }
}