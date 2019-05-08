<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculty';    

    public $timestamps = false;
    
    public $primaryKey = 'facultyId';
    
    public $incrementing = false;   
    
    protected $fillable = [
        'facultyId', 
        'facultyName'
    ];
}
