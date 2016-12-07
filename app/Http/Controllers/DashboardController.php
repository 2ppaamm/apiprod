<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
use App\House;
use DateTime;
use App\Track;
use DB;

class DashboardController extends Controller
{
    public function __construct(){
 //       $this->middleware('cors');
 //       $this->middleware('auth0.jwt');
        \Auth::login(User::find(2));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $user = Auth::user();
        $age = date_diff(date_create($user->date_of_birth), date_create('today'))->y;
        $user['highest_scores'] = $user->highest_scores();
//        $test = new \App\Test;
        $logs = $user->logs;

        $statuses = \App\Status::select('id','status','description')->get();
        $roles = \App\Role::select('id','role')->get();
        $difficulties = \App\Difficulty::select('id', 'difficulty', 'description')->get();
        $courses = \App\Course::with('created_by','houses', 'tracks.skills')->get();
        $houses = House::with('created_by','tracks.skills','course','privacy')->get();

        $dashboard = User::profile($user->id);  // user dashboard info
        // user teaching info

        $classInfo = $user->teachingHouses()->with('studentEnrolment.users.completedtests','studentEnrolment.users.getfieldmaxile')->with('tracks.skills')->get();
        foreach ($classInfo as $class) {
            $class['average_progress']=$class->studentEnrolment()->avg('progress');
            $class['lowest_progress'] = $class->studentEnrolment()->min('progress');
            $class['highest_progress'] = $class->studentEnrolment()->max('progress');
            $class['students_completed_course'] = $class->studentEnrolment()->where('expiry_date','<', new DateTime('today'))->count();         
            $class['total_students'] = $class->studentEnrolment()->count();
            $class['underperform'] = $class->studentEnrolment()->where('progress','<', 40)->count();
            $class['on_target'] = $class->studentEnrolment()->where('progress','>=', 40)->where('progress', '<',80)->whereRoleId(6)->count();
            $class['excel'] = $class->studentEnrolment()->where('progress','>=', 80)->count();
        }

//return $user->completedtests;
//return $user->teachingHouses()->with('studentEnrolment.users.tests')->get();

        return response()->json(['message' => 'Request executed successfully', 
            'teach_info' => $classInfo,
            'user'=>$dashboard, 'game_leaders'=>User::gameleader(), 
            'maxile_leaders'=>User::maxileleader(),'houses'=>$houses,
            'courses'=>$courses, 'statuses'=>$statuses,'roles'=>$roles, 'difficulties'=>$difficulties,
            'logs'=>$logs, 'correctness'=>$user->accuracy(), 
            'tracks_passed'=>count($user->tracksPassed).'/'.count(Track::all()), 
            'skill_passed'=>count($user->skill_user()->where('difficulty_passed','>',2)->get()).'/'.count(\App\Skill::all()), 
            'my_results'=>$user->getfieldmaxile()->get(), 'code'=>201]);
	}
}