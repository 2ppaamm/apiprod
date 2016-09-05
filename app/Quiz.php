<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
	use RecordLog;

	protected $fillable = ['quiz'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at'];

    public function questions(){
        return $this->belongsToMany(Question::class)->withTimestamps()->withPivot(['answered','date_answered','correct']);
    }

    public function activities(){
    	return $this->morphMany(Activity::class, 'classwork');
    }

    public function results(){
        return $this->morphMany(Result::class, 'assessment');
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
