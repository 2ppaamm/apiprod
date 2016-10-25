<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldUser extends Model
{
    use RecordLog;
    
    protected $table = 'field_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['field_id','user_id', 'field_maxile', 'field_test_date'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    // make dates carbon so that carbon google that out
    protected $dates = [''];
}
