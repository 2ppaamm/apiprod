<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Log;

class LogController extends Controller
{
    public function show(User $user) {
    	return $user->logs;
    }

    public function index() {
    	return Log::with(['user','subject'])->latest()->get();
    }
}
