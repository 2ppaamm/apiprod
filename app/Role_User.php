<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role_User extends Model
{
	use RecordLog;

    protected $fillable = ['user_id','role_id'];
    protected $table = "role_user";
}
