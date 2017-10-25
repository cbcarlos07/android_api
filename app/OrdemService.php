<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdemService extends Model
{
    protected $table = "ordem_service";
    public $timestamps = false;
    protected $primaryKey = "id_os";
    protected $dates = ['data'];
}
