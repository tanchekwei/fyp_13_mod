<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use Notifiable;
    
    protected $guard = 'staff';

    protected $table = 'staff';    

    public $timestamps = false;
    
    public $primaryKey = 'staffId';
    
    public $incrementing = false;   
    
    protected $fillable = [
        'staffId', 
        'title',
        'staffName',
        'password',
        'phoneNo',
        'email',
        'status',
        'role',
        'full\part',
        'designation',
        'specialization',
        'departmentId'
    ];
	
	// lau
	//public function user()  {
    //    return $this->hasOne('App\User', 'id', 'user_id');
   // }
   
   public function priv_key() { 
		return $this->hasOne('App\Ssh_Keys', 'userId', 'staffId');
   }
}
