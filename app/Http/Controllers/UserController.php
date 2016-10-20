<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\User;
use Auth;
use App\Http\Requests\GameScoreRequest;

class UserController extends Controller
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
        $user = Auth::user();
        $user->is_admin;
        return $user->is_admin ? $users = User::with('enrolledClasses.roles','logs')->get() : response()->json(['message' =>'not authorized to view users', 'code'=>401], 401);

//        return response()->json(['data'=>$users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
/*    public function store(CreateUserRequest $request)
    {
        $user = $request->all();
        return User::create($user);
        return response()->json(['message' => 'User correctly added', 'data'=>$user, 'code'=>201]);
    }
*/
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        $logon_user = Auth::user();
        if ($logon_user->id != $users->id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to view user','code'=>401], 401);     
        }
        $users = User::profile($users->id);
        $users['highest_scores'] = $users->highest_scores();
        return $users;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $users)
    {
        $logon_user = Auth::user();
        if ($logon_user->id != $users->id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update user', 'code'=>401], 401);     
        }
        if ($request->email) {
            return response()->json(['message' => 'You cannot change the email address of an account', 'data'=>$users, 'code'=>500], 500);
        } 
        $users->fill($request->all())->save();
        return $users;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
  .   * @return \Illuminate\Http\Response
     */
    public function destroy(User $users)
    {
        $logon_user = Auth::user();
        if (!$logon_user->is_admin){
            return response()->json(['message' => 'You have no access rights to delete user', 'data'=>$user, 'code'=>500], 500);
        }
        $users->delete();
        return response()->json(['message'=>'User has been deleted.'], 200);     
    }

    public function game_score(GameScoreRequest $request)
    {
        $user = Auth::user();
        if ($request->old_game_level != $user->game_level){
            return response()->json(['message'=>'Old game score is incorrect. Cannot update new score', 'code'=>500], 500);
        }
        $user->game_level = $request->new_game_level;
        $user->save();
        return User::profile($user->id);
    }
}