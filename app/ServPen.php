<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServPen extends Model
{
    protected $table = "serv_pen";
    protected $primaryKey = "id_serv_pen";
    public $timestamps = false;
    public function clientes(){
        return $this->belongsTo('App\Cliente', 'cliente','id_cli');
    }
}
