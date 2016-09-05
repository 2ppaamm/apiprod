<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class UserTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Please log on.', 'code'=>404], 404);
        }
        return $user->incompletetests;        
    }

    //
}
