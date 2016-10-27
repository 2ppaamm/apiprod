<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use RecordLog;
    
    public function tracks(){
        return $this->hasMany(Track::class);
    }

    public function houses(){
    	return $this->hasMany(House::class);
    }
}
