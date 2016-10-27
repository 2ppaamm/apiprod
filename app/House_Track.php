<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class House_Track extends Model
{
    use RecordLog;
    
    protected $table = 'house_track';
    protected $fillable = ['house_id','track_id', 'track_order', 'start_date','end_date'];
    protected $hidden = ['created_at', 'updated_at'];
}
