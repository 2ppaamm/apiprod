<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Difficulty extends Model
{
    use RecordLog;
    
    protected $hidden = ['user_id', 'created_at', 'updated_at'];
    protected $fillable = ['difficulty', 'description', 'short_description',
        'image', 'status'];

    //relationship
    public function user() {                        //who created this difficulty level
        return $this->belongsTo('User');
    }

    public function levels(){
        return $this->belongsToMany('Level');
    }
    public function status() {
        return $this->belongsTo('Status');
    }
}
