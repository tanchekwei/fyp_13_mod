<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $table = 'Rubric';
    protected $primaryKey = 'rubricId';
    const UPDATED_AT = null;
}
