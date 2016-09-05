<?php

namespace App;
use ReflectionClass;

trait ClassActivity{

    // activity
    protected static function bootClassActivity() {

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
            'user_id' => $this->user_id
        ]);
    }

    protected function getLogName($model, $action) {
    	$name = strtolower((new ReflectionClass($model))->getShortName());
    	return "{$action}_{$name}";
    }

    protected static function getModelEvents(){
    	if (isset(static::$recordEvents)) {
    		return static::$recordEvents;
    	}
    	return [
    		'created', 'deleted'
    	];
    }

}	    