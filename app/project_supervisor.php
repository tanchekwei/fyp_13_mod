<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class project_supervisor extends Model
{
    protected $table = 'project_supervisor';    

    public $timestamps = false;
    
    public $primaryKey = 'projectCode';
    
    public $incrementing = false;  
}
