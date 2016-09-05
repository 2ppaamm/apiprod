<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use RecordLog;
	
    protected $hidden = ['user_id', 'created_at', 'updated_at','question_id'];
    protected $fillable = ['user_id','question_id','user_id','solution'];

    //relationship
    public function user() {                        //who created this question
        return $this->belongsTo(User::class);
    }

    public function question(){
    	return $this->belongsTo(Question::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

}
