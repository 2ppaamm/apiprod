<?php

namespace App;

trait HasRoles {
// Role Management

    public function roles(){
        return $this->belongsToMany(Role::class, 'house_role_user');
    }

    public function hasRole($role) {
        if (is_string($role)){
            return $this->roles->contains('name',$role);
        }
        return !! $role->intersect($this->roles)->count();
    }

    public function assignRole($role){
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    public function removeRole($role){
        return $this->roles()->detach(
            Role::whereName($role)->firstOrFail()
        );
    }

}