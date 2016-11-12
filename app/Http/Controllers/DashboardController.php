<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
use App\House;
use DateTime;
use App\Track;

class DashboardController extends Controller
{
    public function __construct(){
 //       $this->middleware('cors');
        $this->middleware('auth0.jwt');
//        $currentuser =  User::whereId(4)->first();
 //               \Auth::login($currentuser);
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
        $courses = \App\Course::with('created_by','houses', 'tracks.skills')->get();
        $houses = House::with('created_by','tracks.skills','course','privacy')->get();

        $dashboard = User::profile($user->id);
return        $classInfo = \App\Enrolment::whereIn('house_id',$user->teachingHouses()->lists('house_id'))->whereRoleId(6)->select(DB::raw('AVG(progress) AS average_progress'))->get() ;

        
        return response()->json(['message' => 'Request executed successfully', 
            'user'=>$dashboard, 'game_leaders'=>User::gameleader(), 
            'maxile_leaders'=>User::maxileleader(),'houses'=>$houses, 
            'courses'=>$courses, 'statuses'=>$statuses,'roles'=>$roles, 
            'logs'=>$logs, 'correctness'=>$user->accuracy(), 
            'tracks_passed'=>count($user->tracksPassed).'/'.count(Track::all()), 
            'skill_passed'=>count($user->skill_user()->where('difficulty_passed','>',2)->get()).'/'.count(\App\Skill::all()), 
            'my_results'=>$user->getfieldmaxile()->get(), 'code'=>201]);

	}
}