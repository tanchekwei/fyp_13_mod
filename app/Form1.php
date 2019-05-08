<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form1 extends Model
{
    protected $table = 'Form1';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'studentId';
}
