<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ssh_Keys extends Model
{
    protected $table = 'Ssh_Keys';
    public $timestamps = false;
    protected $primaryKey = 'userId';
}