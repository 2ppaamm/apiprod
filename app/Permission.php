<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use RecordLog;
    	
	protected $table = "permissions";
    public function roles(){
    	return $this->belongsToMany(Role::class);
    }
}
