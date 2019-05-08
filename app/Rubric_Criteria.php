<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rubric_Criteria extends Model
{
    protected $table = 'Rubric_Criteria';
    public $timestamps = false;
    protected $primaryKey = 'rubricCriteriaId';
}
