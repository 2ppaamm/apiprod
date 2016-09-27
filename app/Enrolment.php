<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use RecordLog;

    protected $table = 'house_role_user';
    protected $fillable = ['mastercode','purchaser_id','payment_email','user_id','role_id', 'house_id','expiry_date', 'start_date', 'places_alloted'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['mastercode', 'payment_email', 'places_alloted'];

    public function users(){
    	return $this->belongsTo(User::class);
    }

    public function roles(){
    	return $this->belongsTo(User::class);
    }

    public function houses(){
    	return $this->belongsTo(User::class);
    }

    public function mastercodes(){
        return $this->hasOne(Mastercode::class);
    }

    public function inforce($user, $course){
        return $this->users()->whereUserId($user->id)->whereIn('house_id', $course->houses()->lists('id'))->where('expiry_date','>', new DateTime(now))->get();
    }
}
