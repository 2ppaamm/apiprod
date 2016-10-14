<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\House;
use App\User;
use App\Http\Requests\CreateEnrolmentRequest;
use Auth;
use App\Enrolment;
use DateTime;
use App\Role;

class HouseUserController extends Controller
{
    public function __construct(){
        $this->middleware('auth0.jwt');
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $house = House::whereId($id)->with('enrolledUsers.roles')->get();
        if (!$house) {
            return response()->json(['message' => 'This class does not exist', 'code'=>404], 404);
        }
        return $house;        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEnrolmentRequest $request, House $houses)
    {   
        //check if it is student self-enrolling, then check for mastercode. If are places left then update. If no more places, give a warning.
        if ($request->mastercode) {
            return $this->selfEnrol($request->mastercode, $houses);  // self-enrol with mastercode
        }
        $user = Auth::user();
        $date = new DateTime('now');
        $most_powerful = $user->enrolledClasses()->whereHouseId($houses->id)->with('roles')->min('role_id');
        $role_to_enrol = Role::where('role','LIKE',$request->role)->first();
        if (!$role_to_enrol) {
            return response()->json(['message'=>'Role does not exist.', 'code'=>404], 404);
        }
        if ($most_powerful < $role_to_enrol->id || $user->is_admin) {        // administrator 
            $user_id = $request->user_id ? $request->user_id : $user->id; 

        }
        if (!$request->user_id) return $this->adminEnrol(Auth::user()->id, $houses, $request->role); // admin self-enrol to another role
        else return $this->adminEnrol($request->user_id,$houses, $request->role); // admin enrolling others with no mastercode
    }

    /**
     *  Called by $this->store function.
     *
     * @param  \Illuminate\Http\Request  $mastercode, $houses
     * @return \Illuminate\Http\Response
     */
    public function selfEnrol($mastercode, House $houses) {
        $user = Auth::user();
        $check_mastercode = Enrolment::whereMastercode($mastercode)->first();
        if (!$check_mastercode) return response()->json(['message'=>'Your mastercode is wrong.', 'code'=>404], 404);
        $date = new DateTime('now');
        if ($check_mastercode->places_alloted) {
            $check_mastercode->places_alloted -= 1;
            $mastercode = $check_mastercode->places_alloted < 1 ? null : $mastercode;
            $check_mastercode->fill(['mastercode'=>$mastercode])->save();
            $enrolment = Enrolment::firstOrNew(['user_id'=>$user->id, 'house_id'=>$houses->id, 'role_id'=>6]);
            $enrolment->fill(['start_date'=>$date,'expiry_date'=>$date->modify('+1 year'), 'payment_email'=>$check_mastercode->payment_email, 'purchaser_id'=>$check_mastercode->user_id])->save();
            return response()->json(['message'=>'Your mastercode has been accepted and your enrolment is successful.', 'code'=>201], 201);
        }
        return response()->json(['message'=>'There is no more places left for this mastercode.', 'code'=>404], 404);
    }

    /**
     * Enrolment by admin or user enrolled in the class with super access: Principal, teacher, 
     * Department Head  - no mastercode required     
     *
     * @param  \Illuminate\Http\Request  $enrol_user, House, Role->role
     * @return \Illuminate\Http\Response
     */
    public function adminEnrol($enrol_user, House $houses, $role) {
        $user = Auth::user();
        $most_powerful = $user->enrolledClasses()->whereHouseId($houses->id)->with('roles')->min('role_id');
        $role_to_enrol = Role::where('role','LIKE',$role)->first();
        if (!$role_to_enrol) {
            return response()->json(['message'=>'Role does not exist.', 'code'=>404], 404);
        }
        $date = new DateTime('now');
        if ($most_powerful < $role_to_enrol->id || $user->is_admin) {        // administrator can do enrol anyone
            $enrolment = Enrolment::firstOrNew(['user_id'=>$enrol_user, 'role_id'=>$role_to_enrol->id, 'house_id'=>$houses->id]);
            $enrolment->fill(['start_date'=>$date, 'expiry_date'=>$date->modify('+1 year')])->save();
            return response()->json(['message'=>'Enrolment successful.', 'code'=>201], 201);
        } else {
            return response()->json(['message'=>'Not authorized to enrol.', 'code'=>401], 401);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     
    public function store(CreateEnrolmentRequest $request, $id)
    {    
        $outmsg=[];
        $errormsg=[];
        $roles = $request->roles;

        if ($request->get('user_id')){
            $user = User::find($request->get('user_id'));
            if (!$user) return response()->json(['message' => 'This user does not exist', 'code'=>404], 404);
        } else $user = Auth::user();
        $house = \App\House::find($id);
        if (!$house) return response()->json(['message' => 'This class does not exist', 'code'=>404], 404);
        foreach ($roles as $key => $role) {
            $role = \App\Role::find($role);
            //Try to enrol if user has the access rights. - not done yet
            try {
                //enrol user to the house in house_role_user
                Enrolment::create(['user_id'=>$user->id, 'role_id'=>$role->id, 'house_id'=>$id]);
                $house->enrolUser($role_id);
                array_push($outmsg, $user->name.' is successfully enrolled in '.$house->house.' as a '.$role->role);
            }
            catch(\Exception $exception) {
                array_push($errormsg, $user->name.' is already enrolled in '.$house->house.' as a '.$role->role);
            }
        }
        $controller = new DashboardController;
        return response()->json(['message'=>$outmsg, 'errormessage'=>$errormsg], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $users)
    {
        $house = \App\House::find($id);
        if (!$house) {
            return response()->json(['message' => 'This class does not exist', 'code'=>404], 404);
        }
        return $house->enrolledUsers()->get();
                
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateEnrolmentRequest $request, $id, $users)
    {
        $msg = "";
        $role = $request->get('roles');
        $house = \App\House::find($id);
//        return $house->enrolledUsers()->wherePivotRoleId($role)->get();//detach($users);
        if (!$house) {
            return response()->json(['message' => 'This class does not exist', 'code'=>404], 404);
        }
        try {
            //unenrol user to the house in house_role_user
            $house->unenrollUser($users, $role);
            $msg = 'Successfully unenrolled';
        }
        catch(\Exception $exception) {
            $msg = 'Unenrolment cannot be done.'.$exception;
        }
        return response()->json(['message'=>$msg, 'code'=>201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(House $houses, User $users)
    {
        try {
            $houses->enrolledUsers()->detach($users);
        } catch(\Exception $exception){
            return response()->json(['message'=>'Unable to remove user from class', 'code'=>500], 500);
        }
        return response()->json(['message'=>'User removed successfully', 'code'=>201],201);
    }
}
