<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use RecordLog;

    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['type', 'description'];

    //relationship
    public function questions(){
        return $this->hasMany('Question');
    }
}
