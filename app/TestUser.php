<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    use RecordLog;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'test_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['test_id','user_id', 'test_completed', 'completed_date','result','attempts'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    // make dates carbon so that carbon google that out
    protected $dates = ['last_quiz_date'];

}
