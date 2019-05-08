<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cohort extends Model
{
    protected $table = 'cohort';    

    public $timestamps = false;
    
    public $primaryKey = 'cohortId';
    
    public $incrementing = false;   
    
    protected $fillable = [
        'cohortId', 
        'startDate',
        'endDate',
        'Project1Session',
        'Project2Session'
    ];
}
