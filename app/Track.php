<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;
use App\SkillUser;

class Track extends Model
{
    use RecordLog;

    protected $hidden = ['created_at', 'updated_at','pivot'];
    protected $fillable = ['track', 'description', 'level_id', 'field_id',
        'image', 'status_id','user_id'];

    //relationship
    public function user() {                        //who created this track
        return $this->belongsTo(User::class);
    }

    public function users(){
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('track_maxile','track_test_date', 'track_passed');
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function field(){
        return $this->belongsTo(Field::class)->select('id','field','description');
    }

    public function questions(){
        return $this->hasManyThrough(Question::class, Skill::class);
    }

    public function postReqTracks(){
        return $this->belongsToMany(Track::class,'track_track','preReq_track_id', 'track_id');
    }

    public function preReqTracks(){
        return $this->belongsToMany(Track::class, 'track_track','track_id', 'preReq_track_id');
    }

    public function courses(){
        return $this->belongsToMany(Course::class)->withPivot(['track_order','number_of', 'unit_id'])->withTimestamps();
    }

    public function skills(){
        return $this->belongsToMany(Skill::class)->withPivot(['skill_order', 'start_date', 'end_date'])->withTimestamps();
    }

    public function checkedSkills(){
        return $this->belongsToMany(Skill::class)->whereCheck(TRUE)->withPivot(['skill_order', 'start_date', 'end_date'])->withTimestamps();
    }

    public function skillsdesc(){
        return $this->belongsToMany(Skill::class)->withPivot(['skill_order', 'start_date', 'end_date'])->withTimestamps()->select('id','skill', 'description','skill_order')->orderBy('skill_order','desc');
    }


    public function maxSkill($track){
        return $this->skillsdesc()->select('skill_order')->whereTrackId($track)->first();
    }

    public function houses(){
        return $this->belongsToMany(House::class)->withPivot(['track_order','start_date', 'end_date']);
    }

    public function level(){
        return $this->belongsTo(Level::class);//->select('id','level','description','start_maxile_level','end_maxile_level');
    }

    public function unit(){
        return $this->belongsToMany(Unit::class,'course_track')->withPivot(['track_order','number_of', 'course_id'])->select('unit');
    }

    public function track_maxile(){
        return $this->users()->whereUserId(Auth::user()->id)->select('track_maxile', 'track_test_date', 'track_passed');
    }

    public function allSkillsPassed($userid, $max_track_maxile){
        $skills = $this->skills;
        $allpassed = TRUE;
        $i = 0;
        while ($allpassed && $i < sizeof($skills)) {
            $allpassed = $skills[$i]->users()->whereUserId($userid)->select('skill_passed')->first()->skill_passed;
            $i++;
        }
        return $allpassed;
    }

    public function storeMaxile($user){
        $track_passed = (count($user->skill_user()->whereSkillPassed(TRUE)->get()) >= count($this->skills)) ? TRUE:FALSE;
        $track_maxile = !$track_passed ? SkillUser::whereUserId($user->id)->whereIn('skill_id', $this->skills()->pluck('id'))->sum('skill_maxile')/count($this->skills) : $this->level->end_maxile_level;
        $this->users()->sync([$user->id =>['track_id'=>$this->id,
            'track_test_date' => new DateTime('now'),
            'track_passed' => $track_passed,
            'track_maxile' => min($this->level->start_maxile_level, $track_maxile)]], false);
        return $track_maxile;        
    }

    public function calculateMaxile($user, $diagnostic){
        $track_maxile = $user->skill_user()->whereIn('skill_id', $this->skills()->pluck('id'))->avg('skill_maxile');
        $record = [
            'track_test_date' => new DateTime('now'),
            'track_passed' => $track_maxile < $this->level->end_maxile_level ? FALSE : TRUE,
            'track_maxile' => $track_maxile];

        $this->users()->updateExistingPivot($user->id, $record);
        return $track_maxile;
    }

    public function passTrack($user, $track_maxile){
        $record = [
            'track_test_date' => new DateTime('now'),
            'track_passed' => TRUE,
            'track_maxile' => $track_maxile];
        $this->users()->updateExistingPivot($user->id, $record);
    }

    public function failTrack($user, $track_maxile){
        $record = [
            'track_test_date' => new DateTime('now'),
            'track_passed' => FALSE,
            'track_maxile' => $track_maxile];
        $this->users()->updateExistingPivot($user->id, $record);
    }

    public function skillsFailed($user){
        return $this->skills->intersect($user->skill_user()->whereSkillPassed(FALSE)->get());
    }

    public function track_passed(){
        return $this->users()->whereUserId(Auth::user()->id)->whereTrackPassed(TRUE)->select('track_test_date', 'track_maxile');
    }

    public function owner(){
        return $this->user()->select('id','name');
    }
}