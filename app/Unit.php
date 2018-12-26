<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use RecordLog;
    
    protected $fillable = ['unit', 'description'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];
}
