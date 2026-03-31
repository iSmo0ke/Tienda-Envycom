<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $fillable = ['user_id', 'alias', 'receptor_name', 'phone', 'calle_numero', 'colonia', 'municipio_alcaldia', 'estado', 'codigo_postal', 'referencias', 'is_default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
