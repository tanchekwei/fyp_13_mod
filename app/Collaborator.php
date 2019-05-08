<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
	public $timestamps = false;
    //
    public function student(){
        return $this->hasMany('App\Student', 'studentId', 'studentId');
    }
}
