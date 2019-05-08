<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $table = 'Criteria';
    public $timestamps = false;
    protected $primaryKey = 'criteriaId';
}
