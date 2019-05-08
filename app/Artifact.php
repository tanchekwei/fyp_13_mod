<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artifact extends Model
{
    protected $table = 'Artifact';
    public $timestamps = false;
    protected $primaryKey = 'artifactId';
}
