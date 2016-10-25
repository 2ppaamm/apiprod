<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkillLink extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'skilllinks';

    protected $hidden = ['user_id', 'created_at', 'updated_at', 'skill_id'];
    protected $fillable = ['user_id','skill_id','link'];

    //relationship
    public function user() {                        //who created this question
        return $this->belongsTo(User::class);
    }

    public function skills(){
    	return $this->belongsTo(Skill::class);
    }

    public function status(){
    	return $this->hasOne(Status::class);
    }

}
