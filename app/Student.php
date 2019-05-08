<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use Notifiable;
    
    protected $guard = 'student';

    protected $table = 'student';    

    public $timestamps = false;
    
    public $primaryKey = 'studentId';
    
    public $incrementing = false;   
    
    protected $fillable = [
        'studentId', 
        'studentName',
        'phoneNo',
        'password',
        'TARCemail',
        'status',
        'tutorialGroup',
        'cohortId',
        'programmeId',
        'teamId',
    ];
	
	//lau 
	public function priv_key() { 
		return $this->hasOne('App\Ssh_Keys', 'userId', 'studentId');
   }
   
   public function team() { 
	return $this->hasOne('App\Team', 'teamId', 'teamId');
   }
}
