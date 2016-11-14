<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class EnrolmentController extends Controller
{
    public function __construct(){
        $this->middleware('auth0.jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Auth::user();
        return $user->is_admin ? $users = \App\Enrolment::with('houses','roles', 'users')->get() : response()->json(['message' =>'not authorized to view enrolment details', 'code'=>401], 401);

//        return response()->json(['data'=>$users], 200);
    }
}
