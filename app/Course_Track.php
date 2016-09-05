<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course_Track extends Model
{
   protected $fillable = ['course_id','track_id','track_order','unit_id','number_of'];
   protected $table = "course_track";
}
