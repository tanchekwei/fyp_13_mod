<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'Team';
    protected $primaryKey = 'teamId';
    public $timestamps = false;
    public $incrementing = false;   
    protected $fillable = [
        'teamId',
        'supervisor',
        'moderator',
        'conprtitionName',
        'status',
        'projectCode',
    ];
	
	// lau
    public function students(){
        return $this->hasMany('App\Student', 'teamId' ,'teamId');
    }

    public function staffs(){
        //return $this->hasMany('App\Staff', 'staffId', 'supervisor');
		return $this->hasMany('App\Staff','staffId','staffId');
	}
}
