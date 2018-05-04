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
       $this->middleware('cors');
   }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function leaders()
    {
        return response()->json(['message' => 'Leader request executed successfully', 
            'game_leaders'=>User::gameleader(), 
            'maxile_leaders'=>User::maxileleader()], 201);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $user = Auth::user();
/*        $age = $user->date_of_birth ? date_diff(date_create($user->date_of_birth), date_create('today'))->y : 0;
        $user['highest_scores'] = $user->highest_scores();
//        $test = new \App\Test;
        $logs = $user->logs;

        $statuses = \App\Status::select('id','status','description')->get();
        $roles = \App\Role::select('id','role')->get();
        $difficulties = \App\Difficulty::select('id', 'difficulty', 'description')->get();
        $courses = \App\Course::with('created_by','houses', 'tracks.skills')->get();
        $houses = House::with('created_by','tracks.skills','course','privacy','teachers')->get();

        $dashboard = User::profile($user->id);  // user dashboard info
        // user teaching info

        $classInfo = $user->teachingHouses()->with('houses.studentEnrolment.users.completedtests','houses.studentEnrolment.users.getfieldmaxile')->with('houses.tracks.skills')->get();
        foreach ($classInfo as $class) {
            $class['average_progress']=$class->houses->studentEnrolment()->avg('progress');
            $class['lowest_progress'] = $class->houses->studentEnrolment()->min('progress');
            $class['highest_progress'] = $class->houses->studentEnrolment()->max('progress');
            $class['students_completed_course'] = $class->houses->studentEnrolment()->where('expiry_date','<', new DateTime('today'))->count();
            $class['chartdata']=['total_students'=>$class->houses->studentEnrolment()->count(),'underperform'=>$class->houses->studentEnrolment()->where('progress','<', 40)->count(),'on_target'=>$class->houses->studentEnrolment()->where('progress','>=', 40)->where('progress', '<',80)->whereRoleId(6)->count(), 'excel'=>$class->houses->studentEnrolment()->where('progress','>=', 80)->count()];         
            $class['total_students'] = $class->houses->studentEnrolment()->count();
            $class['underperform'] = $class->houses->studentEnrolment()->where('progress','<', 40)->count();
            $class['on_target'] = $class->houses->studentEnrolment()->where('progress','>=', 40)->where('progress', '<',80)->whereRoleId(6)->count();
            $class['excel'] = $class->houses->studentEnrolment()->where('progress','>=', 80)->count();
        }
*/
//return $user->completedtests;
//return $user->teachingHouses()->with('studentEnrolment.users.tests')->get();

        return response()->json(['message' => 'Request executed successfully', 
//            'results'=>['correctness'=>$user->accuracy(),'tracks_passed'=>count($user->tracksPassed).'/'.count(Track::all()), 
//                    'skills_passed'=>count($user->skill_user()->where('difficulty_passed','>',2)->get()).'/'.count(\App\Skill::all())],
            'user'=>$user,
//            'teach_info' => $classInfo,
            //'houses'=>$houses,
//            'courses'=>$courses, 'statuses'=>$statuses,'roles'=>$roles, 'difficulties'=>$difficulties,
//            'logs'=>$logs,
//            'my_questions'=> $user->myquestions()->with('skill')->select('correct','skill_id', 'attempts', 'question', 'question_image', 'answer0', 'answer0_image', 'answer1', 'answer1_image', 'answer2', 'answer2_image', 'answer3', 'answer3_image', 'type_id')->get(),
             'code'=>201]);
	}
}                                                                    