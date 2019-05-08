<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form2 extends Model
{
    protected $table = 'Form2';
    protected $primaryKey = 'studentId';
    public $incrementing = false;
    const UPDATED_AT = null;
}
