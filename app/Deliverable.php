<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    protected $table = "Deliverable";
    public $timestamps = false;
    protected $primaryKey = 'deliverable_id';
}
