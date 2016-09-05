<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function assessment(){
        return $this->morphTo();
    }

    public function quiz(){
        return $this->belongsTo(Test::class);
    }
}
