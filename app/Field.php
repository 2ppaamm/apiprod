<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{

    use RecordLog;
    
    protected $hidden = ['user_id', 'created_at', 'updated_at','pivot'];
    protected $fillable = ['field', 'description', 'image', 'status_id'];

    public function user() {                        //who created this track
        return $this->belongsTo(User::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function tracks(){
        return $this->hasMany(Track::class);
    }
}
