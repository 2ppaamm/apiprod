<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission_Role extends Model
{
    use RecordLog;
    	
    protected $fillable = ['permission_id','role_id'];
    protected $table = "permission_role";
}
