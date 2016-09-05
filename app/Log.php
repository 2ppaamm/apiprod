<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $hidden = ['user_id', 'created_at'];
    protected $fillable = ['user_id','subject_id','subject_type','name', 'user_id'];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function subject(){
    	return $this->morphTo();
    }
}
