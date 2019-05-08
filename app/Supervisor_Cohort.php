<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supervisor_Cohort extends Model
{
    protected $table = 'supervisor_cohort';    

    public $timestamps = false;
    
    public $primaryKey = 'staffId';
    
    public $incrementing = false;   
    
    protected $fillable = [
        'cohortId',
        'staffId',
        'moderatorId'
    ];

}
