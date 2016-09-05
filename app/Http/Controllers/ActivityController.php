<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Activity;

class ActivityController extends Controller
{
    public function show(User $user) {
    	return $user->activity;
    }

    public function index() {
    	return Activity::with(['user','subject'])->latest()->get();
    }
}
