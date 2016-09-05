<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use RecordLog;
    
    protected $hidden = ['user_id', 'created_at', 'updated_at'];
    protected $fillable = ['course', 'description', 'level_id','prereq_course_id','image', 'status_id','user_id'];

    public function houses(){
    	return $this->hasMany(House::class);
    }

    public function created_by() {                        //who created this track
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status(){
    	return $this->belongsTo(Status::class);
    }

    public function next_course(){
    	return $this->belongsTo(Course::class,'next_course_id');
    }

    public function previous_course(){
    	return $this->hasOne(Course::class, 'next_course_id');
    }

    public function level(){
    	return $this->belongsTo(Level::class);
    }

    public function tracks(){
    	return $this->belongsToMany(Track::class)->withPivot(['track_order','number_of', 'unit_id'])->withTimestamps()->orderBy('pivot_track_order');
    }

    public function unit(){
    	return $this->hasManyThrough(Unit::class,'course_track');
    }

    public function maxTrack($course){
        return $this->belongsToMany(Track::class)->withPivot('track_order','number_of', 'unit_id')->orderBy('pivot_track_order','desc')->select('track_order')->whereCourseId($course)->first();
    }

}
