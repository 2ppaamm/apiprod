<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use RecordLog;
    	
	protected $table = "permissions";
    
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['permission', 'description'];


    public function roles(){
    	return $this->belongsToMany(Role::class);
    }
}
