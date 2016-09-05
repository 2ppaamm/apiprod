<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track_Track extends Model
{

    protected $fillable = ['prereq_track_id','track_id'];
    protected $table = "track_track";
}
