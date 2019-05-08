<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';    

    public $timestamps = false;
    
    public $primaryKey = 'departmentId';
    
    public $incrementing = false;  
}
