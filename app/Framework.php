<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Framework extends Model
{
    protected $table = 'frameworks';

    protected $fillable = ['framework', 'description'];

    public function houses() {
    	return $this->hasMany(House::class);
    }
}
