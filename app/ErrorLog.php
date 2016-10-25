<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'errorlogs';

    protected $fillable = ['user_id','error'];

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
