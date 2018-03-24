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
//        \Auth::login(User::find(2));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return $user->is_admin ? response()->json(User::with('enrolledClasses.roles','logs')->get()): response()->json(['message' =>'not authorized to view users', 'code'=>401], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $user = $request->all();
        $user['password'] = bcrypt($request->password);
        User::create($user);
        return response()->json(['message' => 'User correctly added', 'data'=>$user, 'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::findorfail($id);
        $logon_user = Auth::user();
        if ($logon_user->id != $users->id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to view user','code'=>401], 401);     
        }
        return $users;
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
        $logon_user = Auth::user();
        $users = User::findorfail($id);
        
        if ($logon_user->id != $users->id && !$logon_user->is_admin) {            
            return response()->json(['message' => 'You have no access rights to update user.', 'code'=>401], 401);     
        }
        if ($request->email || $request->maxile_level || $request->game_level) {
            if (!$logon_user->is_admin) {
                array_except($request,['email','maxile_level','game_level']);
            }
        }
        $users->fill($request->all())->save();
        $users->fill($request->all())->save();
        return response()->json(['message'=>'User successfully updated.', 'user'=>$users,'code'=>201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
  .   * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = findorfail($id);
        $logon_user = Auth::user();
        if (!$logon_user->is_admin){
            return response()->json(['message' => 'You have no access rights to delete user', 'data'=>$user, 'code'=>401], 500);
        }
        if (count($users->enrolledClasses)>0) return response()->json(['message'=>'User has existing classes and cannot be deleted.'], 400);
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