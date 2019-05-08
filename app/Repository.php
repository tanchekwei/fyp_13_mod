<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
	public $timestamps = false;
	
    //
    public function collaborators() {
        return $this->hasMany('App\Collaborator');
    }

    public function staff() {
        return $this->hasOne('App\Staff', 'staffId', 'created_by'); // 
    }

    public function project() {
        return $this->hasOne('App\Project', 'projectCode', 'projectCode');
}

}
