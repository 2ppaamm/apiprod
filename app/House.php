<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class House extends Model
{
    use RecordLog;
    
    protected $fillable = ['house', 'description', 'user_id','course_id','image', 'status_id', 'start_date', 'end_date'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    public function privacy(){
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function skills() {
    	return $this->belongsToMany(Skill::class)->withPivot('start_date','end_date');
    }

    public function course(){
    	return $this->belongsTo(Course::class);
    }

    public function tracks(){
    	return $this->belongsToMany(Track::class)->withPivot('track_order','start_date', 'end_date')->orderBy('track_order');
    }

    public function maxTrack($house){
        return $this->belongsToMany(Track::class)->withPivot('track_order','start_date', 'end_date')->orderBy('track_order','desc')->select('track_order')->whereHouseId($house)->first();
    }

    public function created_by(){
        return $this->belongsTo(User::class, 'user_id')->select('name','firstname','lastname', 'email', 'image', 'id');
    }

    public function enrolledUsers(){    	
        return $this->belongsToMany(User::class, 'house_role_user')->withPivot('role_id','mastercode', 'progress', 'payment_email','purchaser_id')->with(['roles'=>function($query){
    			$query->select('role', 'role_id')->groupBy('role');
    	}])->groupBy('user_id')->withTimestamps();
    }

    public function enrolment(){
        return $this->hasMany(Enrolment::class);
    }

    public function studentEnrolment(){
        return $this->enrolment()->whereRoleId(Role::where('role', 'LIKE', '%Student')->pluck('id'));
    }

    public function enrolUser($role){
        return $this->enrolment()->create(['role_id'=>$role]);
    }

    public function unenrollUser($user, $role){
        return $this->enrolment()->whereRoleId($role)->whereUserId($user)->delete();
    }

    public function activities(){
        return $this->hasMany(Activity::class);
    }

    public function roles(){
    	return $this->belongsToMany(Role::class, 'house_role_user')->withPivot('user_id')->groupBy('role')->withTimestamps();
    }

    public function enrolledStudents(){
    	return $this->enrolledUsers()->whereIn('role_id',Role::where('role', 'LIKE', '%Student')->pluck('id'))->select('name', 'contact', 'email', 'maxile_level', 'game_level','date_of_birth', 'image', 'firstname','lastname');
    }


    public function teachers(){
        return $this->enrolledUsers()->where('house_role_user.role_id','=',Role::where('role', 'LIKE', '%Teacher')->pluck('id'))->select('name', 'contact', 'email', 'image', 'firstname','lastname');
    }

    public function asStudent(){
        return $this->enrolledStudents()->whereUserId(Auth::user()->id)->take(1);
    }

    public function tests(){
        return $this->belongsToMany(Test::class)->withTimestamps();
    }

    public function quizzes(){
        return $this->belongsToMany(Quiz::class)->Timestamps();
    }

    // roles and permissions
    public function userRoles(){
        return $this->belongsToMany(Role::class, 'house_role_user')->withPivot('user_id')->withTimestamps();
    }

    public function roleUsers(){
        return $this->belongsToMany(User::class, 'house_role_user')->withPivot('role_id')->withTimestamps();
    }

    public function progress(){
        return $this->tracks;
    }
}