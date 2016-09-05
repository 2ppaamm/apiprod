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
        $user->trackResults;
        $age = date_diff(date_create($user->date_of_birth), date_create('today'))->y;
        $user['highest_scores'] = $user->highest_scores();
        $test = new \App\Test;
        $logs = $user->logs;
//        return User::userMaxile();
//        return $user->tests;
        // Generate 5 questions to start
        if (count($user->myQuestions)<5){
            $user['user_level'] = 'diagnostic';
            if (count($user->incorrectQuestions) < 5) {
                for ($i=0; $i<5-count($user->incorrectQuestions); $i++){
                    $user->myQuestions()->sync([rand(1,36)],false);
                }
            }
        }

        $percentCorrect = count($user->myQuestions)>0 ? intval((count($user->myquestions)-count($user->incorrectQuestions))/count($user->myQuestions)*100):0;
        $statuses = \App\Status::select('id','status','description')->get();
        $roles = \App\Role::select('id','role')->get();
        $courses = \App\Course::with('created_by','houses', 'tracks.skills')->get();
        $houses = House::with('created_by','tracks.skills','course','privacy')->get();
        $dashboard = User::profile($user->id);

//        return House::userTracksResults();
        return response()->json(['message' => 'Request executed successfully', 'user'=>$dashboard, 'houses'=>$houses, 'courses'=>$courses, 'statuses'=>$statuses,'roles'=>$roles, 'logs'=>$logs, 'correctness'=>$percentCorrect,'code'=>201]);

//        return response()->json($user, $);

/*        $dashboard = User::whereId($user->id)->with(['enrolledClasses.activities.classwork','enrolledClasses.tracks.skills.questions','classRoles','courses','logs.subject'])->get();
*/
	}
}