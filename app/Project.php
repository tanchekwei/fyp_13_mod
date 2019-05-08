<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';
    protected $primaryKey = 'projectCode';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'projectCode',
        'title',
        'description',
        'projectGroup',
        'cluster',
        'status',
        'level',
        'clientName',
        'advisor',
        'teamSize',
    ];
	
	// lau 
	public function repositories() {
        return $this->hasMany('App\Repository', 'projectCode');
    }

    public function teams(){
        return $this->hasMany('App\Team', 'projectCode', 'projectCode');
    }
}
