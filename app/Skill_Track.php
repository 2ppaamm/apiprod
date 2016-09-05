<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill_Track extends Model
{
    protected $fillable = ['track_id','skill_id','skill_order','start_date','end_date'];
    protected $table = "skill_track";
    protected $hidden = ['created_at', 'updated_at','pivot'];
 
}
	