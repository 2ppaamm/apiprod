<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\User;
use Auth;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth0.jwt');
        $currentuser =  \App\User::whereId(3)->first();
                \Auth::login($currentuser);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $users = User::with('roles','enrolledClasses','maxile','logs')->get();

//        return response()->json(['data'=>$users], 200);
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
        return User::create($user);
        return response()->json(['message' => 'User correctly added', 'data'=>$user, 'code'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        //check to see if user has role in the class
//        dd($users->hasClassRole('Teacher','4'));

        //user's roles in selected class
  //      return $houseRole = $users->houseRoles()->with(['userHouses'=>function($q) use ($house){
    //        $q->whereHouseId($house)->groupBy('house_id');
      //  }])->groupBy('id')->whereHouseId($house)->get();



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
        $users->delete();
        return response()->json(['message'=>'User has been deleted.'], 200);     
    }

    public function game_score(User $users, Request $request)
    {
        $user = Auth::user();
        $request->old_game_level == $user->game_level ? $user->game_level = $request->new_game_level : null;
        $user->save();
        return User::find($user->id);
    }
}