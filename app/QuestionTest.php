<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionTest extends Model
{
    protected $table = 'question_test';

    protected $fillable = ['question_id','test_id', 'answered', 'date_answered','correct'];
}
