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

class HouseUserController extends Controller
{
    public function __construct(){
 //       $this->middleware('cors');
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
