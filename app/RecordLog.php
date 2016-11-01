<?php

namespace App;
use ReflectionClass;
use Auth;

trait RecordLog{

    // activity
    protected static function bootRecordLog() {

        foreach (static::getModelEvents() as $event){
	        static::$event(function($model) use ($event){
	        	$model->RecordLog($event);
	        });
        }
    }

    public function recordLog($event){
        Log::create([
            'subject_id' =>$this->id,
            'subject_type'=>get_class($this),
            'name' => $this->getLogName($this,$event),
            'user_id' => Auth::user() ? Auth::user()->id:1
        ]);
    }

    protected function getLogName($model, $action) {
    	$name = strtolower((new ReflectionClass($model))->getShortName());
    	return "{$name}:{$model->description} was {$action}  scores:{$model->maxile_level} {$model->game_level}";
    }

    protected static function getModelEvents(){
    	if (isset(static::$recordEvents)) {
    		return static::$recordEvents;
    	}
    	return [
    		'created', 'deleted', 'updated'
    	];
    }

}	    