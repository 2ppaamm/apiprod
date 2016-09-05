<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use RecordLog;

    protected $table = 'house_role_user';
    protected $fillable = ['user_id','role_id', 'house_id'];

    public function users(){
    	return $this->belongsTo(User::class);
    }

    public function roles(){
    	return $this->belongsTo(User::class);
    }

    public function houses(){
    	return $this->belongsTo(User::class);
    }
}
